<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Organization;
use App\Qualification;
use Faker\Generator as Faker;

$factory->define(Qualification::class, function (Faker $faker) {

    return [
        'organization_id' => factory(Organization::class),
        'name' => $faker->words(3, true),
        'description' => $faker->sentence(),
    ];
});
