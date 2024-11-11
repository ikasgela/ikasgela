<?php

namespace Tests\Browser\Sitio;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class T4_TutorTest extends DuskTestCase
{
    public function testLogin()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('login'));
            $browser->type('email', 'tutor@ikasgela.com');
            $browser->type('password', '12345Abcde');
            $browser->check('remember');
            $browser->press(__('Login'));
            $browser->assertRouteIs('tutor.index');
            $browser->assertDontSee('Ignition');
            $browser->assertDontSee('403');
        });
    }

    public function testInforme()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('tutor.index'));
            $browser->assertRouteIs('tutor.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Group report'));
        });
    }

    public function testActividades()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('tutor.tareas_enviadas'));
            $browser->assertRouteIs('tutor.tareas_enviadas');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Activities per day'));
        });
    }

    public function testInformesResultados()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('results.index'));
            $browser->assertRouteIs('results.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Evaluation and calification'));
        });
    }

    public function testInformesProgreso()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('archivo.outline'));
            $browser->assertRouteIs('archivo.outline');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Course progress'));
        });
    }

    public function testInformesArchivo()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('archivo.index'));
            $browser->assertRouteIs('archivo.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Archived'));
        });
    }

    public function testInformesDiario()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('archivo.diario'));
            $browser->assertRouteIs('archivo.diario');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Activity journal'));
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
