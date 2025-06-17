<?php

namespace Database\Factories;

use App\Models\CriteriaGroup;
use App\Models\Rubric;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CriteriaGroup>
 */
class CriteriaGroupFactory extends Factory
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
            'orden' => Str::orderedUuid(),
            'rubric_id' => Rubric::factory(),
        ];
    }
}
