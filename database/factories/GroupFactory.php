<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Period;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Group>
 */
class GroupFactory extends Factory
{
    protected $model = Group::class;

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
