<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Organization;
use App\Period;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Period::class, function (Faker $faker) {

    $name = $faker->unique()->year;

    return [
        'organization_id' => factory(Organization::class),
        'name' => $name,
        'slug' => Str::slug($name)
    ];
});
