<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'id_number'        => (string) $this->faker->unique()->numerify('#########'),
            'first_name'       => $this->faker->firstName(),
            'middle_initial'   => Str::upper($this->faker->randomLetter()),
            'last_name'        => $this->faker->lastName(),
            'course'           => $this->faker->randomElement(['BSIT', 'BSA', 'BSN', 'BSCS']),
            'blood_type'       => $this->faker->randomElement(['A+', 'A-', 'B+', 'O+', null]),
            'address'          => $this->faker->address(),
            'guardian_name'    => $this->faker->name(),
            'parent_address'   => $this->faker->address(),
            'guardian_contact' => $this->faker->numerify('09#########'),
            'gender'           => $this->faker->randomElement(['M', 'F']),
            'picture_path'     => 'storage/uploads/' . Str::uuid() . '.jpg',
            'status'           => 'pending',
            'face_embedding'   => null,
            'face_embedding_updated_at' => null,
            'face_last_similarity' => null,
        ];
    }

    public function withEmbedding(array $embedding): self
    {
        return $this->state(fn () => [
            'face_embedding' => $embedding,
            'face_embedding_updated_at' => now(),
        ]);
    }
}

