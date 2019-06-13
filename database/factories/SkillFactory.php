<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Organization;
use App\Skill;
use Faker\Generator as Faker;

$factory->define(Skill::class, function (Faker $faker) {

    return [
        'organization_id' => factory(Organization::class),
        'name' => $faker->words(3, true),
        'description' => $faker->sentence(),
    ];
});
