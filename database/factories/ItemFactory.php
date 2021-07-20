<?php

namespace Database\Factories;

use App\Item;
use App\Pregunta;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition()
    {
        return [
            'pregunta_id' => Pregunta::factory(),
            'texto' => $this->faker->sentence(3),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Item $model) {
            $model->orden = $model->id;
            $model->save();
        });
    }
}
