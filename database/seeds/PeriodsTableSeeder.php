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
        $ikasgela = Organization::where('slug', 'ikasgela')->first();
        $egibide = Organization::where('slug', 'egibide')->first();
        $deusto = Organization::where('slug', 'deusto')->first();

        $name = '2019';
        factory(Period::class)->create([
            'organization_id' => $ikasgela->id,
            'name' => $name,
            'slug' => Str::slug($name)
        ]);

        $name = '2019';
        factory(Period::class)->create([
            'organization_id' => $egibide->id,
            'name' => $name,
            'slug' => Str::slug($name)
        ]);

        $name = '2019';
        factory(Period::class)->create([
            'organization_id' => $deusto->id,
            'name' => $name,
            'slug' => Str::slug($name)
        ]);
    }
}
