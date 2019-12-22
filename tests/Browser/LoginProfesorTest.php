<?php

namespace Tests\Browser;

use App\Role;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginProfesorTest extends DuskTestCase
{
    public function testLoginProfesor()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login');
            $browser->type('email', 'lucia@ikasgela.com');
            $browser->type('password', '12345Abcde');
//            $browser->check('remember');
            $browser->press('Entrar');
            $browser->assertPathIs('/alumnos');
        });
    }
}
