<?php

namespace Database\Factories;

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Curso;
use App\Skill;
use Faker\Generator as Faker;

$factory->define(Skill::class, function (Faker $faker) {

    return [
        'curso_id' => factory(Curso::class),
        'name' => $faker->words(3, true),
        'description' => $faker->sentence(),
    ];
});
