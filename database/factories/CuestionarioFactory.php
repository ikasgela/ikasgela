<?php

namespace Database\Factories;

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Cuestionario;
use Faker\Generator as Faker;

$factory->define(Cuestionario::class, function (Faker $faker) {

    return [
        'titulo' => $faker->words(3, true),
        'descripcion' => $faker->sentence(8),
    ];
});
