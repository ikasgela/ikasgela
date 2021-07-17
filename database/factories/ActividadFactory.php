<?php

namespace Database\Factories;

use App\Actividad;
use App\Unidad;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ActividadFactory extends Factory
{
    protected $model = Actividad::class;

    public function definition()
    {
        $nombre = $this->faker->words(3, true);

        return [
            'unidad_id' => Unidad::factory(),
            'nombre' => $nombre,
            'slug' => Str::slug($nombre)
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Actividad $model) {
            $model->orden = $model->id;
            $model->save();
        });
    }
}
