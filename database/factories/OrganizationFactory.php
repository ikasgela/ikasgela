<?php

namespace Database\Factories;

use App\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition()
    {
        $name = $this->faker->company;

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'seats' => $this->faker->numberBetween(5, 10),
        ];
    }
}
