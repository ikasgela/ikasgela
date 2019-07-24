<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\FileUpload;
use Faker\Generator as Faker;

$factory->define(FileUpload::class, function (Faker $faker) {

    return [
        'titulo' => $faker->words(3, true),
        'descripcion' => $faker->sentence(6),
        'max_files' => 1
    ];
});
