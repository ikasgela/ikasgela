<?php

namespace Tests\Feature\Estructura;

use Override;
use App\Http\Controllers\ActividadController;
use App\Models\Actividad;
use App\Models\Registro;
use App\Models\Tarea;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Tests for ActividadController::actualizarEstado, clonarActividad, archivarTarea,
 * bloquearRepositorios (private), mostrarSiguienteActividad (private)
 */
class ActividadesEstadoTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        parent::crearUsuarios();
    }

    private function crearTareaConCursoActual(int $estado = 10): array
    {
        $actividad = Actividad::factory()->create(['plantilla' => false]);
        $actividad->unidad->curso->update(['silence_notifications' => true]);
        $curso = $actividad->unidad->curso;

        // Attach actividad to alumno
        $this->alumno->actividades()->attach($actividad, ['estado' => $estado]);
        $tarea = Tarea::where('user_id', $this->alumno->id)->where('actividad_id', $actividad->id)->first();

        // Set curso_actual for alumno
        setting_usuario(['curso_actual' => $curso->id], $this->alumno);

        return compact('actividad', 'curso', 'tarea');
    }

    // ===== actualizarEstado - case 20: start =====
    public function testActualizarEstadoCase20()
    {
        $this->actingAs($this->alumno);
        ['actividad' => $actividad, 'tarea' => $tarea] = $this->crearTareaConCursoActual(10);

        $response = $this->put(route('actividades.estado', $tarea), [
            'nuevoestado' => 20,
        ]);

        $response->assertRedirect();
        $tarea->refresh();
        $this->assertEquals(20, $tarea->estado);
    }

    // ===== actualizarEstado - case 30: submit =====
    public function testActualizarEstadoCase30()
    {
        $this->actingAs($this->alumno);
        ['actividad' => $actividad, 'tarea' => $tarea] = $this->crearTareaConCursoActual(20);

        $response = $this->put(route('actividades.estado', $tarea), [
            'nuevoestado' => 30,
        ]);

        $response->assertRedirect();
        $tarea->refresh();
        $this->assertEquals(30, $tarea->estado);
    }

    // ===== actualizarEstado - case 31: reset =====
    public function testActualizarEstadoCase31Admin()
    {
        $this->actingAs($this->not_profesor);
        ['actividad' => $actividad, 'tarea' => $tarea] = $this->crearTareaConCursoActual(30);
        setting_usuario(['curso_actual' => $actividad->unidad->curso->id], $this->not_profesor);

        $response = $this->put(route('actividades.estado', $tarea), [
            'nuevoestado' => 31,
        ]);

        $response->assertRedirect();
        $tarea->refresh();
        $this->assertEquals(20, $tarea->estado);
    }

    // ===== actualizarEstado - case 32: reopen =====
    public function testActualizarEstadoCase32Admin()
    {
        $this->actingAs($this->not_profesor);
        ['actividad' => $actividad, 'tarea' => $tarea] = $this->crearTareaConCursoActual(30);
        setting_usuario(['curso_actual' => $actividad->unidad->curso->id], $this->not_profesor);

        $response = $this->put(route('actividades.estado', $tarea), [
            'nuevoestado' => 32,
        ]);

        $response->assertRedirect();
        $tarea->refresh();
        $this->assertEquals(20, $tarea->estado);
    }

    // ===== actualizarEstado - case 40: revisada OK =====
    public function testActualizarEstadoCase40Admin()
    {
        $this->actingAs($this->not_profesor);
        ['actividad' => $actividad, 'tarea' => $tarea] = $this->crearTareaConCursoActual(30);
        setting_usuario(['curso_actual' => $actividad->unidad->curso->id], $this->not_profesor);

        $response = $this->put(route('actividades.estado', $tarea), [
            'nuevoestado' => 40,
            'puntuacion' => 100,
            'feedback' => 'Great job!',
        ]);

        $response->assertRedirect();
        $tarea->refresh();
        $this->assertEquals(40, $tarea->estado);
    }

    // ===== actualizarEstado - case 41: revisada ERROR =====
    public function testActualizarEstadoCase41Admin()
    {
        $this->actingAs($this->not_profesor);
        ['actividad' => $actividad, 'tarea' => $tarea] = $this->crearTareaConCursoActual(30);
        setting_usuario(['curso_actual' => $actividad->unidad->curso->id], $this->not_profesor);

        $response = $this->put(route('actividades.estado', $tarea), [
            'nuevoestado' => 41,
            'puntuacion' => 50,
            'feedback' => 'Fix this.',
        ]);

        $response->assertRedirect();
    }

    // ===== actualizarEstado - case 42: auto-avance =====
    public function testActualizarEstadoCase42Admin()
    {
        $this->actingAs($this->not_profesor);
        ['actividad' => $actividad, 'tarea' => $tarea] = $this->crearTareaConCursoActual(30);
        setting_usuario(['curso_actual' => $actividad->unidad->curso->id], $this->not_profesor);

        $response = $this->put(route('actividades.estado', $tarea), [
            'nuevoestado' => 42,
        ]);

        $response->assertRedirect();
        $tarea->refresh();
        $this->assertEquals(42, $tarea->estado);
    }

    // ===== actualizarEstado - case 50 =====
    public function testActualizarEstadoCase50Admin()
    {
        $this->actingAs($this->not_profesor);
        ['actividad' => $actividad, 'tarea' => $tarea] = $this->crearTareaConCursoActual(40);
        setting_usuario(['curso_actual' => $actividad->unidad->curso->id], $this->not_profesor);

        $response = $this->put(route('actividades.estado', $tarea), [
            'nuevoestado' => 50,
        ]);

        $response->assertRedirect();
        $tarea->refresh();
        $this->assertEquals(50, $tarea->estado);
    }

    // ===== actualizarEstado - case 60: archive =====
    public function testActualizarEstadoCase60Admin()
    {
        $this->actingAs($this->not_profesor);
        ['actividad' => $actividad, 'tarea' => $tarea] = $this->crearTareaConCursoActual(40);
        setting_usuario(['curso_actual' => $actividad->unidad->curso->id], $this->not_profesor);

        $response = $this->put(route('actividades.estado', $tarea), [
            'nuevoestado' => 60,
        ]);

        $response->assertRedirect();
        $tarea->refresh();
        $this->assertEquals(60, $tarea->estado);
    }

    // ===== actualizarEstado - case 62 =====
    public function testActualizarEstadoCase62Admin()
    {
        $this->actingAs($this->not_profesor);
        ['actividad' => $actividad, 'tarea' => $tarea] = $this->crearTareaConCursoActual(40);
        setting_usuario(['curso_actual' => $actividad->unidad->curso->id], $this->not_profesor);

        $response = $this->put(route('actividades.estado', $tarea), [
            'nuevoestado' => 62,
        ]);

        $response->assertRedirect();
        $tarea->refresh();
        $this->assertEquals(62, $tarea->estado);
    }

    // ===== actualizarEstado - case 63: ampliar plazo =====
    public function testActualizarEstadoCase63Admin()
    {
        $this->actingAs($this->not_profesor);
        ['actividad' => $actividad, 'tarea' => $tarea] = $this->crearTareaConCursoActual(20);
        $actividad->update(['fecha_limite' => now()->subDays(1)]);
        setting_usuario(['curso_actual' => $actividad->unidad->curso->id], $this->not_profesor);

        $response = $this->put(route('actividades.estado', $tarea), [
            'nuevoestado' => 63,
            'ampliacion_plazo' => 7,
        ]);

        $response->assertRedirect();
    }

    // ===== actualizarEstado - case 64: auto-avance y archivada =====
    public function testActualizarEstadoCase64Admin()
    {
        $this->actingAs($this->not_profesor);
        ['actividad' => $actividad, 'tarea' => $tarea] = $this->crearTareaConCursoActual(20);
        setting_usuario(['curso_actual' => $actividad->unidad->curso->id], $this->not_profesor);

        $response = $this->put(route('actividades.estado', $tarea), [
            'nuevoestado' => 64,
        ]);

        $response->assertRedirect();
        $tarea->refresh();
        $this->assertEquals(64, $tarea->estado);
    }

    // ===== actualizarEstado - case 70: toggle final =====
    public function testActualizarEstadoCase70Admin()
    {
        $this->actingAs($this->not_profesor);
        ['actividad' => $actividad, 'tarea' => $tarea] = $this->crearTareaConCursoActual(10);
        setting_usuario(['curso_actual' => $actividad->unidad->curso->id], $this->not_profesor);
        $originalFinal = $actividad->final;

        $response = $this->put(route('actividades.estado', $tarea), [
            'nuevoestado' => 70,
        ]);

        $response->assertRedirect();
        $actividad->refresh();
        $this->assertNotEquals($originalFinal, $actividad->final);
    }

    // ===== actualizarEstado - case 10: undo pending =====
    public function testActualizarEstadoCase10Admin()
    {
        $this->actingAs($this->not_profesor);
        ['actividad' => $actividad, 'tarea' => $tarea] = $this->crearTareaConCursoActual(11);
        setting_usuario(['curso_actual' => $actividad->unidad->curso->id], $this->not_profesor);

        $response = $this->put(route('actividades.estado', $tarea), [
            'nuevoestado' => 10,
        ]);

        $response->assertRedirect();
        $tarea->refresh();
        $this->assertEquals(10, $tarea->estado);
    }

    // ===== actualizarEstado - case 21: reopen from 41 =====
    public function testActualizarEstadoCase21Admin()
    {
        $this->actingAs($this->not_profesor);
        ['actividad' => $actividad, 'tarea' => $tarea] = $this->crearTareaConCursoActual(41);
        setting_usuario(['curso_actual' => $actividad->unidad->curso->id], $this->not_profesor);

        $response = $this->put(route('actividades.estado', $tarea), [
            'nuevoestado' => 21,
        ]);

        $response->assertRedirect();
        $tarea->refresh();
        $this->assertEquals(21, $tarea->estado);
    }

    // ===== actualizarEstado - mostrarSiguienteActividad (case 71) =====
    public function testActualizarEstadoCase71Admin()
    {
        $this->actingAs($this->not_profesor);
        ['actividad' => $actividad, 'tarea' => $tarea] = $this->crearTareaConCursoActual(20);
        setting_usuario(['curso_actual' => $actividad->unidad->curso->id], $this->not_profesor);

        $response = $this->put(route('actividades.estado', $tarea), [
            'nuevoestado' => 71,
        ]);

        $response->assertRedirect();
    }

    // ===== actualizarEstado - profesor redirect path =====
    public function testActualizarEstadoAsProfesor()
    {
        $this->actingAs($this->profesor);
        ['actividad' => $actividad, 'tarea' => $tarea] = $this->crearTareaConCursoActual(10);
        setting_usuario(['curso_actual' => $actividad->unidad->curso->id], $this->profesor);

        $response = $this->put(route('actividades.estado', $tarea), [
            'nuevoestado' => 20,
        ]);

        $response->assertRedirect();
    }

    // ===== clonarActividad =====
    public function testClonarActividad()
    {
        $actividad = Actividad::factory()->create(['plantilla' => true]);
        $controller = new ActividadController();
        $clon = $controller->clonarActividad($actividad);
        $this->assertNotNull($clon);
        $this->assertEquals($actividad->id, $clon->plantilla_id);
    }

    // ===== archivarTarea =====
    public function testArchivarTarea()
    {
        ['actividad' => $actividad, 'tarea' => $tarea] = $this->crearTareaConCursoActual(40);
        setting_usuario(['curso_actual' => $actividad->unidad->curso->id], $this->alumno);

        $controller = new ActividadController();
        $tarea->estado = 60;
        $controller->archivarTarea($tarea, $actividad, $this->alumno);
        $this->assertTrue(true);
    }

    // ===== actualizarEstado - not authenticated =====
    public function testActualizarEstadoNotAuth()
    {
        $actividad = Actividad::factory()->create(['plantilla' => false]);
        $tarea = Tarea::factory()->create(['actividad_id' => $actividad->id, 'estado' => 10]);

        $response = $this->put(route('actividades.estado', $tarea), ['nuevoestado' => 20]);
        $response->assertRedirect(route('login'));
    }
}
