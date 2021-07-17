<?php

namespace Database\Factories;

use App\Actividad;
use App\Tarea;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TareaFactory extends Factory
{
    protected $model = Tarea::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'actividad_id' => Actividad::factory(),
            'estado' => $this->faker->unique()->randomNumber(2),
        ];
    }
}
