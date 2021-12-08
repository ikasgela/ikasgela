<?php

namespace Database\Seeders;

use App\Models\Curso;
use App\Models\Milestone;
use Illuminate\Database\Seeder;

class MilestoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $curso = Curso::where('nombre', 'Programación')->first();

        Milestone::factory()->create([
            'curso_id' => $curso,
            'name' => 'Primera evaluación',
            'date' => now(),
            'published' => true,
        ]);

        Milestone::factory()->create([
            'curso_id' => $curso,
            'name' => 'Segunda evaluación',
            'date' => now()->addDays(7),
            'published' => true,
        ]);
    }
}
