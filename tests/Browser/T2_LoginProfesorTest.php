<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class T2_LoginProfesorTest extends DuskTestCase
{
    public function testLoginProfesor()
    {
        $this->browse(function (Browser $browser) {

            // Login de profesor
            $browser->visit('/login');
            $browser->type('email', 'lucia@ikasgela.com');
            $browser->type('password', '12345Abcde');
            $browser->check('remember');
            $browser->press('Entrar');
            $browser->assertPathIs('/alumnos');
        });
    }
}