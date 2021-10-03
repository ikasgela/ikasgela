<?php

namespace Database\Seeders;

use App\Models\JPlag;
use App\Models\Tarea;
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
            'tarea_id' => Tarea::factory(),
            'match_id' => Tarea::factory(),
            'percent' => 80,
        ]);
    }
}
