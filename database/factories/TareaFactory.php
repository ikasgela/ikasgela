<?php

namespace Database\Factories;

use App\Models\Actividad;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tarea>
 */
class TareaFactory extends Factory
{
    protected $model = Tarea::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'actividad_id' => Actividad::factory(),
            'estado' => fake()->unique()->randomNumber(2),
        ];
    }
}
