<?php

namespace Database\Factories;

use App\Models\Curso;
use Illuminate\Database\Eloquent\Factories\Factory;

class MilestoneFactory extends Factory
{
    public function definition()
    {
        return [
            'curso_id' => Curso::factory(),
            'name' => fake()->words(3, true),
            'date' => fake()->dateTimeThisMonth(),
            'published' => true,
        ];
    }
}
