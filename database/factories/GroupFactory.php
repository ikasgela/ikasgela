<?php

namespace Database\Factories;

use App\Group;
use App\Period;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GroupFactory extends Factory
{
    protected $model = Group::class;

    public function definition()
    {
        $name = $this->faker->sentence(3, true);

        return [
            'period_id' => Period::factory(),
            'name' => $name,
            'slug' => Str::slug($name)
        ];
    }
}
