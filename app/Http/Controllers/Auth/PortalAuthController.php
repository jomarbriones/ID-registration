<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EnrolledStudent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PortalAuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'identifier' => ['required','string'], // 9-digit student number or admin email
            'password'   => ['required','string'],
        ]);

        $identifier = trim($data['identifier']);
        $digits = preg_replace('/[^0-9]/', '', $identifier);
        if (ctype_digit($digits)) {
            $digits = substr($digits, 0, 9);
        }

        // Find by student number (with or without hyphen) or by email
        $user = User::query()
            ->where('student_number', $digits)
            ->orWhere('email', $identifier)
            ->orWhereRaw("REPLACE(student_number,'-','') = ?", [$digits])
            ->first();

        if (!$user) {
            return back()->withErrors(['identifier' => 'Invalid credentials.'])->withInput();
        }

        $valid = false;
        // Normal hashed password check
        if (Hash::check($data['password'], $user->password)) {
            $valid = true;
        } else {
            // If someone edited DB and stored plaintext, heal it on first login
            if (!Hash::isHashed($user->password) && hash_equals((string)$user->password, (string)$data['password'])) {
                $valid = true;
                $user->password = $data['password']; // 'hashed' cast will hash
                $user->save();
            }
        }

        if (!$valid) {
            return back()->withErrors(['identifier' => 'Invalid credentials.'])->withInput();
        }

        session([
            'portal_user' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role,
                'student_number' => $user->student_number,
            ]
        ]);

        // Optionally normalize hashes to current driver on login
        if (\Illuminate\Support\Facades\Hash::needsRehash($user->password)) {
            $user->password = $data['password']; // 'hashed' cast will re-hash
            $user->save();
        }

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('students.register');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function checkStudent(Request $request)
    {
        $studentNumber = trim((string) $request->query('student_number', ''));
        $digits = preg_replace('/[^0-9]/', '', $studentNumber);

        if (strlen($digits) !== 9) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please provide all 9 digits of your student number.',
            ], 422);
        }

        $enrolled = EnrolledStudent::where('student_number', $digits)
            ->orWhereRaw("REPLACE(student_number,'-','') = ?", [$digits])
            ->first();
        if (!$enrolled) {
            return response()->json([
                'status' => 'error',
                'message' => 'Student number not found in the CvSU Naic registry.',
            ], 404);
        }

        $alreadyUser = User::where('student_number', $digits)
            ->orWhereRaw("REPLACE(student_number,'-','') = ?", [$digits])
            ->exists();

        if ($alreadyUser) {
            return response()->json([
                'status' => 'error',
                'message' => 'This student number already has an account.',
            ], 409);
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Student number located in the campus registry. Continue creating your account.',
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'student_number' => ['required','digits:9'],
            'password'       => ['required','string','min:6','confirmed'],
        ]);

        $studentNumber = trim($data['student_number']);
        $digits = preg_replace('/[^0-9]/', '', $studentNumber);

        // Check if student number is in enrolled list (allow with/without hyphen)
        $enrolled = EnrolledStudent::where('student_number', $digits)
            ->orWhereRaw("REPLACE(student_number,'-','') = ?", [$digits])
            ->first();
        if (!$enrolled) {
            return back()->withErrors([
                'student_number' => 'Student number not found in registry. Only CvSU Naic enrollees may register.',
            ])->withInput();
        }

        // Ensure not already registered as a user
        if (User::where('student_number', $digits)
                ->orWhereRaw("REPLACE(student_number,'-','') = ?", [$digits])
                ->exists()) {
            return back()->withErrors([
                'student_number' => 'This student number already has an account.',
            ])->withInput();
        }

        // Normalize storage to the formatted version from registry if available
        $normalized = $enrolled?->student_number ?? $digits;

        $user = User::create([
            'name'            => $enrolled->full_name ?? 'Student '.$digits,
            'email'           => $digits.'@portal.local',
            'password'        => $data['password'],
            'role'            => 'student',
            'student_number'  => $normalized,
        ]);

        return redirect()->route('login')->with('success', 'Your account was created successfully! Please log in to continue.');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('portal_user');
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
