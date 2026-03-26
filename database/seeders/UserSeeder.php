<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        // Create student users
        $students = [
            ['name' => 'John Doe', 'email' => 'john@example.com'],
            ['name' => 'Jane Smith', 'email' => 'jane@example.com'],
            ['name' => 'Bob Johnson', 'email' => 'bob@example.com'],
            ['name' => 'Alice Williams', 'email' => 'alice@example.com'],
            ['name' => 'Charlie Brown', 'email' => 'charlie@example.com'],
        ];

        foreach ($students as $student) {
            \App\Models\User::create([
                'name' => $student['name'],
                'email' => $student['email'],
                'password' => bcrypt('password123'),
                'role' => 'student',
            ]);
        }
    }
}
