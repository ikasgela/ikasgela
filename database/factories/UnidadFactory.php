<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Curso;
use App\Unidad;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Unidad::class, function (Faker $faker) {

    $nombre = $faker->words(3, true);

    return [
        'curso_id' => factory(Curso::class),
        'nombre' => $nombre,
        'slug' => Str::slug($nombre)
    ];
});

$factory->afterCreating(Unidad::class, function ($unidad, Faker $faker) {
    $unidad->orden = $unidad->id;
});
