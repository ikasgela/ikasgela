<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TareaBienvenidaTest extends DuskTestCase
{
    public function testTareaBienvenida()
    {
        $this->browse(function (Browser $browser) {

            // Login de alumno
            $browser->visit('/login');
            $browser->type('email', 'marc@ikasgela.com');
            $browser->type('password', '12345Abcde');
            $browser->check('remember');
            $browser->press('Entrar');
            $browser->assertPathIs('/home');

            // Aceptar actividad
            $browser->assertSee('Tarea de bienvenida');

            $browser->press('Aceptar actividad');
            $browser->assertPathIs('/home');

            // Enviar para revisión
            $browser->assertSee('Primeros pasos');

            $browser->press('Enviar para revisión');
            $browser->assertPathIs('/home');

            // Avance automático
            $browser->assertSee('Esta actividad es de avance automático');

            $browser->press('Siguiente paso');
            $browser->assertPathIs('/home');

            // Archivar
            $browser->assertSee('Tarea completada automáticamente');

            $browser->press('Archivar');
            $browser->assertPathIs('/home');

            // No hay más tareas
            $browser->assertSee('No hay actividades en curso');
        });
    }
}
