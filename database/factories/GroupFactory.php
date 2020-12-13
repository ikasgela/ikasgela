<?php

namespace Database\Factories;

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Group;
use App\Period;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Group::class, function (Faker $faker) {

    $name = $faker->sentence(3, true);

    return [
        'period_id' => factory(Period::class),
        'name' => $name,
        'slug' => Str::slug($name)
    ];
});
