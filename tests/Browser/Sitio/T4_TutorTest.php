<?php

namespace Tests\Browser\Sitio;

use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\BrowserUiHelpers;
use Tests\DuskTestCase;

class T4_TutorTest extends DuskTestCase
{
    use BrowserUiHelpers;

    public function testLogin()
    {
        $this->browse(function (Browser $browser) {
            $this->loginAs($browser, 'tutor@ikasgela.com', '12345Abcde', 'tutor.index');
        });
    }

    public function testInforme()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('tutor.index'));
            $browser->assertRouteIs('tutor.index');
            $this->assertNoAppErrors($browser);
            $browser->assertSee(__('Group report'));
        });
    }

    public function testActividades()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('tutor.tareas_enviadas'));
            $browser->assertRouteIs('tutor.tareas_enviadas');
            $this->assertNoAppErrors($browser);
            $browser->assertSee(__('Activities per day'));
        });
    }

    public function testInformesResultados()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('results.index'));
            $browser->assertRouteIs('results.index');
            $this->assertNoAppErrors($browser);
            $browser->assertSee(__('Evaluation and calification'));
        });
    }

    public function testInformesProgreso()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('archivo.outline'));
            $browser->assertRouteIs('archivo.outline');
            $this->assertNoAppErrors($browser);
            $browser->assertSee(__('Course progress'));
        });
    }

    public function testInformesArchivo()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('archivo.index'));
            $browser->assertRouteIs('archivo.index');
            $this->assertNoAppErrors($browser);
            $browser->assertSee(__('Archived'));
        });
    }

    public function testInformesDiario()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('archivo.diario'));
            $browser->assertRouteIs('archivo.diario');
            $this->assertNoAppErrors($browser);
            $browser->assertSee(__('Activity journal'));
        });
    }

    public function testCursos()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('users.portada'));
            $browser->assertRouteIs('users.portada');
            $this->assertNoAppErrors($browser);
            $browser->assertSee(__('Courses'));
        });
    }

    public function testLogout()
    {
        $this->browse(function (Browser $browser) {
            $this->logoutToPortada($browser);
        });
    }
}
