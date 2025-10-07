<?php

namespace Database\Factories;

use App\Models\Curso;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class MilestoneFactory extends Factory
{
    public function definition()
    {
        return [
            'curso_id' => Curso::factory(),
            'name' => fake()->words(3, true),
            'date' => Carbon::instance(fake()->dateTimeThisMonth())->second(0),
            'published' => true,
        ];
    }
}
