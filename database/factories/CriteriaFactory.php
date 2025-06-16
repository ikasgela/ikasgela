<?php

namespace Database\Factories;

use App\Models\CriteriaGroup;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Criteria>
 */
class CriteriaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'texto' => fake()->sentence(8),
            'puntuacion' => 0,
            'orden' => Str::orderedUuid(),
            'seleccionado' => false,
            'criteria_group_id' => CriteriaGroup::factory(),
        ];
    }
}
