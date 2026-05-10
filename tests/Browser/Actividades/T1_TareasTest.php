<?php

namespace Tests\Browser\Actividades;

use App\Models\Actividad;
use App\Models\Tarea;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\BrowserUiHelpers;
use Tests\DuskTestCase;

class T1_TareasTest extends DuskTestCase
{
    use BrowserUiHelpers;

    public function testLogin()
    {
        $this->browse(function (Browser $browser) {
            $this->loginAs($browser, 'marc@ikasgela.com', '12345Abcde', 'users.home');
        });
    }

    public function testAceptarTareaBienvenida()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('users.home'));
            $browser->assertSee('Tarea de bienvenida');
            $browser->press(__('Accept activity'));
            $browser->assertRouteIs('users.home');
        });
    }

    public function testArchivarTareaBienvenida()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('users.home'));
            $browser->assertSee('Primeros pasos');
            $browser->press(__('Archive'));
            $browser->assertRouteIs('users.home');
            $browser->assertSee(__('There are no activities in progress.'));
        });
    }

    public function testActividad()
    {
        $alumno = User::where('email', 'marc@ikasgela.com')->firstOrFail();
        $actividadAgenda = Actividad::where('plantilla', true)
            ->where('nombre', 'like', 'Agenda%')
            ->orderBy('id')
            ->firstOrFail();

        $this->browse(function (Browser $browser) {

            // Cerrar sesión
            $browser->logout();

            // Login de profesor
            $this->loginAs($browser, 'lucia@ikasgela.com', '12345Abcde', 'profesor.index');
        });

        $this->browse(function (Browser $browser) use ($alumno, $actividadAgenda) {
            $browser->visit(route('profesor.index'));
            $browser->pause(500)->check("usuarios_seleccionados[{$alumno->id}]");
            $browser->check("seleccionadas[{$actividadAgenda->id}]");

            $browser->press(__('Save assigment'));
            $browser->assertRouteIs('profesor.index');
            $browser->assertSee('Marc');
            $browser->assertSee('1');
        });

        $this->browse(function (Browser $browser) {
            // Cerrar sesión
            $browser->logout();

            // Login de alumno
            $this->loginAs($browser, 'marc@ikasgela.com', '12345Abcde', 'users.home');

            // Aceptar actividad
            $browser->assertSee('Agenda');

            $browser->press(__('Accept activity'));
            $browser->assertRouteIs('users.home');

            // Clonar el repositorio
            $browser->assertSee('Agenda de contactos con varias ventanas que comparten datos.');
            $browser->assertSee(__('Clone the project'));

            $browser->press('@clone-button');

            $browser->pause(1000);

            $browser->visit(route('users.home'));

            $browser->assertSee(__('Open in IntelliJ IDEA'));

            // Enviar para revisión
            $browser->press(__('Submit for review'));
            $browser->waitFor('div#single-click-confirm-modal');
            $browser->assertSee(__('Confirm'));
            $browser->press(__('Confirm'));

            $browser->assertRouteIs('users.home');

            // Aparece la siguiente actividad
            $browser->assertSee('Tres en raya');

            // Cerrar sesión
            $browser->logout();

            // Login de profesor
            $this->loginAs($browser, 'lucia@ikasgela.com', '12345Abcde', 'profesor.index');
        });

        $this->browse(function (Browser $browser) use ($alumno) {
            // Corregir la tarea y darla por terminada
            $tarea = Tarea::where('user_id', $alumno->id)
                ->where('estado', 30)
                ->latest('id')
                ->firstOrFail();

            $browser->visit(route('profesor.tareas', $alumno));
            $browser->visit(route('profesor.revisar', [$alumno, $tarea]));
            $browser->type('puntuacion', '80');
            $browser->press(__('Add'));
            $browser->press(__('Finished'));
            $browser->assertRouteIs('profesor.tareas', ['user' => $alumno->id]);
            $browser->assertSee('80');

            // Cerrar sesión
            $browser->logout();

            // Login de alumno
            $this->loginAs($browser, 'marc@ikasgela.com', '12345Abcde', 'users.home');

            // Aceptar actividad
            $browser->assertSee('Agenda');

            $browser->press(__('Archive'));
            $browser->assertRouteIs('users.home');

            // No hay más tareas
            $browser->assertSee('Tres en raya');
        });
    }

    public function testLogout()
    {
        $this->browse(function (Browser $browser) {
            $this->logoutToPortada($browser);
        });
    }
}
