<?php

namespace Database\Seeders;

use App\Models\Actividad;
use App\Models\IntellijProject;
use App\Models\JPlag;
use Illuminate\Database\Seeder;

class JPlagTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        JPlag::factory()->create([
            'intellij_project_id' => IntellijProject::find(1),
            'match_id' => Actividad::find(1),
            'percent' => 80,
        ]);
    }
}
