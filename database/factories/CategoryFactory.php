<?php

namespace Database\Factories;

use App\Category;
use App\Period;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

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
