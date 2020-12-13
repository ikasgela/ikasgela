<?php

namespace Database\Factories;

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Registro;
use App\Tarea;
use App\User;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Registro::class, function (Faker $faker) {

    return [
        'user_id' => factory(User::class),
        'tarea_id' => factory(Tarea::class),
        'estado' => $faker->unique()->randomNumber(2),
        'timestamp' => Carbon::now(),
        'detalles' => $faker->sentence(),
    ];
});
