<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class T4_TareasTest extends DuskTestCase
{
    public function testLogin()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('login'));
            $browser->type('email', 'marc@ikasgela.com');
            $browser->type('password', '12345Abcde');
            $browser->check('remember');
            $browser->press(__('Login'));
            $browser->assertRouteIs('users.home');
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
        $this->browse(function (Browser $browser) {

            // Cerrar sesión
            $browser->logout();

            // Login de profesor
            $browser->visit(route('login'));
            $browser->type('email', 'lucia@ikasgela.com');
            $browser->type('password', '12345Abcde');
            $browser->check('remember');
            $browser->press(__('Login'));
            $browser->assertRouteIs('profesor.index');

            $browser->visit('/alumnos');
            $browser->pause(500)->check("input[name='usuarios_seleccionados[1]']");
            $browser->check("input[name='seleccionadas[2]']");

            $browser->press(__('Save assigment'));
            $browser->assertRouteIs('profesor.index');

            if (config('ikasgela.avatar_enabled')) {
                $browser->assertSeeIn('main > div.p-4.col-12.col-sm-10 > div.table-responsive > table > tbody > tr:nth-child(2) > td:nth-child(4)', 'Marc');
                $browser->assertSeeIn('main > div.p-4.col-12.col-sm-10 > div.table-responsive > table > tbody > tr:nth-child(2) > td:nth-child(6)', '1');
            } else {
                $browser->assertSeeIn('main > div.p-4.col-12.col-sm-10 > div.table-responsive > table > tbody > tr:nth-child(2) > td:nth-child(3)', 'Marc');
                $browser->assertSeeIn('main > div.p-4.col-12.col-sm-10 > div.table-responsive > table > tbody > tr:nth-child(2) > td:nth-child(5)', '1');
            }

            // Cerrar sesión
            $browser->logout();

            // Login de alumno
            $browser->visit(route('login'));
            $browser->type('email', 'marc@ikasgela.com');
            $browser->type('password', '12345Abcde');
            $browser->check('remember');
            $browser->press(__('Login'));
            $browser->assertRouteIs('users.home');

            // Aceptar actividad
            $browser->assertSee('Agenda');

            $browser->press(__('Accept activity'));
            $browser->assertRouteIs('users.home');

            // Clonar el repositorio
            $browser->assertSee('Agenda de contactos con varias ventanas que comparten datos.');

            $browser->press(__('Clone the project'));
            $browser->assertRouteIs('users.home');

            $browser->pause(1000)->visit('/home');

            $browser->assertSee(__('Open in IntelliJ IDEA'));

            // Enviar para revisión
            $browser->press(__('Submit for review'));
            $browser->acceptDialog();
            $browser->assertRouteIs('users.home');

            // Aparece la siguiente actividad
            $browser->assertSee('Tres en raya');

            // Cerrar sesión
            $browser->logout();

            // Login de profesor
            $browser->visit(route('login'));
            $browser->type('email', 'lucia@ikasgela.com');
            $browser->type('password', '12345Abcde');
            $browser->check('remember');
            $browser->press(__('Login'));
            $browser->assertRouteIs('profesor.index');

            // Corregir la tarea y darla por terminada
            $browser->visit('/alumnos/1/tareas');
            $browser->visit('/profesor/1/revisar/2');
            $browser->type('puntuacion', '80');
            $browser->press(__('Add'));
            $browser->press(__('Finished'));
            $browser->assertRouteIs('profesor.tareas', ['user' => 1]);  // $browser->assertPathIs('/alumnos/1/tareas');

            $browser->assertSeeIn('main > div.p-4.col-12.col-sm-10 > div.table-responsive > table > tbody > tr:nth-child(2) > td:nth-child(9)', '80');

            // Cerrar sesión
            $browser->logout();

            // Login de alumno
            $browser->visit(route('login'));
            $browser->type('email', 'marc@ikasgela.com');
            $browser->type('password', '12345Abcde');
            $browser->check('remember');
            $browser->press(__('Login'));
            $browser->assertRouteIs('users.home');

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
            $browser->logout();
            $browser->visit(route('portada'));
            $browser->assertRouteIs('portada');
        });
    }
}
