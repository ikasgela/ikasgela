<?php

namespace Tests\Browser\Pages\Profesor;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PanelControl extends DuskTestCase
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

    public function testLogout()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout();
            $browser->visit(route('portada'));
            $browser->assertRouteIs('portada');
        });
    }
}
