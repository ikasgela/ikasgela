<?php

namespace Tests\Feature\Alumno;

use App\Actividad;
use App\Curso;
use App\Qualification;
use App\Skill;
use App\Unidad;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ResultTest extends TestCase
{
    use DatabaseTransactions;

    private $required = [
    ];

    public function setUp(): void
    {
        $this->markTestSkipped('Tests desactivados.');

        parent::setUp();
        parent::crearUsuarios();
    }

    /** @test */
    public function curso_aprobado()
    {
        // Auth
        $this->actingAs($this->alumno);

        // Given

        // Usuario
        $user = $this->alumno;

        // Curso
        $curso = factory(Curso::class)->create();

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
            'tags' => 'examen',
            'plantilla' => true,
        ]);

        $tarea3 = $actividad3->duplicate();

        $user->actividades()->attach($tarea3, [
            'estado' => 60,
            'puntuacion' => 80,
        ]);

        // When
        $response = $this->get(route('results.index'));

        // Then
        $response->assertSeeInOrder([
            __('Results'),
            __('Mandatory activities'),
            trans_choice('tasks.completed', 2),
            __('Assessment tests'),
            trans_choice('tasks.passed', 2),
            __('Calification'),
            '8,75',
            __('Continuous evaluation'),
            trans_choice('tasks.passed', 1),
        ]);
    }
}
