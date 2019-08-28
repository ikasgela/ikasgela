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

        // Primera unidad

        $cualificacion = factory(Qualification::class)->create([
            'organization_id' => $organization->id,
            'name' => 'Introducción a la programación',
            'description' => 'Presentación del curso, conceptos básicos y uso de las herramientas.',
            'template' => true,
        ]);

        $cualificacion->skills()->attach($ce1, ['percentage' => 100]);

        $unidad = Unidad::whereHas('curso.category.period.organization', function ($query) use ($organization) {
            $query->where('organizations.slug', $organization->slug);
        })
            ->where('slug', 'introduccion')
            ->first();

        $unidad->qualification()->associate($cualificacion);
        $unidad->save();

        // Ikasgela

        $this->generarEstructuraCentro('ikasgela');

        // Egibide

        $this->generarEstructuraCentro('egibide');
    }

    /**
     * @param $organization
     * @param $skill
     */
    private function generarCualificacionUnidad($organization, $skill): void
    {
        $cualificacion = factory(Qualification::class)->create([
            'organization_id' => $organization->id,
            'name' => $skill->name,
            'description' => $skill->description,
            'template' => true,
        ]);

        $cualificacion->skills()->attach($skill, ['percentage' => 100]);

        $unidad = Unidad::whereHas('curso.category.period.organization', function ($query) use ($organization) {
            $query->where('organizations.slug', $organization->slug);
        })
            ->where('nombre', $skill->name)
            ->first();

        $unidad->qualification()->associate($cualificacion);
        $unidad->save();
    }

    private function generarEstructuraCentro($slug)
    {
        $organization = Organization::where('slug', $slug)->first();

        // Skills

        $skill1 = factory(Skill::class)->create([
            'organization_id' => $organization->id,
            'name' => 'Introducción a la programación',
            'description' => 'Presentación del curso, conceptos básicos y uso de las herramientas.',
        ]);

        $skill2 = factory(Skill::class)->create([
            'organization_id' => $organization->id,
            'name' => 'Programación estructurada',
            'description' => 'Desarrollo de aplicaciones mediante programación estructurada.',
        ]);

        $skill3 = factory(Skill::class)->create([
            'organization_id' => $organization->id,
            'name' => 'Programación modular',
            'description' => 'Desarrollo de aplicaciones mediante programación modular.',
        ]);

        $skill4 = factory(Skill::class)->create([
            'organization_id' => $organization->id,
            'name' => 'Estructuras de datos I',
            'description' => 'Estructuras estáticias: arrays y matrices.',
        ]);

        $skill5 = factory(Skill::class)->create([
            'organization_id' => $organization->id,
            'name' => 'Programación orientada a objetos',
            'description' => 'Conceptos básicos de programación orientada a objetos.',
        ]);

        $skill6 = factory(Skill::class)->create([
            'organization_id' => $organization->id,
            'name' => 'Estructuras de datos II',
            'description' => 'Estructuras dinámicas: listas, conjuntos y diccionarios.',
        ]);

        $skill7 = factory(Skill::class)->create([
            'organization_id' => $organization->id,
            'name' => 'Programación funcional',
            'description' => 'Conceptos básicos de programación funcional.',
        ]);

        $skill8 = factory(Skill::class)->create([
            'organization_id' => $organization->id,
            'name' => 'GUI',
            'description' => 'Desarrollo de aplicaciones con interfaz gráfica de usuario.',
        ]);

        $skill9 = factory(Skill::class)->create([
            'organization_id' => $organization->id,
            'name' => 'Persistencia',
            'description' => 'Almacenamiento de información en ficheros y bases de datos.',
        ]);

        // General

        $cualificacion = factory(Qualification::class)->create([
            'organization_id' => $organization->id,
            'name' => 'General',
            'description' => 'Cualificación predeterminada para el curso.',
            'template' => true,
        ]);

        $cualificacion->skills()->attach($skill1, ['percentage' => 100]);
        $cualificacion->skills()->attach($skill2, ['percentage' => 100]);
        $cualificacion->skills()->attach($skill3, ['percentage' => 100]);
        $cualificacion->skills()->attach($skill4, ['percentage' => 100]);
        $cualificacion->skills()->attach($skill5, ['percentage' => 100]);
        $cualificacion->skills()->attach($skill6, ['percentage' => 100]);
        $cualificacion->skills()->attach($skill7, ['percentage' => 100]);
        $cualificacion->skills()->attach($skill8, ['percentage' => 100]);
        $cualificacion->skills()->attach($skill9, ['percentage' => 100]);

        $curso = Curso::whereHas('category.period.organization', function ($query) use ($slug) {
            $query->where('organizations.slug', $slug);
        })
            ->where('slug', 'programacion')
            ->first();

        $curso->qualification()->associate($cualificacion);
        $curso->save();

        // Unidades

        $this->generarCualificacionUnidad($organization, $skill1);
        $this->generarCualificacionUnidad($organization, $skill2);
        $this->generarCualificacionUnidad($organization, $skill3);
        $this->generarCualificacionUnidad($organization, $skill4);
        $this->generarCualificacionUnidad($organization, $skill5);
        $this->generarCualificacionUnidad($organization, $skill6);
        $this->generarCualificacionUnidad($organization, $skill7);
        $this->generarCualificacionUnidad($organization, $skill8);
        $this->generarCualificacionUnidad($organization, $skill9);
    }
}
