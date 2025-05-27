<?php

namespace Database\Factories;

use App\Models\Cuestionario;
use App\Models\Pregunta;
use Illuminate\Database\Eloquent\Factories\Factory;
use Override;

/**
 * @extends Factory<Pregunta>
 */
class PreguntaFactory extends Factory
{
    protected $model = Pregunta::class;

    public function definition()
    {
        return [
            'cuestionario_id' => Cuestionario::factory(),
            'titulo' => fake()->words(3, true),
            'texto' => fake()->sentence(16),
            'multiple' => false,
        ];
    }

    #[Override]
    public function configure()
    {
        return $this->afterCreating(function (Pregunta $model) {
            $model->orden = $model->id;
            $model->save();
        });
    }
}
