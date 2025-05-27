<?php

namespace Database\Factories;

use App\Models\Curso;
use App\Models\FileResource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FileResource>
 */
class FileResourceFactory extends Factory
{
    protected $model = FileResource::class;

    public function definition()
    {
        return [
            'titulo' => fake()->words(3, true),
            'descripcion' => fake()->sentence(6),
            'curso_id' => Curso::factory(),
        ];
    }
}
