<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Announcement>
 */
class AnnouncementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'created_by' => User::factory()->headTeacher(),
            'title' => fake()->sentence(4),
            'content' => fake()->paragraph(),
            'priority' => 'normal',
            'is_pinned' => false,
            'published_at' => now(),
        ];
    }
}
