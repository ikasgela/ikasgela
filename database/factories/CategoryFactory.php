<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Period;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition()
    {
        $name = fake()->sentence(3, true);

        return [
            'period_id' => Period::factory(),
            'name' => $name,
            'slug' => Str::slug($name)
        ];
    }
}
