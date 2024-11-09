<?php

namespace Tests\Browser\Pages\Tutor\Informes;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class Diario extends DuskTestCase
{
    public function testLogin()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('login'));
            $browser->type('email', 'lucia@ikasgela.com');
            $browser->type('password', '12345Abcde');
            $browser->check('remember');
            $browser->press(__('Login'));
            $browser->assertRouteIs('profesor.index');
        });
    }

    public function testIndex()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('archivo.diario'));
            $browser->assertRouteIs('archivo.diario');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Activity journal'));
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
