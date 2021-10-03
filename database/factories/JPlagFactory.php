<?php

namespace Database\Factories;

use App\Models\Actividad;
use App\Models\IntellijProject;
use App\Models\JPlag;
use Illuminate\Database\Eloquent\Factories\Factory;

class JPlagFactory extends Factory
{
    protected $model = JPlag::class;

    public function definition()
    {
        return [
            'intellij_project_id' => IntellijProject::factory(),
            'match_id' => Actividad::factory(),
            'percent' => $this->faker->randomNumber(2),
        ];
    }
}
