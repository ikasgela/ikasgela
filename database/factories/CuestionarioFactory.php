<?php

namespace Database\Factories;

use App\Models\Cuestionario;
use App\Models\Curso;
use Illuminate\Database\Eloquent\Factories\Factory;

class CuestionarioFactory extends Factory
{
    protected $model = Cuestionario::class;

    public function definition()
    {
        return [
            'titulo' => $this->faker->words(3, true),
            'descripcion' => $this->faker->sentence(8),
            'curso_id' => Curso::factory(),
        ];
    }
}
