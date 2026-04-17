<?php

namespace Database\Factories;

use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Grade>
 */
class GradeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'subject_id' => Subject::factory(),
            'school_year_id' => SchoolYear::factory(),
            'quarter' => 'Q1',
            'ww_total_score' => 80,
            'ww_max_score' => 100,
            'ww_percentage' => 80.00,
            'ww_weighted' => 32.00,
            'pt_total_score' => 70,
            'pt_max_score' => 100,
            'pt_percentage' => 70.00,
            'pt_weighted' => 28.00,
            'qa_score' => 90,
            'qa_max_score' => 100,
            'qa_percentage' => 90.00,
            'qa_weighted' => 18.00,
            'quarterly_grade' => 78.00,
            'remarks' => 'Passed',
            'status' => 'draft',
        ];
    }

    public function submitted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'submitted_at' => now(),
            'approved_at' => now(),
        ]);
    }
}
