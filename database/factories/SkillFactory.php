<?php

namespace Database\Factories;

use App\Curso;
use App\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

class SkillFactory extends Factory
{
    protected $model = Skill::class;

    public function definition()
    {
        return [
            'curso_id' => Curso::factory(),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
        ];
    }
}
