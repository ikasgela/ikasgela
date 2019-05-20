<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Qualification;
use App\Tarea;
use App\User;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Qualification::class, function (Faker $faker) {

    return [
        'name' => $faker->words(3, true),
        'description' => $faker->sentence(),
    ];
});
