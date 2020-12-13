<?php

namespace Database\Factories;

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\File;
use App\FileUpload;
use App\User;
use Faker\Generator as Faker;

$factory->define(File::class, function (Faker $faker) {

    return [
        // REF: Generar ruta de archivo: https://github.com/fzaninotto/Faker/issues/1472#issuecomment-384364762
        'path' => '/' . implode('/', $faker->words($faker->numberBetween(0, 4))),
        'title' => $faker->words(3, true),
        'size' => $faker->numberBetween(0, 1_000_000_000),
        'file_upload_id' => factory(FileUpload::class),
        'user_id' => factory(User::class),
    ];
});
