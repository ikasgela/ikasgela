<?php

namespace Database\Factories;

use App\Models\Curso;
use App\Models\SafeExam;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SafeExam>
 */
class SafeExamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'token' => SafeExam::new_token(),
            'quit_password' => SafeExam::new_quit_password(),
            'curso_id' => Curso::factory()->create()
        ];
    }
}
