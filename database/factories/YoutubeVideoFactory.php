<?php

namespace Database\Factories;

use App\Models\YoutubeVideo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<YoutubeVideo>
 */
class YoutubeVideoFactory extends Factory
{
    protected $model = YoutubeVideo::class;

    public function definition()
    {
        return [
            'titulo' => fake()->sentence(3),
            'descripcion' => fake()->sentence(8),
            'codigo' => fake()->regexify('[A-Za-z0-9]{12}'),
        ];
    }
}
