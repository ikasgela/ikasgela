<?php

namespace Tests\Feature\Profesor;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Override;
use Tests\TestCase;

class ProfesorControllerTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testIndex()
    {
        // Auth
        $this->actingAs($this->profesor);

        // When
        $response = $this->get(route('profesor.index'));

        // Then
        $response->assertSuccessful();
    }

    public function testNotAdminProfesorNotIndex()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // When
        $response = $this->get(route('profesor.index'));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotIndex()
    {
        // When
        $response = $this->get(route('profesor.index'));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testTareas()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $alumno = User::factory()->create();

        // When
        $response = $this->get(route('profesor.tareas', $alumno));

        // Then
        $response->assertSuccessful();
    }

    public function testNotAdminProfesorNotTareas()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $alumno = User::factory()->create();

        // When
        $response = $this->get(route('profesor.tareas', $alumno));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotTareas()
    {
        // Given
        $alumno = User::factory()->create();

        // When
        $response = $this->get(route('profesor.tareas', $alumno));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testRevisar()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given - create original actividad so feedbacks_actividad is a Collection, not array
        $original = Actividad::factory()->create();
        $actividad = Actividad::factory()->create(['plantilla_id' => $original->id]);
        $tarea = Tarea::factory()->create(['user_id' => $this->alumno->id, 'actividad_id' => $actividad->id]);

        // When
        $response = $this->get(route('profesor.revisar', [$this->alumno, $tarea]));

        // Then
        $response->assertSuccessful();
    }

    public function testNotAdminProfesorNotRevisar()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $tarea = Tarea::factory()->create(['user_id' => $this->alumno->id]);

        // When
        $response = $this->get(route('profesor.revisar', [$this->alumno, $tarea]));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotRevisar()
    {
        // Given
        $tarea = Tarea::factory()->create();

        // When
        $response = $this->get(route('profesor.revisar', [$tarea->user, $tarea]));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testEditNotaManual()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $user = User::factory()->create();
        $curso = Curso::factory()->create();
        $curso->users()->attach($user, ['nota' => 8]);

        // When
        $response = $this->get(route('profesor.nota_manual.edit', [$user, $curso]));

        // Then
        $response->assertSuccessful();
    }

    public function testNotAdminProfesorNotEditNotaManual()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $user = User::factory()->create();
        $curso = Curso::factory()->create();
        $curso->users()->attach($user, ['nota' => 0]);

        // When
        $response = $this->get(route('profesor.nota_manual.edit', [$user, $curso]));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEditNotaManual()
    {
        // Given
        $user = User::factory()->create();
        $curso = Curso::factory()->create();

        // When
        $response = $this->get(route('profesor.nota_manual.edit', [$user, $curso]));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdateNotaManual()
    {
        // Auth
        $this->actingAs($this->admin);

        // Given
        $user = User::factory()->create();
        $curso = Curso::factory()->create();
        $curso->users()->attach($user, ['nota' => 0]);

        // When
        $response = $this->post(route('profesor.nota_manual.update', [$user, $curso]), [
            'nota' => 9,
        ]);

        // Then
        $response->assertRedirect();
    }

    public function testNotAdminProfesorNotUpdateNotaManual()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $user = User::factory()->create();
        $curso = Curso::factory()->create();

        // When
        $response = $this->post(route('profesor.nota_manual.update', [$user, $curso]), [
            'nota' => 9,
        ]);

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdateNotaManual()
    {
        // Given
        $user = User::factory()->create();
        $curso = Curso::factory()->create();

        // When
        $response = $this->post(route('profesor.nota_manual.update', [$user, $curso]), [
            'nota' => 9,
        ]);

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testAsignarTarea()
    {
        // Auth
        $this->actingAs($this->profesor);
        Mail::fake();

        // Given: set up curso_actual for the authenticated professor
        $curso = Curso::factory()->create(['silence_notifications' => true]);
        $this->profesor->cursos()->syncWithoutDetaching($curso);
        setting_usuario(['curso_actual' => $curso->id]);

        // Create a plantilla actividad in that curso
        $actividad = Actividad::factory()->create([
            'plantilla' => true,
            'unidad_id' => \App\Models\Unidad::factory()->create(['curso_id' => $curso->id])->id,
        ]);

        // Target alumno
        $alumno = User::factory()->create();
        $alumno->cursos()->syncWithoutDetaching($curso);

        // When
        $response = $this->post(route('profesor.asignar_tarea', $alumno), [
            'seleccionadas' => [$actividad->id],
        ]);

        // Then
        $response->assertRedirect();
    }

    public function testAsignarTareasGrupo()
    {
        // Auth
        $this->actingAs($this->profesor);
        Mail::fake();

        // Given
        $curso = Curso::factory()->create(['silence_notifications' => true]);
        $this->profesor->cursos()->syncWithoutDetaching($curso);
        setting_usuario(['curso_actual' => $curso->id]);

        $actividad = Actividad::factory()->create([
            'plantilla' => true,
            'unidad_id' => \App\Models\Unidad::factory()->create(['curso_id' => $curso->id])->id,
        ]);

        $alumno = User::factory()->create();
        $alumno->cursos()->syncWithoutDetaching($curso);

        // When
        $response = $this->post(route('profesor.asignar_tareas_grupo'), [
            'usuarios_seleccionados' => [$alumno->id],
            'seleccionadas' => [$actividad->id],
        ]);

        // Then
        $response->assertRedirect();
    }

    public function testAsignarTareasEquipo()
    {
        $this->actingAs($this->profesor);
        Mail::fake();

        $curso = Curso::factory()->create(['silence_notifications' => true]);
        $this->profesor->cursos()->syncWithoutDetaching($curso);
        setting_usuario(['curso_actual' => $curso->id]);

        $actividad = Actividad::factory()->create([
            'plantilla' => true,
            'unidad_id' => \App\Models\Unidad::factory()->create(['curso_id' => $curso->id])->id,
        ]);

        $group = \App\Models\Group::factory()->create();
        $team = \App\Models\Team::factory()->create(['group_id' => $group->id]);

        $alumno = User::factory()->create();
        $team->users()->attach($alumno);

        $response = $this->post(route('profesor.asignar_tareas_equipo'), [
            'equipos_seleccionados' => [$team->id],
            'seleccionadas' => [$actividad->id],
        ]);

        $response->assertRedirect();
    }

    public function testAsignarTareaEquipo()
    {
        $this->actingAs($this->profesor);
        Mail::fake();

        $curso = Curso::factory()->create(['silence_notifications' => true]);
        $this->profesor->cursos()->syncWithoutDetaching($curso);
        setting_usuario(['curso_actual' => $curso->id]);

        $actividad = Actividad::factory()->create([
            'plantilla' => true,
            'unidad_id' => \App\Models\Unidad::factory()->create(['curso_id' => $curso->id])->id,
        ]);

        $group = \App\Models\Group::factory()->create();
        $team = \App\Models\Team::factory()->create(['group_id' => $group->id]);

        $alumno = User::factory()->create();
        $team->users()->attach($alumno);

        $response = $this->post(route('profesor.asignar_tarea_equipo', $team), [
            'seleccionadas' => [$actividad->id],
        ]);

        $response->assertRedirect();
    }

    public function testJplag()
    {
        $this->actingAs($this->profesor);

        $tarea = Tarea::factory()->create(['estado' => 30]);

        $response = $this->get(route('profesor.jplag', $tarea));

        $response->assertRedirect();
    }

    public function testIndexWithFiltros()
    {
        $this->actingAs($this->profesor);

        // Given
        $curso = Curso::factory()->create();
        $this->profesor->cursos()->syncWithoutDetaching($curso);
        setting_usuario(['curso_actual' => $curso->id]);

        // When - test with multiple filter combinations
        $response = $this->get(route('profesor.index', [
            'filtro_alumnos' => 'R',
            'filtro_alumnos_bloqueados' => 'B',
            'filtro_actividades_examen' => 'E',
        ]));

        // Then
        $response->assertSuccessful();
        $this->assertEquals('R', session('profesor_filtro_alumnos'));
        $this->assertEquals('B', session('profesor_filtro_alumnos_bloqueados'));
        $this->assertEquals('E', session('profesor_filtro_actividades_examen'));
    }

    public function testIndexToggleFiltros()
    {
        $this->actingAs($this->profesor);

        // Given
        $curso = Curso::factory()->create();
        $this->profesor->cursos()->syncWithoutDetaching($curso);
        setting_usuario(['curso_actual' => $curso->id]);

        // When - test toggling filters (turning them off)
        $response = $this->get(route('profesor.index', [
            'filtro_alumnos_bloqueados' => 'B',
        ]));
        $response = $this->get(route('profesor.index', [
            'filtro_alumnos_bloqueados' => 'B', // toggle off
        ]));

        // Then
        $response->assertSuccessful();
        $this->assertEquals('', session('profesor_filtro_alumnos_bloqueados'));
    }

    public function testIndexWithUserIdFilter()
    {
        $this->actingAs($this->profesor);

        // Given
        $curso = Curso::factory()->create();
        $this->profesor->cursos()->syncWithoutDetaching($curso);
        $alumno = User::factory()->create();
        $alumno->cursos()->syncWithoutDetaching($curso);
        setting_usuario(['curso_actual' => $curso->id]);

        // When
        $response = $this->get(route('profesor.index', ['user_id' => $alumno->id]));

        // Then
        $response->assertSuccessful();
        $this->assertEquals($alumno->id, session('filtrar_user_actual'));
    }

    public function testIndexWithEtiquetas()
    {
        $this->actingAs($this->profesor);

        // Given
        $curso = Curso::factory()->create();
        $this->profesor->cursos()->syncWithoutDetaching($curso);
        setting_usuario(['curso_actual' => $curso->id]);

        // When - test clearing tags
        $response = $this->get(route('profesor.index', ['filtro_etiquetas' => 'N']));

        // Then
        $response->assertSuccessful();
        $this->assertEquals('', session('profesor_filtro_etiquetas'));
        $this->assertEquals([], session('tags_usuario'));
    }

    public function testJplagDownload()
    {
        $this->actingAs($this->profesor);

        // Given
        $tarea = Tarea::factory()->create(['estado' => 30]);

        // When
        $response = $this->get(route('profesor.jplag_download', $tarea));

        // Then - should redirect with message
        $response->assertRedirect();
    }

    public function testAsignarTareaConFechaOverride()
    {
        $this->actingAs($this->profesor);
        Mail::fake();

        $curso = Curso::factory()->create(['silence_notifications' => true]);
        $this->profesor->cursos()->syncWithoutDetaching($curso);
        setting_usuario(['curso_actual' => $curso->id]);

        $actividad = Actividad::factory()->create([
            'plantilla' => true,
            'unidad_id' => \App\Models\Unidad::factory()->create(['curso_id' => $curso->id])->id,
        ]);

        $alumno = User::factory()->create();
        $alumno->cursos()->syncWithoutDetaching($curso);

        // Provide fecha_override_enable to trigger the override branch (line 383)
        $response = $this->post(route('profesor.asignar_tarea', $alumno), [
            'seleccionadas' => [$actividad->id],
            'fecha_override_enable' => true,
            'fecha_override' => now()->addDays(7)->format('Y-m-d'),
        ]);

        $response->assertRedirect();
    }

    public function testAsignarTareaConNotificacion()
    {
        $this->actingAs($this->profesor);
        Mail::fake();

        // silence_notifications = false so that the Mail::queue path is reachable
        $curso = Curso::factory()->create(['silence_notifications' => false]);
        $this->profesor->cursos()->syncWithoutDetaching($curso);
        setting_usuario(['curso_actual' => $curso->id]);

        $actividad = Actividad::factory()->create([
            'plantilla' => true,
            'unidad_id' => \App\Models\Unidad::factory()->create(['curso_id' => $curso->id])->id,
        ]);

        $alumno = User::factory()->create();
        $alumno->cursos()->syncWithoutDetaching($curso);
        // Enable the user notification setting
        setting_usuario(['notificacion_actividad_asignada' => true], $alumno);

        // Pass notificar=1 to trigger Mail::queue (line 409)
        $response = $this->post(route('profesor.asignar_tarea', $alumno), [
            'seleccionadas' => [$actividad->id],
            'notificar' => true,
        ]);

        $response->assertRedirect();
        Mail::assertQueued(\App\Mail\ActividadAsignada::class);
    }

    public function testAsignarTareasEquipoConFechaOverride()
    {
        $this->actingAs($this->profesor);
        Mail::fake();

        $curso = Curso::factory()->create(['silence_notifications' => true]);
        $this->profesor->cursos()->syncWithoutDetaching($curso);
        setting_usuario(['curso_actual' => $curso->id]);

        $actividad = Actividad::factory()->create([
            'plantilla' => true,
            'unidad_id' => \App\Models\Unidad::factory()->create(['curso_id' => $curso->id])->id,
        ]);

        $group = \App\Models\Group::factory()->create();
        $team = \App\Models\Team::factory()->create(['group_id' => $group->id]);
        $alumno = User::factory()->create();
        $alumno->cursos()->syncWithoutDetaching($curso);
        $team->users()->attach($alumno);

        $response = $this->post(route('profesor.asignar_tarea_equipo', $team), [
            'seleccionadas' => [$actividad->id],
            'fecha_override_enable' => true,
            'fecha_override' => now()->addDays(7)->format('Y-m-d'),
        ]);

        $response->assertRedirect();
    }

    public function testAsignarTareasEquipoConNotificacion()
    {
        $this->actingAs($this->profesor);
        Mail::fake();

        $curso = Curso::factory()->create(['silence_notifications' => false]);
        $this->profesor->cursos()->syncWithoutDetaching($curso);
        setting_usuario(['curso_actual' => $curso->id]);

        $actividad = Actividad::factory()->create([
            'plantilla' => true,
            'unidad_id' => \App\Models\Unidad::factory()->create(['curso_id' => $curso->id])->id,
        ]);

        $group = \App\Models\Group::factory()->create();
        $team = \App\Models\Team::factory()->create(['group_id' => $group->id]);
        $alumno = User::factory()->create();
        $alumno->cursos()->syncWithoutDetaching($curso);
        $team->users()->attach($alumno);
        setting_usuario(['notificacion_actividad_asignada' => true], $alumno);

        $response = $this->post(route('profesor.asignar_tarea_equipo', $team), [
            'seleccionadas' => [$actividad->id],
            'notificar' => true,
        ]);

        $response->assertRedirect();
        Mail::assertQueued(\App\Mail\ActividadAsignada::class);
    }
}
