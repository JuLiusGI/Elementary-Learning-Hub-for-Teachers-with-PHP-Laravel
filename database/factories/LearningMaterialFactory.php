<?php

namespace Database\Factories;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LearningMaterial>
 */
class LearningMaterialFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uploaded_by' => User::factory()->teacher(),
            'subject_id' => Subject::factory(),
            'grade_level' => 'grade_1',
            'quarter' => 'Q1',
            'week_number' => 1,
            'title' => fake()->sentence(3),
            'description' => fake()->sentence(),
            'file_type' => 'pdf',
            'file_path' => 'materials/test-file.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
            'is_downloadable' => true,
            'download_count' => 0,
        ];
    }
}
