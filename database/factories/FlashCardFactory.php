<?php

namespace Database\Factories;

use App\Models\FlashCard;
use App\Models\FlashDeck;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<FlashCard>
 */
class FlashCardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'titulo' => null,
            'descripcion' => null,
            'anverso' => fake()->sentence(8),
            'anverso_visible' => true,
            'reverso' => fake()->sentence(8),
            'reverso_visible' => false,
            'orden' => Str::orderedUuid(),
            'flash_deck_id' => FlashDeck::factory(),
        ];
    }
}
