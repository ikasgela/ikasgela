<?php

namespace Tests\Browser\Pages\Tutor;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ActividadesDia extends DuskTestCase
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
            $browser->visit(route('tutor.tareas_enviadas'));
            $browser->assertRouteIs('tutor.tareas_enviadas');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Activities per day'));
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
