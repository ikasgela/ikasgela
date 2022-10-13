<?php

namespace Database\Factories;

use App\Models\Actividad;
use App\Models\RuleGroup;
use App\Models\Selector;
use Illuminate\Database\Eloquent\Factories\Factory;

class RuleGroupFactory extends Factory
{
    protected $model = RuleGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'operador' => $this->faker->randomElement(['and', 'or']),
            'accion' => 'siguiente',
            'resultado' => Actividad::factory(),
            'selector_id' => Selector::factory(),
        ];
    }
}
