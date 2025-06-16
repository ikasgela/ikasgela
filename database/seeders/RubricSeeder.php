<?php

namespace Database\Seeders;

use App\Models\Criteria;
use App\Models\CriteriaGroup;
use App\Models\Rubric;
use Illuminate\Database\Seeder;

class RubricSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rubrica = Rubric::factory()->create([
            'titulo' => 'Rúbrica de ejemplo',
            'descripcion' => 'Criterios de calificación.',
            'plantilla' => true,
            'curso_id' => 1,
        ]);

        $c1 = CriteriaGroup::factory()->create([
            'titulo' => 'Plazo de entrega',
            'rubric_id' => $rubrica->id,
        ]);

        Criteria::factory()->create([
            'texto' => 'No entregado',
            'puntuacion' => 0,
            'criteria_group_id' => $c1->id,
        ]);

        Criteria::factory()->create([
            'texto' => 'Entregado con retraso',
            'puntuacion' => 5,
            'criteria_group_id' => $c1->id,
        ]);

        Criteria::factory()->create([
            'texto' => 'Entregado a tiempo',
            'puntuacion' => 10,
            'criteria_group_id' => $c1->id,
        ]);

        $c2 = CriteriaGroup::factory()->create([
            'rubric_id' => $rubrica->id,
        ]);

        Criteria::factory()->create([
            'texto' => 'Suspenso',
            'puntuacion' => 0,
            'criteria_group_id' => $c2->id,
        ]);

        Criteria::factory()->create([
            'texto' => 'Aprobado',
            'puntuacion' => 5,
            'criteria_group_id' => $c2->id,
        ]);

        $c3 = CriteriaGroup::factory()->create([
            'titulo' => 'Penalizaciones',
            'descripcion' => 'Posibles penalizaciones.',
            'rubric_id' => $rubrica->id,
        ]);

        Criteria::factory()->create([
            'texto' => 'Penalización',
            'puntuacion' => -10,
            'criteria_group_id' => $c3->id,
        ]);
    }
}
