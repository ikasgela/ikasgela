<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Pregunta;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition()
    {
        return [
            'pregunta_id' => Pregunta::factory(),
            'texto' => fake()->sentence(3),
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
