<?php

namespace Database\Factories;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        $email = $this->faker->unique()->safeEmail;

        return [
            'name' => $this->faker->name,
            'email' => $email,
            'username' => User::generar_username($email),
            'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
            'remember_token' => Str::random(10),
            'email_verified_at' => Carbon::now(),
            'last_active' => Carbon::now()
        ];
    }
}
