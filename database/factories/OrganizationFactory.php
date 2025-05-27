<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Organization>
 */
class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition()
    {
        $name = fake()->company;

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'seats' => fake()->numberBetween(5, 10),
        ];
    }
}
