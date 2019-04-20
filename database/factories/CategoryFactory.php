<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Category;
use App\Period;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Category::class, function (Faker $faker) {

    $period = factory(Period::class)->create();

    $name = $faker->sentence(3, true);

    return [
        'period_id' => $period->id,
        'name' => $name,
        'slug' => Str::slug($name)
    ];
});
