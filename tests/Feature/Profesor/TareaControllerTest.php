<?php

namespace Tests\Feature\Profesor;

use App\Models\Curso;
use App\Models\Tarea;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Override;
use Tests\TestCase;

class TareaControllerTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    public function testEdit()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $tarea = Tarea::factory()->create();

        // When
        $response = $this->get(route('tareas.edit', $tarea));

        // Then
        $response->assertSuccessful()->assertSeeInOrder([__('Edit task'), __('Save')]);
    }

    public function testNotAdminProfesorNotEdit()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $tarea = Tarea::factory()->create();

        // When
        $response = $this->get(route('tareas.edit', $tarea));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotEdit()
    {
        // Auth
        // Given
        $tarea = Tarea::factory()->create();

        // When
        $response = $this->get(route('tareas.edit', $tarea));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testUpdate()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $tarea = Tarea::factory()->create(['estado' => 10]);

        // When
        $response = $this->put(route('tareas.update', $tarea), ['estado' => 20]);

        // Then
        $this->assertDatabaseHas('tareas', ['id' => $tarea->id, 'estado' => 20]);
    }

    public function testUpdateRequiresEstado()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $tarea = Tarea::factory()->create();

        // When
        $response = $this->put(route('tareas.update', $tarea), ['estado' => null]);

        // Then
        $response->assertSessionHasErrors('estado');
    }

    public function testNotAdminProfesorNotUpdate()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $tarea = Tarea::factory()->create();

        // When
        $response = $this->put(route('tareas.update', $tarea), ['estado' => 10]);

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotUpdate()
    {
        // Auth
        // Given
        $tarea = Tarea::factory()->create();

        // When
        $response = $this->put(route('tareas.update', $tarea), ['estado' => 10]);

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testDestroy()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);
        $tarea = Tarea::factory()->create();

        // When
        $this->delete(route('tareas.destroy', [$tarea->user, $tarea]));

        // Then
        $this->assertSoftDeleted('tareas', ['id' => $tarea->id]);
    }

    public function testNotAdminProfesorNotDestroy()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $tarea = Tarea::factory()->create();

        // When
        $response = $this->delete(route('tareas.destroy', [$tarea->user, $tarea]));

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotDestroy()
    {
        // Auth
        // Given
        $tarea = Tarea::factory()->create();

        // When
        $response = $this->delete(route('tareas.destroy', [$tarea->user, $tarea]));

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testBorrarMultiple()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);
        $tarea1 = Tarea::factory()->create(['user_id' => $this->alumno->id]);
        $tarea2 = Tarea::factory()->create(['user_id' => $this->alumno->id]);

        // When
        $this->delete(route('tareas.borrar_multiple', $this->alumno), ['asignadas' => [$tarea1->id, $tarea2->id]]);

        // Then
        $this->assertSoftDeleted('tareas', ['id' => $tarea1->id]);
        $this->assertSoftDeleted('tareas', ['id' => $tarea2->id]);
    }

    public function testBorrarMultipleRequiresAsignadas()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $tarea = Tarea::factory()->create(['user_id' => $this->alumno->id]);

        // When
        $response = $this->delete(route('tareas.borrar_multiple', $this->alumno), ['asignadas' => null]);

        // Then
        $response->assertSessionHasErrors('asignadas');
    }

    public function testNotAdminProfesorNotBorrarMultiple()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $tarea = Tarea::factory()->create();

        // When
        $response = $this->delete(route('tareas.borrar_multiple', $tarea->user), ['asignadas' => [$tarea->id]]);

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotBorrarMultiple()
    {
        // Auth
        // Given
        $tarea = Tarea::factory()->create();

        // When
        $response = $this->delete(route('tareas.borrar_multiple', $tarea->user), ['asignadas' => [$tarea->id]]);

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testFechaFinalizacionMultiple()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);
        $tarea = Tarea::factory()->create(['user_id' => $this->alumno->id]);

        // When
        $response = $this->post(route('tareas.fecha_finalizacion_multiple', $this->alumno), [
            'asignadas' => [$tarea->id],
            'fecha_override' => now()->addDays(5)->format('Y-m-d H:i:s'),
        ]);

        // Then
        $response->assertRedirect(route('profesor.tareas', ['user' => $this->alumno->id]));
    }

    public function testFechaFinalizacionMultipleRequiresAsignadas()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $tarea = Tarea::factory()->create(['user_id' => $this->alumno->id]);

        // When
        $response = $this->post(route('tareas.fecha_finalizacion_multiple', $this->alumno), [
            'asignadas' => null,
            'fecha_override' => now()->addDays(5)->format('Y-m-d H:i:s'),
        ]);

        // Then
        $response->assertSessionHasErrors('asignadas');
    }

    public function testNotAdminProfesorNotFechaFinalizacionMultiple()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $tarea = Tarea::factory()->create(['user_id' => $this->alumno->id]);

        // When
        $response = $this->post(route('tareas.fecha_finalizacion_multiple', $this->alumno), [
            'asignadas' => [$tarea->id],
            'fecha_override' => now()->addDays(5)->format('Y-m-d H:i:s'),
        ]);

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotFechaFinalizacionMultiple()
    {
        // Auth
        // Given
        $tarea = Tarea::factory()->create(['user_id' => $this->alumno->id]);

        // When
        $response = $this->post(route('tareas.fecha_finalizacion_multiple', $this->alumno), [
            'asignadas' => [$tarea->id],
            'fecha_override' => now()->addDays(5)->format('Y-m-d H:i:s'),
        ]);

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testBorrarMultipleActivas()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);
        $tarea1 = Tarea::factory()->create(['user_id' => $this->alumno->id]);
        $tarea2 = Tarea::factory()->create(['user_id' => $this->alumno->id]);

        // When
        $response = $this->delete(route('tareas.borrar_multiple_activas'), ['asignadas' => [$tarea1->id, $tarea2->id]]);

        // Then
        $this->assertSoftDeleted('tareas', ['id' => $tarea1->id]);
        $this->assertSoftDeleted('tareas', ['id' => $tarea2->id]);
        $response->assertRedirect(route('profesor.index'));
    }

    public function testBorrarMultipleActivasRequiresAsignadas()
    {
        // Auth
        $this->actingAs($this->profesor);

        // When
        $response = $this->delete(route('tareas.borrar_multiple_activas'), ['asignadas' => null]);

        // Then
        $response->assertSessionHasErrors('asignadas');
    }

    public function testNotAdminProfesorNotBorrarMultipleActivas()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $tarea = Tarea::factory()->create();

        // When
        $response = $this->delete(route('tareas.borrar_multiple_activas'), ['asignadas' => [$tarea->id]]);

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotBorrarMultipleActivas()
    {
        // Auth
        // Given
        $tarea = Tarea::factory()->create();

        // When
        $response = $this->delete(route('tareas.borrar_multiple_activas'), ['asignadas' => [$tarea->id]]);

        // Then
        $response->assertRedirect(route('login'));
    }

    public function testFechaFinalizacionMultipleActivas()
    {
        // Auth
        $this->actingAs($this->profesor);

        // Given
        $curso = Curso::factory()->create();
        setting_usuario(['curso_actual' => $curso->id]);
        $tarea = Tarea::factory()->create(['user_id' => $this->alumno->id]);

        // When
        $response = $this->post(route('tareas.fecha_finalizacion_multiple_activas'), [
            'asignadas' => [$tarea->id],
            'fecha_override' => now()->addDays(5)->format('Y-m-d H:i:s'),
        ]);

        // Then
        $response->assertRedirect(route('profesor.index'));
    }

    public function testFechaFinalizacionMultipleActivasRequiresAsignadas()
    {
        // Auth
        $this->actingAs($this->profesor);

        // When
        $response = $this->post(route('tareas.fecha_finalizacion_multiple_activas'), [
            'asignadas' => null,
            'fecha_override' => now()->addDays(5)->format('Y-m-d H:i:s'),
        ]);

        // Then
        $response->assertSessionHasErrors('asignadas');
    }

    public function testNotAdminProfesorNotFechaFinalizacionMultipleActivas()
    {
        // Auth
        $this->actingAs($this->not_admin_profesor);

        // Given
        $tarea = Tarea::factory()->create(['user_id' => $this->alumno->id]);

        // When
        $response = $this->post(route('tareas.fecha_finalizacion_multiple_activas'), [
            'asignadas' => [$tarea->id],
            'fecha_override' => now()->addDays(5)->format('Y-m-d H:i:s'),
        ]);

        // Then
        $response->assertForbidden();
    }

    public function testNotAuthNotFechaFinalizacionMultipleActivas()
    {
        // Auth
        // Given
        $tarea = Tarea::factory()->create(['user_id' => $this->alumno->id]);

        // When
        $response = $this->post(route('tareas.fecha_finalizacion_multiple_activas'), [
            'asignadas' => [$tarea->id],
            'fecha_override' => now()->addDays(5)->format('Y-m-d H:i:s'),
        ]);

        // Then
        $response->assertRedirect(route('login'));
    }
}
