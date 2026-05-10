<?php

namespace Tests\Browser\Sitio;

use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\BrowserUiHelpers;
use Tests\DuskTestCase;

class T3_ProfesorTest extends DuskTestCase
{
    use BrowserUiHelpers;

    public function testLogin()
    {
        $this->browse(function (Browser $browser) {
            $this->loginAs($browser, 'profesor@ikasgela.com', '12345Abcde', 'profesor.index');
        });
    }

    public function testPanel()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('profesor.index'));
            $browser->assertRouteIs('profesor.index');
            $this->assertNoAppErrors($browser);
            $browser->assertSee('Noa');
            $browser->assertSee('Marc');
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

    public function testEquipos()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('teams.index'));
            $browser->assertRouteIs('teams.index');
            $this->assertNoAppErrors($browser);
            $browser->assertSee(__('Teams'));
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
