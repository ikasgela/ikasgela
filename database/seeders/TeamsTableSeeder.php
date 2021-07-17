<?php

namespace Database\Seeders;

use App\Group;
use App\Team;
use App\User;
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
        $todos = Team::factory()->create([
            'group_id' => $group->id,
            'name' => $name,
            'slug' => Str::slug($name)
        ]);

        $users = User::whereIn('email', ['noa@ikasgela.com', 'marc@ikasgela.com'])->get();

        $todos->users()->sync($users);
    }
}
