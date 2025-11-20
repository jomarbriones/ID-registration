<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EnrolledStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EnrolledStudentController extends Controller
{
    /**
     * Return enrolled student count.
     */
    public function count()
    {
        return response()->json([
            'status' => 'success',
            'count' => EnrolledStudent::count(),
        ]);
    }

    /**
     * Accepts an .xlsx roster and upserts enrolled students.
     */
    public function import(Request $request)
    {
        $validated = $request->validate([
            'roster' => ['required', 'file', 'mimes:xlsx', 'max:10240'],
            'replace_existing' => ['nullable', 'boolean'],
        ]);

        $file = $validated['roster'];

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to read the uploaded spreadsheet. Please confirm it is a valid .xlsx file.',
            ], 422);
        }

        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        if (count($rows) < 2) {
            return response()->json([
                'status' => 'error',
                'message' => 'The uploaded file is empty. Add a header row and at least one student row.',
            ], 422);
        }

        $headerRow = array_shift($rows);
        $map = $this->buildColumnMap($headerRow);
        $required = ['id_number', 'first_name', 'last_name', 'middle_initial', 'course'];

        $missing = array_diff($required, array_keys($map));
        if (!empty($missing)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Missing required columns: ' . implode(', ', $missing)
                    . '. Expected headers include id number, first_name, last_name, middle_initial, course.',
            ], 422);
        }

        $imported = 0;
        $skipped = 0;
        $replace = $request->boolean('replace_existing');

        DB::transaction(function () use ($rows, $map, &$imported, &$skipped, $replace) {
            if ($replace) {
                EnrolledStudent::truncate();
            }

            foreach ($rows as $index => $row) {
                $rawId = trim((string) ($row[$map['id_number']] ?? ''));
                $number = preg_replace('/[^0-9]/', '', $rawId);

                if ($number === '') {
                    $skipped++;
                    continue;
                }

                $first = trim((string) ($row[$map['first_name']] ?? ''));
                $last = trim((string) ($row[$map['last_name']] ?? ''));
                $middle = trim((string) ($row[$map['middle_initial']] ?? ''));
                $course = trim((string) ($row[$map['course']] ?? ''));

                if ($first === '' || $last === '') {
                    $skipped++;
                    continue;
                }

                $full = trim($first . ' ' . ($middle ? $middle . '. ' : '') . $last);

                EnrolledStudent::updateOrCreate(
                    ['student_number' => $number],
                    [
                        'first_name' => $first,
                        'middle_initial' => $middle ?: null,
                        'last_name' => $last,
                        'course' => $course ?: null,
                        'full_name' => $full,
                    ]
                );

                $imported++;
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => $imported > 0
                ? "Imported {$imported} students" . ($skipped ? " ({$skipped} skipped)" : '')
                : 'No new students were imported.',
            'imported' => $imported,
            'skipped' => $skipped,
            'replaced' => $replace,
        ]);
    }

    /**
     * Map normalized headers to their column letters.
     *
     * @param array<string,string|null> $headerRow
     * @return array<string,string>
     */
    protected function buildColumnMap(array $headerRow): array
    {
        $map = [];
        foreach ($headerRow as $col => $heading) {
            $key = $this->normalizeHeader($heading);
            if ($key) {
                $map[$key] = $col;
            }
        }
        return $map;
    }

    protected function normalizeHeader(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '_', $value);

        return match ($value) {
            'id', 'id_number', 'student_number', 'student_no', 'student_id' => 'id_number',
            'first', 'first_name', 'firstname' => 'first_name',
            'last', 'last_name', 'lastname' => 'last_name',
            'middle', 'middle_initial', 'middleinitial', 'mi' => 'middle_initial',
            'course', 'program' => 'course',
            default => null,
        };
    }
}
