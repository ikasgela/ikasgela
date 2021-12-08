<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class T3_LoginAlumnoTest extends DuskTestCase
{
    public function testLoginAlumno()
    {
        $this->browse(function (Browser $browser) {

            // Login de alumno
            $browser->visit(route('login'));
            $browser->type('email', 'marc@ikasgela.com');
            $browser->type('password', '12345Abcde');
            $browser->check('remember');
            $browser->press(__('Login'));
            $browser->assertRouteIs('users.home');
        });
    }
}
