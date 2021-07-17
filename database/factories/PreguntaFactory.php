<?php

namespace Database\Factories;

use App\Cuestionario;
use App\Pregunta;
use Illuminate\Database\Eloquent\Factories\Factory;

class PreguntaFactory extends Factory
{
    protected $model = Pregunta::class;

    public function definition()
    {
        return [
            'cuestionario_id' => Cuestionario::factory(),
            'titulo' => $this->faker->words(3, true),
            'texto' => $this->faker->sentence(16),
            'multiple' => false,
        ];
    }
}
