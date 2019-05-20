<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Skill;
use Faker\Generator as Faker;

$factory->define(Skill::class, function (Faker $faker) {

    return [
        'name' => $faker->words(3, true),
        'description' => $faker->sentence(),
    ];
});
