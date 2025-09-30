<?php

namespace Database\Factories;

use App\Models\Curso;
use App\Models\FlashDeck;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FlashDeck>
 */
class FlashDeckFactory extends Factory
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
