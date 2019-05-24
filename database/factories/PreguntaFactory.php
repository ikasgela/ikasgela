<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Cuestionario;
use App\Pregunta;
use Faker\Generator as Faker;

$factory->define(Pregunta::class, function (Faker $faker) {

    return [
        'cuestionario_id' => factory(Cuestionario::class),
        'titulo' => $faker->words(3, true),
        'texto' => $faker->sentence(16),
        'multiple' => false,
    ];
});
