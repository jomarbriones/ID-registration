<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class FacultyController extends Controller
{
    /**
     * Render an HTML snippet of the faculty ID (front/back) for previews.
     */
    protected function renderCard(Faculty $faculty): string
    {
        return view('admin.partials._faculty_preview', [
            'faculty' => $faculty,
            'photoUrl' => $this->photoUrl($faculty->photo_path),
        ])->render();
    }

    /**
     * Return HTML table rows for the faculty list.
     */
    public function list()
    {
        $faculties = Faculty::orderBy('last_name')->orderBy('first_name')->get();
        return view('admin.partials._faculty_rows', compact('faculties'))->render();
    }

    /**
     * Normalize string inputs (trim / normalize casing).
     */
    protected function sanitize(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = trim($value);
            }
        }

        if (!empty($data['gender'])) {
            $data['gender'] = Str::upper($data['gender']);
        }

        return $data;
    }

    /**
     * Create or update a faculty entry.
     */
    public function store(Request $request)
    {
        // Treat empty ID as null so validation won't fail on empty strings from the form.
        if ($request->input('id') === '' || $request->input('id') === null) {
            $request->merge(['id' => null]);
        }

        $data = $request->validate([
            'id' => 'nullable|integer|exists:faculties,id',
            'id_number' => 'required|string|regex:/^\d+$/|max:32',
            'first_name' => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:8',
            'last_name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:12',
            'blood_type' => 'nullable|string|max:8',
            'address' => 'nullable|string|max:255',
            'emergency_contact_address' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_number' => 'nullable|regex:/^\d+$/|max:64',
            'civil_status' => 'nullable|string|max:50',
            'gsis_number' => 'nullable|regex:/^\d+$/|max:64',
            'sss_number' => 'nullable|regex:/^\d+$/|max:64',
            'tin_number' => 'nullable|regex:/^\d+$/|max:64',
            'birthday' => 'nullable|date',
            'id_path' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $data = $this->sanitize($data);

        // Build full name
        $nameParts = [
            $data['first_name'],
            $data['middle_initial'] ? $data['middle_initial'] : null,
            $data['last_name'],
        ];
        $data['name'] = trim(collect($nameParts)->filter()->join(' '));

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $ext = strtolower($request->file('photo')->getClientOriginalExtension());
            $filename = 'faculty-photo-' . Str::uuid() . '.' . $ext;
            $stored = $request->file('photo')->storeAs('uploads/faculty', $filename, 'public');
            $data['photo_path'] = 'storage/' . ltrim($stored, '/');
        }

        $faculty = isset($data['id'])
            ? tap(Faculty::findOrFail($data['id']))->update($data)
            : Faculty::create($data);

        $payload = [
            'status' => 'success',
            'faculty' => $this->formatFaculty($faculty),
            'card_preview' => $this->renderCard($faculty),
        ];

        return response()
            ->json($payload)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    /**
     * Fetch a single faculty entry + preview (for reloading previews from DB).
     */
    public function preview(Request $request)
    {
        $id = $request->query('id');
        if (!$id) {
            return response()->json(['status' => 'error', 'message' => 'Missing faculty id.'], 400);
        }

        $faculty = Faculty::find($id);
        if (!$faculty) {
            return response()->json(['status' => 'error', 'message' => 'Faculty record not found.'], 404);
        }

        $payload = [
            'status' => 'success',
            'faculty' => $this->formatFaculty($faculty),
            'card_preview' => $this->renderCard($faculty),
        ];

        return response()
            ->json($payload)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    /**
     * Print-ready PDF of a single faculty ID, centered on A4.
     */
    public function print(Request $request)
    {
        $idParam = $request->route('id') ?? $request->query('id') ?? $request->query('ids', '');
        $query = Faculty::query();

        if ($idParam !== '') {
            $keys = collect(is_array($idParam) ? $idParam : explode(',', (string)$idParam))
                ->map(fn($v) => trim((string)$v))
                ->filter();
            if ($keys->isEmpty()) {
                return response('<div style="font-family:system-ui,Segoe UI,Arial;padding:16px;color:#b91c1c">No faculty ids provided.</div>', 404);
            }
            $query->where(function($q) use ($keys) {
                foreach ($keys as $k) {
                    $q->orWhere('id', $k)->orWhere('id_number', $k);
                }
            });
        }

        $items = $query
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->orderBy('id_number')
            ->get();

        if ($items->isEmpty()) {
            return response('<div style="font-family:system-ui,Segoe UI,Arial;padding:16px;color:#b91c1c">No matching faculty records found.</div>', 404);
        }

        $pdf = Pdf::loadView('admin.print.faculty-card', [
            'items' => $items,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('faculty-id-cards.pdf');
    }

    /**
     * Delete a faculty record.
     */
    public function destroy(Request $request)
    {
        $id = $request->input('id');
        if(!$id){
            return response()->json(['status'=>'error','message'=>'Missing faculty id'], 400);
        }
        $faculty = Faculty::find($id);
        if(!$faculty){
            return response()->json(['status'=>'error','message'=>'Faculty not found'], 404);
        }
        $faculty->delete();
        return response()->json(['status'=>'success']);
    }

    /**
     * Shape the faculty payload for the UI.
     */
    protected function formatFaculty(Faculty $faculty): array
    {
        return [
            'id' => $faculty->id,
            'id_number' => $faculty->id_number,
            'name' => $faculty->name,
            'first_name' => $faculty->first_name,
            'middle_initial' => $faculty->middle_initial,
            'last_name' => $faculty->last_name,
            'position' => $faculty->position,
            'gender' => $faculty->gender,
            'blood_type' => $faculty->blood_type,
            'address' => $faculty->address,
            'emergency_contact_address' => $faculty->emergency_contact_address,
            'emergency_contact_name' => $faculty->emergency_contact_name,
            'emergency_contact_number' => $faculty->emergency_contact_number,
            'civil_status' => $faculty->civil_status,
            'gsis_number' => $faculty->gsis_number,
            'sss_number' => $faculty->sss_number,
            'tin_number' => $faculty->tin_number,
            'birthday' => optional($faculty->birthday)->toDateString(),
            'birthday_for_display' => optional($faculty->birthday)->format('M d, Y'),
            'photo_url' => $this->photoUrl($faculty->photo_path),
            'id_path' => $faculty->id_path,
        ];
    }

    /**
     * Resolve stored photo path to URL.
     */
    protected function photoUrl(?string $path): ?string
    {
        if (empty($path)) return asset('images/photo-placeholder.png');

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::startsWith($path, ['storage/', '/storage/'])) {
            return url(Str::start($path, '/'));
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::url($path);
        }

        if (file_exists(public_path($path))) {
            return asset($path);
        }

        return asset('images/photo-placeholder.png');
    }
}
