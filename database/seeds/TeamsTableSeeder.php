<?php

use App\Group;
use App\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TeamsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
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
