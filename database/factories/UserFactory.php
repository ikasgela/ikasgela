<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\User;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(User::class, function (Faker $faker) {

    $email = $faker->unique()->safeEmail;

    return [
        'name' => $faker->name,
        'email' => $email,
        'username' => User::generar_username($email),
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'remember_token' => Str::random(10),
        'email_verified_at' => Carbon::now()
    ];
});
