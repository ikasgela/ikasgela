<?php

namespace Database\Factories;

use App\Models\Rule;
use App\Models\RuleGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rule>
 */
class RuleFactory extends Factory
{
    protected $model = Rule::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'propiedad' => fake()->randomElement(['puntuacion', 'intentos']),
            'operador' => fake()->randomElement(['>', '<', '>=', '<=', '==', '!=']),
            'valor' => fake()->numberBetween(50, 100),
            'rule_group_id' => RuleGroup::factory(),
        ];
    }
}
