<?php

namespace Database\Factories;

use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Assignment>
 */
class AssignmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'teacher_id' => User::factory()->teacher(),
            'subject_id' => Subject::factory(),
            'grade_level' => 'grade_1',
            'school_year_id' => SchoolYear::factory(),
            'quarter' => 'Q1',
            'title' => fake()->sentence(3),
            'description' => fake()->sentence(),
            'type' => fake()->randomElement(['written_work', 'performance_task']),
            'max_score' => 100,
            'due_date' => now()->addWeek(),
        ];
    }
}
