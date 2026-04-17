<?php

namespace Database\Factories;

use App\Models\SchoolYear;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KinderAssessment>
 */
class KinderAssessmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_id' => Student::factory()->gradeLevel('kinder'),
            'school_year_id' => SchoolYear::factory(),
            'quarter' => 'Q1',
            'domain' => fake()->randomElement(['socio_emotional', 'language', 'cognitive', 'physical', 'creative']),
            'rating' => fake()->randomElement(['beginning', 'developing', 'proficient']),
            'remarks' => fake()->optional()->sentence(),
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
}
