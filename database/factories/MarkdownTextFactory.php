<?php

namespace Database\Factories;

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\MarkdownText;
use Faker\Generator as Faker;

$factory->define(MarkdownText::class, function (Faker $faker) {

    return [
        'titulo' => $faker->words(3, true),
        'descripcion' => $faker->sentence(6),
        'repositorio' => $faker->words(3, true),
        'rama' => 'master',
        'archivo' => 'README.md'
    ];
});
