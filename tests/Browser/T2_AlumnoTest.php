<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class T2_AlumnoTest extends DuskTestCase
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
            $browser->assertDontSee('Ignition');
        });
    }

    public function testEscritorio()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('users.home'));
            $browser->assertRouteIs('users.home');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Desktop'));
        });
    }

    public function testTutoria()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('messages'));
            $browser->assertRouteIs('messages');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Tutorship'));
        });
    }

    public function testResultados()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('results.index'));
            $browser->assertRouteIs('results.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Results'));
        });
    }

    public function testArchivoAlumno()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('archivo.index'));
            $browser->assertRouteIs('archivo.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Archived'));
        });
    }

    public function testProgresoAlumno()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('archivo.outline'));
            $browser->assertRouteIs('archivo.outline');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Course progress'));
        });
    }

    public function testCursos()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('users.portada'));
            $browser->assertRouteIs('users.portada');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Courses'));
        });
    }

    public function testLogout()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout();
            $browser->visit(route('portada'));
            $browser->assertRouteIs('portada');
            $browser->assertDontSee('Ignition');
        });
    }
}
