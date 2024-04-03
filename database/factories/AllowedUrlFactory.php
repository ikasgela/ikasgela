<?php

namespace Database\Factories;

use App\Models\SafeExam;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AllowedUrl>
 */
class AllowedUrlFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'url' => fake()->url(),
            'safe_exam_id' => SafeExam::factory()->create(),
        ];
    }
}
