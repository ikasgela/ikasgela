<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\FileResource;
use Faker\Generator as Faker;

$factory->define(FileResource::class, function (Faker $faker) {
    return [
        'titulo' => $faker->words(3, true),
        'descripcion' => $faker->sentence(6),
    ];
});
