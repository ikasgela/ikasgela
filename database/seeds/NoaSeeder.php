<?php

use App\Actividad;
use App\Curso;
use App\Qualification;
use App\Registro;
use App\Skill;
use App\Tarea;
use App\Unidad;
use App\User;
use Illuminate\Database\Seeder;

class NoaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Usuario
        $user = User::where('email', 'noa@ikasgela.com')->first();

        // Curso
        $curso = Curso::whereHas('category.period.organization', function ($query) {
            $query->where('organizations.slug', 'ikasgela');
        })
            ->where('slug', 'programacion')
            ->first();

        setting_usuario(['curso_actual' => $curso->id], $user);

        // Organización
        $organization = $curso->category->period->organization;

        // Competencias
        $competencia1 = factory(Skill::class)->create([
            'organization_id' => $organization->id,
            'name' => 'Diseño de algoritmos',
            'peso_examen' => 40,
        ]);

        $competencia2 = factory(Skill::class)->create([
            'organization_id' => $organization->id,
            'name' => 'Programación estructurada',
            'peso_examen' => 40,
        ]);

        $cualificacion1 = factory(Qualification::class)->create([
            'organization_id' => $organization->id,
            'name' => 'General',
            'template' => true,
        ]);

        $cualificacion1->skills()->attach($competencia1, ['percentage' => 20]);
        $cualificacion1->skills()->attach($competencia2, ['percentage' => 80]);

        $curso->qualification()->associate($cualificacion1);
        $curso->save();

        // Actividades
        $unidad1 = factory(Unidad::class)->create([
            'curso_id' => $curso->id,
            'nombre' => 'U1',
        ]);

        $actividad1 = factory(Actividad::class)->create([
            'unidad_id' => $unidad1->id,
            'puntuacion' => 100,
            'tags' => 'base',
            'plantilla' => true,
        ]);

        $tarea1 = $actividad1->duplicate();

        $user->actividades()->attach($tarea1, [
            'estado' => 60,
            'puntuacion' => 100,
        ]);

        $unidad2 = factory(Unidad::class)->create([
            'curso_id' => $curso->id,
            'nombre' => 'U2',
        ]);

        $actividad2 = factory(Actividad::class)->create([
            'unidad_id' => $unidad2->id,
            'puntuacion' => 100,
            'tags' => 'base',
            'plantilla' => true,
        ]);

        $tarea2 = $actividad2->duplicate();

        $user->actividades()->attach($tarea2, [
            'estado' => 60,
            'puntuacion' => 80,
        ]);

        $unidad3 = factory(Unidad::class)->create([
            'curso_id' => $curso->id,
            'tags' => 'examen',
            'nombre' => 'S1y2',
        ]);

        $actividad3 = factory(Actividad::class)->create([
            'unidad_id' => $unidad3->id,
            'puntuacion' => 100,
            'multiplicador' => '2',
            'tags' => 'examen',
            'plantilla' => true,
        ]);

        $tarea3 = $actividad3->duplicate();

        $user->actividades()->attach($tarea3, [
            'estado' => 60,
            'puntuacion' => 40,
        ]);

        factory(Registro::class)->create([
            'user_id' => $user->id,
            'tarea_id' => Tarea::max('id'),
            'estado' => 30,
            'timestamp' => now()->addDays(-2),
        ]);

        $unidad4 = factory(Unidad::class)->create([
            'curso_id' => $curso->id,
            'nombre' => 'U3',
        ]);

        for ($i = 0; $i < 3; $i++) {
            $actividad4 = factory(Actividad::class)->create([
                'unidad_id' => $unidad4->id,
                'puntuacion' => 100,
                'tags' => 'base',
                'plantilla' => true,
            ]);
            $tarea4 = $actividad4->duplicate();
            $user->actividades()->attach($tarea4, [
                'estado' => 60,
                'puntuacion' => 50,
            ]);
        }

        $actividad5 = factory(Actividad::class)->create([
            'unidad_id' => $unidad4->id,
            'puntuacion' => 100,
            'tags' => 'base',
            'plantilla' => true,
        ]);

        $tarea5 = $actividad5->duplicate();

        $user->actividades()->attach($tarea5, [
            'estado' => 20,
        ]);
    }
}
