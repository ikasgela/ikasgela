<?php

namespace Database\Factories;

use App\Models\MarkdownText;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MarkdownText>
 */
class MarkdownTextFactory extends Factory
{
    protected $model = MarkdownText::class;

    public function definition()
    {
        return [
            'titulo' => fake()->words(3, true),
            'descripcion' => fake()->sentence(6),
            'repositorio' => fake()->words(3, true),
            'rama' => 'master',
            'archivo' => 'README.md',
        ];
    }
}
