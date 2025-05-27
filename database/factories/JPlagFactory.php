<?php

namespace Database\Factories;

use App\Models\JPlag;
use App\Models\Tarea;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JPlag>
 */
class JPlagFactory extends Factory
{
    protected $model = JPlag::class;

    public function definition()
    {
        return [
            'tarea_id' => Tarea::factory(),
            'match_id' => Tarea::factory(),
            'percent' => fake()->randomNumber(2),
        ];
    }
}
