<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\YoutubeVideo;
use Faker\Generator as Faker;

$factory->define(YoutubeVideo::class, function (Faker $faker) {

    return [
        'titulo' => $faker->sentence(3),
        'descripcion' => $faker->sentence(8),
        'codigo' => $faker->regexify('[A-Za-z0-9]{12}'),
    ];
});
