<?php

namespace Database\Factories;

use App\Models\Rule;
use App\Models\RuleGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'propiedad' => $this->faker->randomElement(['nota', 'intentos']),
            'operador' => $this->faker->randomElement(['>', '<', '>=', '<=', '==', '!=']),
            'valor' => $this->faker->numberBetween(50, 100),
            'rule_group_id' => RuleGroup::factory(),
        ];
    }
}
