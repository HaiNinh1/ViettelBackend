<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'password' => 'admin123', // Plain text password
            'role' => 'admin',
        ]);

        // Regular user
        User::create([
            'name' => 'Nguyễn Văn A',
            'username' => 'user',
            'password' => 'user123', // Plain text password
            'role' => 'user',
        ]);

        // HR Manager
        User::create([
            'name' => 'Trần Thị B',
            'username' => 'hrmanager',
            'password' => 'hr123', // Plain text password
            'role' => 'user',
        ]);
    }
}
