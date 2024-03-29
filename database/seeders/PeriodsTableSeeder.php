<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Period;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PeriodsTableSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $ikasgela = Organization::where('slug', 'ikasgela')->first();
        $egibide = Organization::where('slug', 'egibide')->first();
        $deusto = Organization::where('slug', 'deusto')->first();

        $name = now()->year;
        $periodo = Period::factory()->create([
            'organization_id' => $ikasgela->id,
            'name' => $name,
            'slug' => Str::slug($name)
        ]);

        $ikasgela->current_period_id = $periodo->id;
        $ikasgela->save();

        $name = now()->year;
        $periodo = Period::factory()->create([
            'organization_id' => $egibide->id,
            'name' => $name,
            'slug' => Str::slug($name)
        ]);

        $egibide->current_period_id = $periodo->id;
        $egibide->save();

        $name = now()->year;
        $periodo = Period::factory()->create([
            'organization_id' => $deusto->id,
            'name' => $name,
            'slug' => Str::slug($name)
        ]);

        $deusto->current_period_id = $periodo->id;
        $deusto->save();
    }
}
