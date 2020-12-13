<?php

namespace Database\Factories;

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Curso;
use App\Feedback;
use Faker\Generator as Faker;

$factory->define(Feedback::class, function (Faker $faker) {

    return [
        'curso_id' => factory(Curso::class),
        'titulo' => $faker->sentence(3, true),
        'mensaje' => $faker->sentence(8, true),
    ];
});
