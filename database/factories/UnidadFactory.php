<?php

namespace Database\Factories;

use App\Models\Curso;
use App\Models\Unidad;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UnidadFactory extends Factory
{
    protected $model = Unidad::class;

    public function definition()
    {
        $nombre = $this->faker->words(3, true);

        return [
            'curso_id' => Curso::factory(),
            'nombre' => $nombre,
            'slug' => Str::slug($nombre)
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Unidad $model) {
            $model->orden = $model->id;
            $model->save();
        });
    }
}
