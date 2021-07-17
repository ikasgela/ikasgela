<?php

namespace Database\Seeders;

use App\Curso;
use App\Qualification;
use App\Skill;
use App\Unidad;
use Illuminate\Database\Seeder;

class QualificationsTableSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        // Ikasgela - Programación

        $curso = Curso::whereHas('category.period.organization', function ($query) {
            $query->where('organizations.slug', 'ikasgela');
        })
            ->where('slug', 'programacion')
            ->first();

        // Competencias

        $ce1 = Skill::factory()->create([
            'curso_id' => $curso->id,
            'name' => 'CE1 - Diseño de algoritmos',
            'description' => 'Especificar, diseñar e implementar algoritmos en un lenguaje de programación, utilizando métodos eficientes, sistemáticos y organizados de resolución de problemas.',
            'peso_examen' => 40,
        ]);

        $ce2 = Skill::factory()->create([
            'curso_id' => $curso->id,
            'name' => 'CE2 - Sintáxis del lenguaje',
            'description' => 'Escribir correctamente, compilar y ejecutar programas en un lenguaje de alto nivel.',
            'peso_examen' => 40,
        ]);

        $ce3 = Skill::factory()->create([
            'curso_id' => $curso->id,
            'name' => 'CE3 - Estructuras de datos y de control',
            'description' => 'Conocer y dominar estructuras básicas fundamentales utilizadas en la programación, tanto estructuras de datos como estructuras de control del flujo del programa.',
            'peso_examen' => 40,
        ]);

        // Cualificación

        $cualificacion = Qualification::factory()->create([
            'curso_id' => $curso->id,
            'name' => 'General',
            'description' => 'Cualificación predeterminada para el curso.',
            'template' => true,
        ]);

        $cualificacion->skills()->attach($ce1, ['percentage' => 20]);
        $cualificacion->skills()->attach($ce2, ['percentage' => 40]);
        $cualificacion->skills()->attach($ce3, ['percentage' => 40]);

        // Asociar la cualificación al curso

        $curso->qualification()->associate($cualificacion);
        $curso->save();

        // Primera unidad

        $cualificacion = Qualification::factory()->create([
            'curso_id' => $curso->id,
            'name' => 'Introducción a la programación',
            'description' => 'Presentación del curso, conceptos básicos y uso de las herramientas.',
            'template' => true,
        ]);

        $cualificacion->skills()->attach($ce1, ['percentage' => 100]);

        $unidad = Unidad::whereHas('curso', function ($query) use ($curso) {
            $query->where('slug', $curso->slug);
        })
            ->where('slug', 'introduccion')
            ->first();

        $unidad->qualification()->associate($cualificacion);
        $unidad->save();

    }
}
