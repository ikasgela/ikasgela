<?php

namespace Database\Seeders;

use App\Models\TestResult;
use Illuminate\Database\Seeder;

class TestResultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TestResult::factory()->create([
            'titulo' => 'Test de ejemplo',
            'descripcion' => 'Calculadora de resultados de test.',
            'plantilla' => true,
            'curso_id' => 1,
        ]);
    }
}
