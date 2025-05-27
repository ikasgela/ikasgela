<?php

namespace Database\Factories;

use App\Models\Curso;
use App\Models\Selector;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Selector>
 */
class SelectorFactory extends Factory
{
    protected $model = Selector::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'titulo' => fake()->words(3, true),
            'descripcion' => fake()->sentence(8),
            'curso_id' => Curso::factory(),
        ];
    }
}
