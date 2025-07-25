<?php

namespace Database\Factories;

use App\Models\Curso;
use App\Models\Qualification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Qualification>
 */
class QualificationFactory extends Factory
{
    protected $model = Qualification::class;

    public function definition()
    {
        return [
            'curso_id' => Curso::factory(),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
        ];
    }
}
