<?php

namespace Database\Factories;

use App\Curso;
use App\Feedback;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeedbackFactory extends Factory
{
    protected $model = Feedback::class;

    public function definition()
    {
        return [
            'curso_id' => Curso::factory(),
            'titulo' => $this->faker->sentence(3, true),
            'mensaje' => $this->faker->sentence(8, true),
        ];
    }
}
