<?php

namespace Database\Factories;

use App\Models\Cuestionario;
use App\Models\Curso;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cuestionario>
 */
class CuestionarioFactory extends Factory
{
    protected $model = Cuestionario::class;

    public function definition()
    {
        return [
            'titulo' => fake()->words(3, true),
            'descripcion' => fake()->sentence(8),
            'curso_id' => Curso::factory(),
        ];
    }
}
