<?php

namespace Database\Seeders;

use App\Models\Rubric;
use Illuminate\Database\Seeder;

class RubricSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Rubric::factory()->create([
            'titulo' => 'Rúbrica de ejemplo',
            'descripcion' => 'Criterios de calificación.',
            'plantilla' => true,
            'curso_id' => 1,
        ]);
    }
}
