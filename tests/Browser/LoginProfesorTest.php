<?php

namespace Tests\Browser;

use App\Role;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\DuskTestCase;

class LoginProfesorTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function testLoginProfesor()
    {
        $fecha = Carbon::now();

        $role = new Role();
        $role->name = 'profesor';
        $role->description = 'Profesor';
        $role->save();

        $user = factory(User::class)->create([
            'email' => 'profe@ikasgela.com',
            'email_verified_at' => $fecha
        ]);

        $user->roles()->attach($role);

        $this->browse(function ($browser) use ($user) {
            $browser->visit('/login')
                ->type('email', $user->email)
                ->type('password', 'secret')
                ->press('@boton-submit')
                ->assertPathIs('/alumnos');
        });
    }
}
