<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Actividad;
use App\Unidad;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Actividad::class, function (Faker $faker) {

    $nombre = $faker->words(3, true);

    return [
        'unidad_id' => factory(Unidad::class),
        'nombre' => $nombre,
        'slug' => Str::slug($nombre)
    ];
});

$factory->afterCreating(Actividad::class, function ($actividad, Faker $faker) {
    $actividad->orden = $actividad->id;
});
