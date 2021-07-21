<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Period;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PeriodFactory extends Factory
{
    protected $model = Period::class;

    public function definition()
    {
        $name = $this->faker->unique()->year;

        return [
            'organization_id' => Organization::factory(),
            'name' => $name,
            'slug' => Str::slug($name)
        ];
    }
}

