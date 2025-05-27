<?php

namespace Database\Factories;

use App\Models\Curso;
use App\Models\Feedback;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Feedback>
 */
class FeedbackFactory extends Factory
{
    protected $model = Feedback::class;

    public function definition()
    {
        return [
            'comentable_id' => Curso::factory(),
            'comentable_type' => Curso::class,
            'titulo' => fake()->sentence(3, true),
            'mensaje' => fake()->sentence(8, true),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Feedback $model) {
            $model->orden = $model->id;
            $model->save();
        });
    }
}
