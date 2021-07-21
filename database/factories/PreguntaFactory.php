<?php

namespace Database\Factories;

use App\Models\Cuestionario;
use App\Models\Pregunta;
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

    public function configure()
    {
        return $this->afterCreating(function (Pregunta $model) {
            $model->orden = $model->id;
            $model->save();
        });
    }
}
