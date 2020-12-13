<?php

namespace Database\Factories;

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Actividad;
use App\Tarea;
use App\User;
use Faker\Generator as Faker;

$factory->define(Tarea::class, function (Faker $faker) {

    return [
        'user_id' => factory(User::class),
        'actividad_id' => factory(Actividad::class),
        'estado' => $faker->unique()->randomNumber(2),
    ];
});
