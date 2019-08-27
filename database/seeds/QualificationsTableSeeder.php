<?php

use App\Curso;
use App\Organization;
use App\Qualification;
use App\Skill;
use App\Unidad;
use Illuminate\Database\Seeder;

class QualificationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Deusto

        $organization = Organization::where('slug', 'deusto')->first();

        // Competencias

        $ce1 = factory(Skill::class)->create([
            'organization_id' => $organization->id,
            'name' => 'CE1 - Diseño de algoritmos',
            'description' => 'Especificar, diseñar e implementar algoritmos en un lenguaje de programación, utilizando métodos eficientes, sistemáticos y organizados de resolución de problemas.',
        ]);

        $ce2 = factory(Skill::class)->create([
            'organization_id' => $organization->id,
            'name' => 'CE2 - Sintáxis del lenguaje',
            'description' => 'Escribir correctamente, compilar y ejecutar programas en un lenguaje de alto nivel.',
        ]);

        $ce3 = factory(Skill::class)->create([
            'organization_id' => $organization->id,
            'name' => 'CE3 - Estructuras de datos y de control',
            'description' => 'Conocer y dominar estructuras básicas fundamentales utilizadas en la programación, tanto estructuras de datos como estructuras de control del flujo del programa.',
        ]);

        // Cualificación

        $cualificacion = factory(Qualification::class)->create([
            'organization_id' => $organization->id,
            'name' => 'General',
            'description' => 'Cualificación predeterminada para el curso.',
            'template' => true,
        ]);

        $cualificacion->skills()->attach($ce1, ['percentage' => 20]);
        $cualificacion->skills()->attach($ce2, ['percentage' => 40]);
        $cualificacion->skills()->attach($ce3, ['percentage' => 40]);

        // Asociar la cualificación al curso

        $curso = Curso::whereHas('category.period.organization', function ($query) {
            $query->where('organizations.slug', 'deusto');
        })
            ->where('slug', 'programacion-i')
            ->first();

        $curso->qualification()->associate($cualificacion);
        $curso->save();

        // Ikasgela

        $organization = Organization::where('slug', 'ikasgela')->first();

        // Curso

        $cualificacion = factory(Qualification::class)->create([
            'organization_id' => $organization->id,
            'name' => 'General',
            'description' => 'Cualificación predeterminada para el curso.',
            'template' => true,
        ]);

        $ud1 = factory(Skill::class)->create([
            'organization_id' => $organization->id,
            'name' => 'UD1 - Introducción',
            'description' => 'Presentación del curso, conceptos básicos y uso de las herramientas.',
        ]);

        $ud2 = factory(Skill::class)->create([
            'organization_id' => $organization->id,
            'name' => 'UD2 - Programación estructurada',
            'description' => 'Desarrollo de aplicaciones mediante programación estructurada.',
        ]);

        $ud3 = factory(Skill::class)->create([
            'organization_id' => $organization->id,
            'name' => 'UD3 - Programación modular',
            'description' => 'Desarrollo de aplicaciones mediante programación modular.',
        ]);

        $cualificacion->skills()->attach($ud1, ['percentage' => 100]);
        $cualificacion->skills()->attach($ud2, ['percentage' => 100]);
        $cualificacion->skills()->attach($ud3, ['percentage' => 100]);

        $curso = Curso::whereHas('category.period.organization', function ($query) {
            $query->where('organizations.slug', 'ikasgela');
        })
            ->where('slug', 'programacion')
            ->first();

        $curso->qualification()->associate($cualificacion);
        $curso->save();

        // Unidad

        $cualificacion = factory(Qualification::class)->create([
            'organization_id' => $organization->id,
            'name' => 'Colecciones',
            'description' => 'Cualificación para una unidad didáctica.',
            'template' => true,
        ]);

        $cualificacion->skills()->attach($ud2, ['percentage' => 100]);

        $unidad = Unidad::whereHas('curso.category.period.organization', function ($query) {
            $query->where('organizations.slug', 'ikasgela');
        })
            ->where('slug', 'gui')
            ->first();

        $unidad->qualification()->associate($cualificacion);
        $unidad->save();
    }
}
