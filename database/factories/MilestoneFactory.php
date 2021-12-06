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
            'name' => $this->faker->words(3, true),
            'date' => $this->faker->dateTimeThisMonth(),
            'published' => true,
        ];
    }
}
