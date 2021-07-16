<?php

namespace Database\Factories;

use App\Unidad;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UnidadFactory extends Factory
{
    protected $model = Unidad::class;

    public function definition()
    {
        $nombre = $this->faker->words(3, true);

        return [
            'curso_id' => factory(Curso::class),
            'nombre' => $nombre,
            'slug' => Str::slug($nombre)
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Unidad $unidad) {
            $unidad->orden = $unidad->id;
            $unidad->save();
        });
    }
}
