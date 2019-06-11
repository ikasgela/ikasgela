<?php

use App\Organization;
use App\Period;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PeriodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $organization = Organization::where('slug', 'ikasgela')->first();

        $name = '2018';
        factory(Period::class)->create([
            'organization_id' => $organization->id,
            'name' => $name,
            'slug' => Str::slug($name)
        ]);

        $name = '2019';
        factory(Period::class)->create([
            'organization_id' => $organization->id,
            'name' => $name,
            'slug' => Str::slug($name)
        ]);
    }
}
