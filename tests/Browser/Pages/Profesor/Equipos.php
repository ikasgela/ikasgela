<?php

namespace Tests\Browser\Pages\Profesor;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class Equipos extends DuskTestCase
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

    public function testEquipos()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('teams.index'));
            $browser->assertRouteIs('teams.index');
            $browser->assertSee(__('Teams'));
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
