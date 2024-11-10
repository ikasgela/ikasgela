<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class T3_ProfesorTest extends DuskTestCase
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
            $browser->assertDontSee('Ignition');
        });
    }

    public function testPanelControl()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('profesor.index'));
            $browser->assertRouteIs('profesor.index');
            $browser->assertDontSee('Ignition');

            if (config('ikasgela.avatar_enabled')) {
                $browser->assertSeeIn('main > div.p-4.col-12.col-sm-10 > div.table-responsive > table > tbody > tr:nth-child(1) > td:nth-child(4)', 'Noa');
                $browser->assertSeeIn('main > div.p-4.col-12.col-sm-10 > div.table-responsive > table > tbody > tr:nth-child(2) > td:nth-child(4)', 'Marc');
            } else {
                $browser->assertSeeIn('main > div.p-4.col-12.col-sm-10 > div.table-responsive > table > tbody > tr:nth-child(1) > td:nth-child(3)', 'Noa');
                $browser->assertSeeIn('main > div.p-4.col-12.col-sm-10 > div.table-responsive > table > tbody > tr:nth-child(2) > td:nth-child(3)', 'Marc');
            }
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

    public function testEquipos()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('teams.index'));
            $browser->assertRouteIs('teams.index');
            $browser->assertDontSee('Ignition');
            $browser->assertSee(__('Teams'));
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
