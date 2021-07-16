<?php

namespace Database\Factories;

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\IntellijProject;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(IntellijProject::class, function (Faker $faker) {

    $nombre = $faker->words(3, true);

    return [
        'repositorio' => 'root/test',
        'titulo' => $nombre,
        'host' => 'gitea',
    ];
});

$factory->afterCreating(IntellijProject::class, function ($intellij_project, Faker $faker) {
    $intellij_project->orden = Str::orderedUuid();
    $intellij_project->save();
});
