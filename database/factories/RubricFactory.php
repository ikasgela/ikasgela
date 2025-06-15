<?php

namespace Database\Factories;

use App\Models\Curso;
use App\Models\Rubric;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rubric>
 */
class RubricFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'titulo' => fake()->words(3, true),
            'descripcion' => fake()->sentence(8),
            'curso_id' => Curso::factory(),
        ];
    }
}
