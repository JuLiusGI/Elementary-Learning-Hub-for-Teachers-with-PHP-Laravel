<?php

namespace Database\Factories;

use App\Models\SchoolYear;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'lrn' => fake()->unique()->numerify('############'),
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->optional()->lastName(),
            'last_name' => fake()->lastName(),
            'date_of_birth' => fake()->dateTimeBetween('-12 years', '-6 years'),
            'gender' => fake()->randomElement(['male', 'female']),
            'address_barangay' => fake()->streetName(),
            'address_municipality' => fake()->city(),
            'address_province' => fake()->state(),
            'guardian_name' => fake()->name(),
            'guardian_contact' => fake()->numerify('09#########'),
            'guardian_relationship' => fake()->randomElement(['mother', 'father', 'guardian']),
            'grade_level' => 'grade_1',
            'school_year_id' => SchoolYear::factory(),
            'teacher_id' => User::factory()->teacher(),
            'enrollment_status' => 'active',
            'date_enrolled' => now(),
        ];
    }

    public function gradeLevel(string $level): static
    {
        return $this->state(fn (array $attributes) => [
            'grade_level' => $level,
        ]);
    }
}
