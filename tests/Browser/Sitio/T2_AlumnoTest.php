<?php

namespace Tests\Browser\Sitio;

use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\BrowserUiHelpers;
use Tests\DuskTestCase;

class T2_AlumnoTest extends DuskTestCase
{
    use BrowserUiHelpers;

    public function testLogin()
    {
        $this->browse(function (Browser $browser) {
            $this->loginAs($browser, 'marc@ikasgela.com', '12345Abcde', 'users.home');
        });
    }

    public function testEscritorio()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('users.home'));
            $browser->assertRouteIs('users.home');
            $this->assertNoAppErrors($browser);
            $browser->assertSee(__('Desktop'));
        });
    }

    public function testTutoria()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('messages'));
            $browser->assertRouteIs('messages');
            $this->assertNoAppErrors($browser);
            $browser->assertSee(__('Tutorship'));
        });
    }

    public function testResultados()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('results.index'));
            $browser->assertRouteIs('results.index');
            $this->assertNoAppErrors($browser);
            $browser->assertSee(__('Results'));
        });
    }

    public function testArchivo()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('archivo.index'));
            $browser->assertRouteIs('archivo.index');
            $this->assertNoAppErrors($browser);
            $browser->assertSee(__('Archived'));
        });
    }

    public function testProgreso()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('archivo.outline'));
            $browser->assertRouteIs('archivo.outline');
            $this->assertNoAppErrors($browser);
            $browser->assertSee(__('Course progress'));
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
