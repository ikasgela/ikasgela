<?php

namespace Database\Factories;

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Group;
use App\Team;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Team::class, function (Faker $faker) {

    $name = $faker->sentence(2, true);

    return [
        'group_id' => factory(Group::class),
        'name' => $name,
        'slug' => Str::slug($name)
    ];
});
