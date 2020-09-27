<?php

namespace Tests\Feature\Alumno;

use App\Actividad;
use App\Curso;
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
        //$this->markTestSkipped('Tests desactivados.');

        parent::setUp();
        parent::crearUsuarios();
    }

    /** @test */
    public function curso_aprobado()
    {
        // Auth
        $this->actingAs($this->alumno);

        // Given
        $user = $this->alumno;

        // Nuevo curso
        $curso = factory(Curso::class)->create();

        setting_usuario(['curso_actual' => $curso->id]);

        $unidad1 = factory(Unidad::class)->create([
            'curso_id' => $curso->id,
            'nombre' => 'U1',
        ]);

        $actividad1 = factory(Actividad::class)->create([
            'unidad_id' => $unidad1->id,
            'tags' => 'base',
            'nombre' => 'Superada 100',
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
            'tags' => 'base',
            'nombre' => 'Superada 80',
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
            'tags' => 'examen',
            'nombre' => 'Superada 60',
            'plantilla' => true,
        ]);

        $tarea3 = $actividad3->duplicate();

        $user->actividades()->attach($tarea3, [
            'estado' => 20,
            'puntuacion' => 80,
        ]);

        // When
        $response = $this->get(route('results.index'));

        // Then
        $response->assertSeeInOrder([
            __('Results'),
            __('Mandatory activities'),
            'Completadas',
            __('Assessment tests'),
            __('Calification'),
            __('Continuous evaluation'),
            trans_choice('tasks.passed', 1),
        ]);
    }
}
