<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Curso;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Curso::class, function (Faker $faker) {

    $name = $faker->sentence(2);

    return [
        'nombre' => $name,
        'descripcion' => $faker->sentence(8),
        'slug' => Str::slug($name)
    ];
});
