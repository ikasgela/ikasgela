<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class T7_AlumnoTest extends DuskTestCase
{
    public function testLoginAlumno()
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

    public function testEscritorioAlumno()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('users.home'));
            $browser->assertSee(__('Desktop'));
        });
    }

    public function testTutoriaAlumno()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('messages'));
            $browser->assertSee(__('Tutorship'));
        });
    }

    public function testArchivoAlumno()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('archivo.index'));
            $browser->assertSee(__('Archived'));
        });
    }

    public function testProgresoAlumno()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('archivo.outline'));
            $browser->assertSee(__('Course progress'));
        });
    }

    public function testResultadosAlumno()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('results.index'));
            $browser->assertSee(__('Results'));
        });
    }

    public function testLogoutAlumno()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout();
            $browser->visit(route('portada'));
            $browser->assertRouteIs('portada');
        });
    }
}
