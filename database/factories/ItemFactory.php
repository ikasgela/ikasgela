<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Cuestionario;
use App\Item;
use App\Pregunta;
use Faker\Generator as Faker;

$factory->define(Item::class, function (Faker $faker) {

    return [
        'pregunta_id' => factory(Pregunta::class),
        'texto' => $faker->sentence(3),
    ];
});
