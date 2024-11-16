<?php

namespace Database\Factories;

use App\Models\SafeExam;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AllowedApp>
 */
class AllowedAppFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->word(),
            'os' => fake()->numberBetween(0, 2),
            'executable' => fake()->word(),
            'path' => fake()->word(),
            'identifier' => fake()->word(),
            'show_icon' => fake()->boolean(),
            'force_close' => fake()->boolean(),
            'disabled' => fake()->boolean(),
            'safe_exam_id' => SafeExam::factory()->create(),
        ];
    }
}
