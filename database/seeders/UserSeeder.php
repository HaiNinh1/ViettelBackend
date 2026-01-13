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
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Regular user
        User::create([
            'name' => 'Nguyễn Văn A',
            'username' => 'user',
            'password' => Hash::make('user123'),
            'role' => 'user',
        ]);

        // HR Manager
        User::create([
            'name' => 'Trần Thị B',
            'username' => 'hrmanager',
            'password' => Hash::make('hr123'),
            'role' => 'user',
        ]);
    }
}
