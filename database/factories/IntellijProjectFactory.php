<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\IntellijProject;
use Faker\Generator as Faker;

$factory->define(IntellijProject::class, function (Faker $faker) {

    $nombre = $faker->words(3, true);

    return [
        'repositorio' => 'root/test',
        'titulo' => $nombre,
        'host' => 'gitea',
    ];
});
