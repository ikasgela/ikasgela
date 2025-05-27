<?php

namespace Database\Factories;

use App\Models\Curso;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Skill>
 */
class SkillFactory extends Factory
{
    protected $model = Skill::class;

    public function definition()
    {
        return [
            'curso_id' => Curso::factory(),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
        ];
    }
}
