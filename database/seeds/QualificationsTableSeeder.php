<?php

use App\Curso;
use App\Qualification;
use App\Skill;
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
        $ce1 = factory(Skill::class)->create([
            'name' => 'CE1',
            'description' => 'Especificar, diseñar e implementar algoritmos en un lenguaje de programación, utilizando métodos eficientes, sistemáticos y organizados de resolución de problemas.',
        ]);

        $ce2 = factory(Skill::class)->create([
            'name' => 'CE2',
            'description' => 'Escribir correctamente, compilar y ejecutar programas en un lenguaje de alto nivel.',
        ]);

        $ce3 = factory(Skill::class)->create([
            'name' => 'CE3',
            'description' => 'Conocer y dominar estructuras básicas fundamentales utilizadas en la programación, tanto estructuras de datos como estructuras de control del flujo del programa.',
        ]);

        $cualificacion = factory(Qualification::class)->create([
            'name' => 'General',
            'description' => 'Cualificación predeterminada para el curso.',
        ]);

        $cualificacion->skills()->attach($ce1, ['percentage' => 20]);
        $cualificacion->skills()->attach($ce2, ['percentage' => 40]);
        $cualificacion->skills()->attach($ce3, ['percentage' => 40]);

        $curso = Curso::find(1);
        $curso->qualification()->associate($cualificacion);
        $curso->save();
    }
}
