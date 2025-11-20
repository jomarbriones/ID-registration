<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1) Admin accounts (email + password)
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
    }
}
