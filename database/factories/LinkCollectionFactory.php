<?php

namespace Database\Factories;

use App\Models\Curso;
use App\Models\LinkCollection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LinkCollection>
 */
class LinkCollectionFactory extends Factory
{
    protected $model = LinkCollection::class;

    public function definition()
    {
        return [
            'titulo' => fake()->words(3, true),
            'descripcion' => fake()->sentence(6),
            'curso_id' => Curso::factory(),
        ];
    }
}
