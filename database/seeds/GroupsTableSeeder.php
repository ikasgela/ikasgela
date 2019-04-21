<?php

use App\Group;
use App\Period;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $period = Period::where('name', '2018')->first();

        $name = '147FA';
        factory(Group::class)->create([
            'period_id' => $period->id,
            'name' => $name,
            'slug' => Str::slug($name)
        ]);
    }
}
