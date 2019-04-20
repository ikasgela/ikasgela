<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Category;
use App\Curso;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Curso::class, function (Faker $faker) {

    $category = factory(Category::class)->create();

    $name = $faker->sentence(2);

    return [
        'category_id' => $category->id,
        'nombre' => $name,
        'descripcion' => $faker->sentence(8),
        'slug' => Str::slug($name)
    ];
});
