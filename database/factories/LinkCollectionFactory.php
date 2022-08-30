<?php

namespace Database\Factories;

use App\Models\Curso;
use App\Models\LinkCollection;
use Illuminate\Database\Eloquent\Factories\Factory;

class LinkCollectionFactory extends Factory
{
    protected $model = LinkCollection::class;

    public function definition()
    {
        return [
            'titulo' => $this->faker->words(3, true),
            'descripcion' => $this->faker->sentence(6),
            'curso_id' => Curso::factory(),
        ];
    }
}
