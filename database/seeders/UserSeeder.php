<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Head Teacher
        User::create([
            'name' => 'Head Teacher',
            'email' => 'headteacher@school.local',
            'password' => Hash::make('password'),
            'role' => 'head_teacher',
            'grade_level' => null,
            'is_active' => true,
        ]);

        // Teachers (one per grade level)
        $teachers = [
            ['name' => 'Kinder Teacher', 'email' => 'kinder@school.local', 'grade_level' => 'kinder'],
            ['name' => 'Grade 1 Teacher', 'email' => 'grade1@school.local', 'grade_level' => 'grade_1'],
            ['name' => 'Grade 2 Teacher', 'email' => 'grade2@school.local', 'grade_level' => 'grade_2'],
            ['name' => 'Grade 3 Teacher', 'email' => 'grade3@school.local', 'grade_level' => 'grade_3'],
            ['name' => 'Grade 4 Teacher', 'email' => 'grade4@school.local', 'grade_level' => 'grade_4'],
            ['name' => 'Grade 5 Teacher', 'email' => 'grade5@school.local', 'grade_level' => 'grade_5'],
            ['name' => 'Grade 6 Teacher', 'email' => 'grade6@school.local', 'grade_level' => 'grade_6'],
        ];

        foreach ($teachers as $teacher) {
            User::create([
                'name' => $teacher['name'],
                'email' => $teacher['email'],
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'grade_level' => $teacher['grade_level'],
                'is_active' => true,
            ]);
        }
    }
}
