<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition()
    {
        $name = fake()->sentence(2, true);

        return [
            'group_id' => Group::factory(),
            'name' => $name,
            'slug' => Str::slug($name)
        ];
    }
}
