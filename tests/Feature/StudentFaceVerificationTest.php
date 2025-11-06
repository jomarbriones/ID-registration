<?php

namespace Tests\Feature;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentFaceVerificationTest extends TestCase
{
    use RefreshDatabase;

    private function fakeVector(float $seed, int $length = 128): array
    {
        return collect(range(0, $length - 1))
            ->map(fn ($i) => round($seed + ($i * 0.0001), 6))
            ->all();
    }

    private function asJson(array $vector): string
    {
        return json_encode($vector, JSON_THROW_ON_ERROR);
    }

    private function basePayload(array $overrides = []): array
    {
        return array_merge([
            'id_number'        => '123456789',
            'first_name'       => 'Jane',
            'middle_initial'   => 'A',
            'last_name'        => 'Doe',
            'course'           => 'BSIT',
            'blood_type'       => 'A+',
            'address'          => 'Sample Address',
            'guardian_name'    => 'Parent Doe',
            'parent_address'   => 'Parent Address',
            'guardian_contact' => '09123456789',
            'gender'           => 'F',
            'confirm'          => 'on',
        ], $overrides);
    }

    public function test_registration_persists_embedding_for_new_student(): void
    {
        Storage::fake('public');
        Config::set('face.similarity_threshold', 0.6);

        $vector = $this->fakeVector(0.12);

        $response = $this
            ->withSession(['portal_user' => ['id' => 1, 'role' => 'student']])
            ->post(route('students.submit'), array_merge(
                $this->basePayload(),
                [
                    'picture_path'   => UploadedFile::fake()->image('face.jpg', 400, 400),
                    'face_embedding' => $this->asJson($vector),
                ]
            ));

        $response->assertRedirect(route('register'));
        $response->assertSessionHas('success');

        $student = Student::firstOrFail();
        $this->assertSame('pending', $student->status);
        $this->assertEquals($vector, $student->face_embedding);
        $this->assertNull($student->face_last_similarity);

        $storedPath = $student->picture_path;
        $this->assertNotEmpty($storedPath);
        Storage::disk('public')->assertExists(str_replace('storage/', '', $storedPath));
    }

    public function test_registration_rejects_when_face_similarity_below_threshold(): void
    {
        Storage::fake('public');
        Config::set('face.similarity_threshold', 0.9);

        $existingVector = $this->fakeVector(0.5);
        $student = Student::factory()
            ->withEmbedding($existingVector)
            ->create([
                'id_number'    => '987654321',
                'picture_path' => 'storage/uploads/original.jpg',
                'status'       => 'approved',
            ]);

        Storage::disk('public')->put('uploads/original.jpg', 'original');

        $candidateVector = $this->fakeVector(-0.8);

        $response = $this
            ->withSession(['portal_user' => ['id' => 2, 'role' => 'student']])
            ->from(route('students.register'))
            ->post(route('students.submit'), array_merge(
                $this->basePayload([
                    'id_number' => '987654321',
                ]),
                [
                    'picture_path'   => UploadedFile::fake()->image('mismatch.jpg', 400, 400),
                    'face_embedding' => $this->asJson($candidateVector),
                ]
            ));

        $response->assertRedirect(route('students.register'));
        $response->assertSessionHasErrors('picture_path');

        $student->refresh();
        $this->assertEquals($existingVector, $student->face_embedding);
        $this->assertSame('approved', $student->status);

        // No new files should be stored on failure.
        $this->assertCount(1, Storage::disk('public')->allFiles());
        Storage::disk('public')->assertExists('uploads/original.jpg');
    }

    public function test_registration_updates_existing_student_when_face_matches(): void
    {
        Storage::fake('public');
        Config::set('face.similarity_threshold', 0.6);

        $existingVector = $this->fakeVector(0.33);
        $student = Student::factory()
            ->withEmbedding($existingVector)
            ->create([
                'id_number'    => '555444333',
                'picture_path' => 'storage/uploads/legacy.jpg',
                'status'       => 'approved',
            ]);

        Storage::disk('public')->put('uploads/legacy.jpg', 'legacy-photo');

        $newVector = $existingVector; // identical => similarity 1.0

        $response = $this
            ->withSession(['portal_user' => ['id' => 3, 'role' => 'student']])
            ->post(route('students.submit'), array_merge(
                $this->basePayload([
                    'id_number'  => '555444333',
                    'first_name' => 'Updated',
                ]),
                [
                    'picture_path'   => UploadedFile::fake()->image('updated.jpg', 450, 450),
                    'face_embedding' => $this->asJson($newVector),
                ]
            ));

        $response->assertRedirect(route('register'));
        $response->assertSessionHas('success');

        $student->refresh();
        $this->assertEquals($newVector, $student->face_embedding);
        $this->assertSame('pending', $student->status, 'Updating photo should reset status to pending review.');
        $this->assertNotEquals('storage/uploads/legacy.jpg', $student->picture_path);
        Storage::disk('public')->assertMissing('uploads/legacy.jpg');
        Storage::disk('public')->assertExists(str_replace('storage/', '', $student->picture_path));
        $this->assertGreaterThanOrEqual(0.99, $student->face_last_similarity);
    }
}

