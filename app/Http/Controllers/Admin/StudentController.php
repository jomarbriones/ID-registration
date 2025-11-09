<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentController extends Controller
{
    /**
     * Helper: builds a proper public URL for stored photos.
     */
    protected function photoUrl(?string $path): ?string
    {
        if (empty($path)) return asset('images/placeholder.png');

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        // If it's already a /storage path
        if (Str::startsWith($path, ['storage/', '/storage/'])) {
            return url(Str::start($path, '/'));
        }

        // If stored in public disk (uploads/...)
        if (Storage::disk('public')->exists($path)) {
            return Storage::url($path);
        }

        // Fallback for files directly in /public/
        if (file_exists(public_path($path))) {
            return asset($path);
        }

        return asset('images/placeholder.png');
    }

    /**
     * Normalize the embedding payload we get from the client.
     *
     * @return float[]|null
     */
    protected function parseEmbedding(?string $raw): ?array
    {
        if ($raw === null || $raw === '') {
            return null;
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return null;
        }

        $vector = [];
        foreach ($decoded as $value) {
            if (!is_numeric($value)) {
                return null;
            }
            $vector[] = round((float) $value, 8);
        }

        $expected = (int) config('face.embedding_length', 128);
        if ($expected > 0 && count($vector) > 0 && count($vector) !== $expected) {
            // Accept slightly longer vectors but make sure they are not truncated.
            if (count($vector) < max(16, (int) floor($expected * 0.75))) {
                return null;
            }
        }

        return $vector;
    }

    /**
     * Compute cosine similarity between two embeddings.
     */
    protected function cosineSimilarity(array $reference, array $candidate): ?float
    {
        $length = min(count($reference), count($candidate));
        if ($length === 0) {
            return null;
        }

        $dot = 0.0;
        $refNorm = 0.0;
        $candNorm = 0.0;

        for ($i = 0; $i < $length; $i++) {
            $a = (float) $reference[$i];
            $b = (float) $candidate[$i];
            $dot += $a * $b;
            $refNorm += $a * $a;
            $candNorm += $b * $b;
        }

        if ($refNorm <= 0 || $candNorm <= 0) {
            return null;
        }

        return $dot / (sqrt($refNorm) * sqrt($candNorm));
    }

    /**
     * AJAX: Returns rendered HTML rows for pending students.
     */
    public function pending()
    {
        $students = Student::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.partials._pending_rows', compact('students'))->render();
    }

    /**
     * AJAX: Returns rendered HTML rows for approved students.
     */
    public function approved()
    {
        $students = Student::where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.partials._approved_rows', compact('students'))->render();
    }

    /**
     * PREVIEW: Renders full HTML preview (front and back of ID)
     * Used by Admin Dashboard modal.
     */
    public function preview(Request $request)
    {
        $id = $request->query('id');

        // allow lookup by numeric id or student number
        $student = Student::where('id', $id)
            ->orWhere('id_number', $id)
            ->first();

        if (!$student) {
            if ($request->wantsJson() || $request->query('format') === 'json') {
                return response()->json(['status' => 'error', 'message' => 'No matching student found.'], 404);
            }

            return response('<div style="padding:12px;color:#b91c1c">No matching student found.</div>', 404);
        }

        $photoUrl = $this->photoUrl($student->picture_path ?? $student->photo_path ?? null);
        $cardView = view('admin.partials._id_preview', [
            'student' => $student,
            'photoUrl' => $photoUrl,
        ])->render();

        if ($request->wantsJson() || $request->query('format') === 'json') {
            $submittedAt = optional($student->created_at);

            $payload = [
                'status'  => 'success',
                'student' => [
                    'id'             => $student->id,
                    'id_number'      => $student->id_number,
                    'first_name'     => $student->first_name,
                    'middle_initial' => $student->middle_initial,
                    'last_name'      => $student->last_name,
                    'course'         => $student->course,
                    'gender'         => $student->gender,
                    'blood_type'     => $student->blood_type,
                    'address'        => $student->address,
                    'guardian_name'  => $student->guardian_name,
                    'parent_address' => $student->parent_address,
                    'guardian_contact' => $student->guardian_contact,
                    'status'         => $student->status,
                    'photo_url'      => $photoUrl,
                    'submitted_at'   => $submittedAt?->toIso8601String(),
                    'submitted_at_for_display' => $submittedAt ? $submittedAt->format('M d, Y \\a\\t h:i A') : null,
                ],
                'card_preview' => $cardView,
            ];

            return response()
                ->json($payload)
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        }

        $response = response($cardView);
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    /**
     * Handles new student registration (from /register form).
     */
    public function submit(StoreStudentRequest $request)
    {
        $data = $request->validated();
        $embedding = $this->parseEmbedding($request->input('face_embedding'));

        if (!$embedding) {
            return back()
                ->withErrors(['picture_path' => 'Face verification data was missing or invalid. Please re-upload your photo.'])
                ->withInput($request->except('picture_path', 'face_embedding'));
        }

        $existing = Student::where('id_number', $data['id_number'])->first();
        $threshold = (float) config('face.similarity_threshold', 0.58);
        $similarity = null;

        if ($existing && is_array($existing->face_embedding) && count($existing->face_embedding) > 0) {
            $similarity = $this->cosineSimilarity($existing->face_embedding, $embedding);
            if ($similarity === null || $similarity < $threshold) {
                return back()
                    ->withErrors(['picture_path' => 'Face does not match the existing student photo on file.'])
                    ->withInput($request->except('picture_path', 'face_embedding'));
            }
        }

        $storedPath = null;
        if ($request->hasFile('picture_path')) {
            $ext = strtolower($request->file('picture_path')->getClientOriginalExtension());
            $filename = 'id-' . Str::uuid() . '.' . $ext;
            $stored = $request->file('picture_path')->storeAs('uploads', $filename, 'public');
            $storedPath = 'storage/' . ltrim($stored, '/'); // e.g. storage/uploads/uuid.jpg
        }

        $payload = [
            'first_name'       => $data['first_name'],
            'middle_initial'   => $data['middle_initial'] ?? null,
            'last_name'        => $data['last_name'],
            'course'           => $data['course'] ?? null,
            'blood_type'       => $data['blood_type'] ?? null,
            'address'          => $data['address'] ?? null,
            'guardian_name'    => $data['guardian_name'] ?? null,
            'parent_address'   => $data['parent_address'] ?? null,
            'guardian_contact' => $data['guardian_contact'] ?? null,
            'gender'           => $data['gender'],
            'status'           => 'pending',
            'face_embedding'   => $embedding,
            'face_embedding_updated_at' => now(),
            'face_last_similarity' => $similarity,
        ];

        if ($storedPath) {
            $payload['picture_path'] = $storedPath;
        }

        if ($existing) {
            if ($storedPath && $existing->picture_path && $existing->picture_path !== $storedPath) {
                $diskPath = preg_replace('#^storage/#', '', $existing->picture_path);
                if ($diskPath && Storage::disk('public')->exists($diskPath)) {
                    Storage::disk('public')->delete($diskPath);
                }
            }
            $existing->fill($payload);
            $existing->save();
            $student = $existing;
        } else {
            $student = Student::create(array_merge([
                'id_number' => $data['id_number'],
            ], $payload));
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => $existing
                    ? 'Details updated. An admin will re-validate your submission shortly.'
                    : 'Registration submitted. An admin will validate your information shortly.',
                'id'      => $student->id,
                'similarity' => $similarity,
            ]);
        }

        $message = $existing
            ? 'Update received! Your ID registration details will be re-validated by the admin team and you will be notified once they approve it.'
            : 'Success! Your ID registration was submitted and will be validated by the admin team. We will notify you once it is approved.';

        $request->session()->forget('portal_user');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', $message . ' You have been signed out for security - please log in again later to track the review status.');

    }

    /**
     * Admin: replace the stored reference photo + embedding for a student.
     */
    public function refreshPhoto(Request $request)
    {
        $validated = $request->validate([
            'student_id'     => ['required', 'integer', 'exists:students,id'],
            'photo'          => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120', 'dimensions:ratio=1/1,min_width=300,min_height=300'],
            'face_embedding' => ['required', 'string'],
        ]);

        $embedding = $this->parseEmbedding($validated['face_embedding']);
        if (!$embedding) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Face embedding payload is invalid.',
            ], 422);
        }

        $student = Student::findOrFail($validated['student_id']);
        $previousEmbedding = is_array($student->face_embedding) ? $student->face_embedding : null;
        $similarity = $previousEmbedding ? $this->cosineSimilarity($previousEmbedding, $embedding) : null;

        $newPath = $student->picture_path;
        if ($request->hasFile('photo')) {
            $ext = strtolower($request->file('photo')->getClientOriginalExtension());
            $filename = 'id-ref-' . Str::uuid() . '.' . $ext;
            $stored = $request->file('photo')->storeAs('uploads', $filename, 'public');
            $newPath = 'storage/' . ltrim($stored, '/');
        }

        // Remove the old public file if it lives on the same disk.
        if (!empty($student->picture_path) && $student->picture_path !== $newPath) {
            $diskPath = preg_replace('#^storage/#', '', $student->picture_path);
            if ($diskPath && Storage::disk('public')->exists($diskPath)) {
                Storage::disk('public')->delete($diskPath);
            }
        }

        $student->picture_path = $newPath;
        $student->face_embedding = $embedding;
        $student->face_embedding_updated_at = now();
        $student->face_last_similarity = $similarity ?? 1.0;
        $student->status = 'pending';
        $student->save();

        return response()->json([
            'status'     => 'success',
            'message'    => 'Reference photo updated.',
            'similarity' => $similarity,
        ]);
    }

    /**
     * Approve student by ID or student number.
     */
    public function approve(Request $request)
    {
        $id_raw = trim((string)$request->input('id', ''));
        if ($id_raw === '') {
            return response()->json(['status' => 'error', 'message' => 'Missing ID'], 400);
        }

        $student = ctype_digit($id_raw)
            ? Student::find((int)$id_raw)
            : Student::where('id_number', $id_raw)->first();

        if (!$student) {
            return response()->json(['status' => 'error', 'message' => 'No matching student found'], 404);
        }

        $student->status = 'approved';
        $student->save();

        return response()->json(['status' => 'success', 'message' => 'Student approved successfully']);
    }

    /**
     * Decline student by ID or student number.
     */
    public function decline(Request $request)
    {
        $id_raw = trim((string)$request->input('id', ''));
        if ($id_raw === '') {
            return response()->json(['status' => 'error', 'message' => 'Missing ID'], 400);
        }

        $student = ctype_digit($id_raw)
            ? Student::find((int)$id_raw)
            : Student::where('id_number', $id_raw)->first();

        if (!$student) {
            return response()->json(['status' => 'error', 'message' => 'No matching student found'], 404);
        }

        $student->status = 'declined';
        $student->save();

        return response()->json(['status' => 'success', 'message' => 'Student declined successfully']);
    }

    /**
     * Optional: unified endpoint for approve/decline.
     */
    public function updateStatus(Request $request)
    {
        $id = (int)$request->post('id', 0);
        $action = $request->post('action', '');

        if (!$id || !in_array($action, ['approve', 'decline'], true)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid request'], 400);
        }

        $student = Student::find($id);
        if (!$student) {
            return response()->json(['status' => 'error', 'message' => 'Student not found'], 404);
        }

        $student->status = $action === 'approve' ? 'approved' : 'declined';
        $student->save();

        return response()->json(['status' => 'success', 'message' => 'Status updated']);
    }

    /**
     * Generate a print-ready PDF sheet containing multiple student ID fronts.
     * Usage: GET /admin/students/print?ids=1,2,3 (IDs or student numbers)
     * If no ids provided, prints all approved students.
     */
    public function print(Request $request)
    {
        if (!extension_loaded('gd') && !extension_loaded('imagick')) {
            return response(
                '<div style="font-family:system-ui,Segoe UI,Arial;padding:14px;max-width:680px">'
                .'<h3 style="margin:0 0 8px;color:#b91c1c">PDF rendering prerequisites missing</h3>'
                .'<p style="margin:0 0 6px">The PHP GD or Imagick extension is required to generate print-ready PDFs.</p>'
                .'<ol style="margin:8px 0 0 18px;line-height:1.5">'
                .'<li>Open <code>php.ini</code> (e.g., <code>C:\\xampp\\php\\php.ini</code>).</li>'
                .'<li>Uncomment <code>extension=gd</code> (remove leading semicolon) and ensure <code>extension=fileinfo</code> and <code>extension=mbstring</code> are enabled.</li>'
                .'<li>Restart Apache (XAMPP Control Panel) and retry.</li>'
                .'</ol>'
                .'</div>', 500
            );
        }

        $idsParam = trim((string)$request->query('ids', ''));
        $studentsQuery = Student::query();

        if ($idsParam !== '') {
            $keys = collect(explode(',', $idsParam))
                ->map(fn($v) => trim($v))
                ->filter();
            $studentsQuery->where(function($q) use ($keys) {
                foreach ($keys as $k) {
                    $q->orWhere('id', $k)->orWhere('id_number', $k);
                }
            });
        } else {
            $studentsQuery->where('status', 'approved');
        }

        $students = $studentsQuery->orderBy('last_name')->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'No students found to print.');
        }

        // Precompute resolved photo paths for dompdf (absolute)
        $items = $students->map(function($s){
            $raw = $s->picture_path ?? '';
            $abs = null;
            if ($raw) {
                if (Str::startsWith($raw, ['http://','https://'])) {
                    $abs = $raw; // dompdf can fetch if remote enabled
                } elseif (Str::startsWith($raw, ['storage/','/storage/'])) {
                    $abs = public_path(Str::start($raw, '/'));
                } elseif (Storage::disk('public')->exists($raw)) {
                    $abs = public_path('storage/'.ltrim($raw,'/'));
                } elseif (file_exists(public_path($raw))) {
                    $abs = public_path($raw);
                }
            }
            return [
                'model' => $s,
                'photo_abs' => $abs,
            ];
        });

        $pdf = Pdf::loadView('admin.print.cards', [
            'items' => $items,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('student-id-cards.pdf');
    }
}



