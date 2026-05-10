<?php

namespace Tests\Browser\Sitio;

use App\Models\Actividad;
use App\Models\Tarea;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\BrowserUiHelpers;
use Tests\DuskTestCase;

/**
 * Flujo completo "Send again":
 * 1. Se crea una actividad de prueba vía factory (independiente de seeds, que T3 elimina).
 * 2. Se asigna a marc directamente en BD con estado=20 (aceptada).
 * 3. Marc envía para revisión (estado 20 → 30).
 * 4. Lucia revisa y devuelve con "Send again" (estado 30 → 41).
 * 5. Marc lee el feedback (estado 41 → 21).
 * 6. Se limpian los datos de prueba.
 */
class T6_ProfesorFlujosTest extends DuskTestCase
{
    use BrowserUiHelpers;

    private static ?int $actividadId = null;
    private static ?int $tareaId = null;
    private const TEST_ACTIVIDAD_NOMBRE = '__Dusk_T6__';

    public function testSetupDatos(): void
    {
        // Limpiar posibles restos de ejecuciones anteriores fallidas
        Actividad::where('nombre', self::TEST_ACTIVIDAD_NOMBRE)->delete();

        $alumno = User::where('email', 'marc@ikasgela.com')->firstOrFail();

        $actividad = Actividad::factory()->create([
            'unidad_id' => 1,
            'nombre'    => self::TEST_ACTIVIDAD_NOMBRE,
            'auto_avance' => false,
        ]);
        static::$actividadId = $actividad->id;

        // Asignar directamente en BD con estado=20 (aceptada) — sin browser
        $alumno->actividades()->attach($actividad, ['estado' => 20]);

        $tarea = Tarea::where('user_id', $alumno->id)
            ->where('actividad_id', $actividad->id)
            ->firstOrFail();
        static::$tareaId = $tarea->id;
    }

    public function testLogin(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAs($browser, 'marc@ikasgela.com', '12345Abcde', 'users.home');
        });
    }

    public function testAlumnoEnviaParaRevision(): void
    {
        $this->assertNotNull(static::$actividadId, 'testSetupDatos no creó la actividad');

        $this->browse(function (Browser $browser) {
            $browser->visit(route('users.home'));
            $this->assertNoAppErrors($browser);

            $browser->assertSee(self::TEST_ACTIVIDAD_NOMBRE);
            $browser->press(__('Submit for review'));
            $browser->waitFor('div#single-click-confirm-modal');
            $browser->press(__('Confirm'));

            $browser->assertRouteIs('users.home');
            $this->assertNoAppErrors($browser);
            $browser->logout();
        });

        $this->assertDatabaseHas('tareas', ['id' => static::$tareaId, 'estado' => 30]);
    }

    public function testProfesorRevisaYEnviaDeNuevo(): void
    {
        $this->assertNotNull(static::$tareaId, 'testSetupDatos no guardó el ID de tarea');

        $alumno = User::where('email', 'marc@ikasgela.com')->firstOrFail();
        $tarea  = Tarea::findOrFail(static::$tareaId);

        $this->browse(function (Browser $browser) use ($alumno, $tarea) {
            $this->loginAs($browser, 'lucia@ikasgela.com', '12345Abcde', 'profesor.index');

            $browser->visit(route('profesor.revisar', [$alumno, $tarea]));
            $this->assertNoAppErrors($browser);
            $browser->assertSee(__('Send again'));

            $browser->type('puntuacion', '50');
            $browser->press(__('Send again'));

            $browser->assertRouteIs('profesor.tareas', ['user' => $alumno->id]);
            $this->assertNoAppErrors($browser);
            $browser->logout();
        });

        $this->assertDatabaseHas('tareas', ['id' => static::$tareaId, 'estado' => 41]);
    }

    public function testAlumnoLeeFeedback(): void
    {
        $this->assertNotNull(static::$tareaId, 'testSetupDatos no guardó el ID de tarea');

        $this->browse(function (Browser $browser) {
            $this->loginAs($browser, 'marc@ikasgela.com', '12345Abcde', 'users.home');

            $browser->visit(route('users.home'));
            $this->assertNoAppErrors($browser);

            // Estado 41: aparece "Feedback read" (sin modal de confirmación)
            $browser->assertSee(self::TEST_ACTIVIDAD_NOMBRE);
            $browser->press(__('Feedback read'));

            $browser->assertRouteIs('users.home');
            $this->assertNoAppErrors($browser);
            $browser->logout();
        });

        $this->assertDatabaseHas('tareas', ['id' => static::$tareaId, 'estado' => 21]);
    }

    public function testLogout(): void
    {
        $this->browse(function (Browser $browser) {
            $this->logoutToPortada($browser);
        });
    }

    public function testTeardown(): void
    {
        if (static::$actividadId) {
            Actividad::find(static::$actividadId)?->delete();
            static::$actividadId = null;
            static::$tareaId = null;
        }
    }
}
