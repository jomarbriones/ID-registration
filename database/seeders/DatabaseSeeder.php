<?php

namespace Database\Seeders;

use App\Models\EnrolledStudent;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1) Sample list of enrolled student numbers
        $students = [
            ['student_number' => '202110512', 'full_name' => 'Juan Dela Cruz'],
            ['student_number' => '202110513', 'full_name' => 'Maria Santos'],
            ['student_number' => '202110514', 'full_name' => 'Pedro Reyes'],
            ['student_number' => '202110515', 'full_name' => 'Ana Garcia'],
            ['student_number' => '202110516', 'full_name' => 'John Smith'],
        ];
        foreach ($students as $s) {
            EnrolledStudent::updateOrCreate(
                ['student_number' => $s['student_number']],
                ['full_name' => $s['full_name']]
            );
        }

        // 2) Admin accounts (email + password)
        $admins = [
            ['name' => 'Portal Admin', 'email' => 'admin@portal.local', 'password' => 'Admin123!', 'role' => 'admin'],
            ['name' => 'Registrar',    'email' => 'registrar@portal.local', 'password' => 'Registrar123!', 'role' => 'admin'],
        ];
        foreach ($admins as $a) {
            User::updateOrCreate(
                ['email' => $a['email']],
                [
                    'name' => $a['name'],
                    'password' => Hash::make($a['password']),
                    'role' => 'admin',
                    'student_number' => null,
                ]
            );
        }

        // 3) Optional: one sample student account linked to list above
        User::updateOrCreate(
            ['student_number' => '202110512'],
            [
                'name' => 'Juan Dela Cruz',
                'email' => '202110512@portal.local',
                'password' => Hash::make('Student123!'),
                'role' => 'student',
            ]
        );
    }
}
