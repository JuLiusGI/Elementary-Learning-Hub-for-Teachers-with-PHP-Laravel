<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subject>
 */
class SubjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word() . ' Subject',
            'code' => fake()->unique()->lexify('SUBJ-???'),
            'description' => fake()->sentence(),
            'display_order' => fake()->numberBetween(1, 20),
            'is_active' => true,
        ];
    }
}
