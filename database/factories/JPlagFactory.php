<?php

namespace Database\Factories;

use App\Models\JPlag;
use App\Models\Tarea;
use Illuminate\Database\Eloquent\Factories\Factory;

class JPlagFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = JPlag::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'tarea_id' => Tarea::factory(),
            'repository' => $this->faker->sentence(),
            'match' => $this->faker->randomNumber(2),
        ];
    }
}
