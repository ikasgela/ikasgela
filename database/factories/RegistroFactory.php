<?php

namespace Database\Factories;

use App\Models\Registro;
use App\Models\Tarea;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegistroFactory extends Factory
{
    protected $model = Registro::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'tarea_id' => Tarea::factory(),
            'estado' => $this->faker->unique()->randomNumber(2),
            'timestamp' => Carbon::now(),
            'detalles' => $this->faker->sentence(),
        ];
    }
}
