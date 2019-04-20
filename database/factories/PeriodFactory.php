<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Organization;
use App\Period;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Period::class, function (Faker $faker) {

    $organization = factory(Organization::class)->create();

    $name = $faker->year;

    return [
        'organization_id' => $organization->id,
        'name' => $name,
        'slug' => Str::slug($name)
    ];
});
