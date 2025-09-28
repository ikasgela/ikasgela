<?php

namespace Database\Factories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        $email = fake()->unique()->userName . '.' . fake()->randomNumber(4) . '@ikasgela.com';

        return [
            'name' => fake()->name,
            'email' => $email,
            'username' => User::generar_username($email),
            'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
            'remember_token' => Str::random(10),
            'email_verified_at' => Carbon::now(),
            'last_active' => Carbon::now()
        ];
    }
}
