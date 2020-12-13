<?php

namespace Database\Seeders;

use App\Group;
use App\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TeamsTableSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $group = Group::where('name', '147FA')->first();

        $name = 'Todos';
        factory(Team::class)->create([
            'group_id' => $group->id,
            'name' => $name,
            'slug' => Str::slug($name)
        ]);
    }
}
