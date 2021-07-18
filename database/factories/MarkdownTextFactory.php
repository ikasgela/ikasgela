<?php

namespace Database\Factories;

use App\MarkdownText;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MarkdownTextFactory extends Factory
{
    protected $model = MarkdownText::class;

    public function definition()
    {
        return [
            'titulo' => $this->faker->words(3, true),
            'descripcion' => $this->faker->sentence(6),
            'repositorio' => $this->faker->words(3, true),
            'rama' => 'master',
            'archivo' => 'README.md',
        ];
    }
}
