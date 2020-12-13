<?php

namespace Database\Factories;

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Organization;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Organization::class, function (Faker $faker) {

    $name = $faker->company;

    return [
        'name' => $name,
        'slug' => Str::slug($name),
        'seats' => $faker->numberBetween(5, 10),
    ];
});
