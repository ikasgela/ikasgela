<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginAlumnoTest extends DuskTestCase
{
    public function testLoginAlumno()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login');
            $browser->type('email', 'marc@ikasgela.com');
            $browser->type('password', '12345Abcde');
            $browser->check('remember');
            $browser->press('Entrar');
            $browser->assertPathIs('/home');
        });
    }
}
