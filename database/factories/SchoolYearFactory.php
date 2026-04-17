<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SchoolYear>
 */
class SchoolYearFactory extends Factory
{
    public function definition(): array
    {
        $startYear = now()->year;

        return [
            'name' => "{$startYear}-" . ($startYear + 1),
            'start_date' => "{$startYear}-06-01",
            'end_date' => ($startYear + 1) . "-03-31",
            'is_current' => true,
            'is_archived' => false,
        ];
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_current' => false,
            'is_archived' => true,
            'archived_at' => now(),
        ]);
    }
}
