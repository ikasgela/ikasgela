<?php

namespace Database\Factories;

use App\YoutubeVideo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class YoutubeVideoFactory extends Factory
{
    protected $model = YoutubeVideo::class;

    public function definition()
    {
        return [
            'titulo' => $this->faker->sentence(3),
            'descripcion' => $this->faker->sentence(8),
            'codigo' => $this->faker->regexify('[A-Za-z0-9]{12}'),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (YoutubeVideo $model) {
            $model->orden = Str::orderedUuid();
            $model->save();
        });
    }
}
