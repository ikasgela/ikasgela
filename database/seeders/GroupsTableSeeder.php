<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Period;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $period = Period::whereHas('organization', function ($query) {
            $query->where('organizations.slug', 'egibide');
        })
            ->where('slug', '2019')
            ->first();

        $name = '147FA';
        Group::factory()->create([
            'period_id' => $period->id,
            'name' => $name,
            'slug' => Str::slug($name)
        ]);
    }
}
