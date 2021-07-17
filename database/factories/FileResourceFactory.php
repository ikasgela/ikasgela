<?php

namespace Database\Factories;

use App\FileResource;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FileResourceFactory extends Factory
{
    protected $model = FileResource::class;

    public function definition()
    {
        return [
            'titulo' => $this->faker->words(3, true),
            'descripcion' => $this->faker->sentence(6),
            'orden' => Str::orderedUuid(),
        ];
    }
}
