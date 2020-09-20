<?php

namespace Tests\Browser;

use App\Actividad;
use App\Curso;
use App\Unidad;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class FiltradoActividadesTest extends DuskTestCase
{
    public function testTareaBienvenida()
    {
        $this->browse(function (Browser $browser) {

            $usuario = User::where('email', 'marc@ikasgela.com')->first();

            $curso_actual = Curso::find(setting_usuario('curso_actual', $usuario));

            $unidad = factory(Unidad::class)->create([
                'curso_id' => $curso_actual->id
            ]);

            $actividad = factory(Actividad::class)->create([
                'nombre' => 'Prueba',
                'unidad_id' => $unidad->id
            ]);

            $usuario->actividades()->attach($actividad, ['estado' => 20]);

            // Login de alumno
            $browser->visit('/login');
            $browser->type('email', 'marc@ikasgela.com');
            $browser->type('password', '12345Abcde');
            $browser->check('remember');
            $browser->press('Entrar');
            $browser->assertPathIs('/home');

            // Aceptar actividad
            $browser->assertSee('Prueba');

            $browser->screenshot('prueba');

            $actividad->delete();

            /*
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

            */
        });
    }

    /*
    public function testActividad()
    {
        $this->browse(function (Browser $browser) {

            // Cerrar sesión
            $browser->logout();

            // Login de profesor
            $browser->visit('/login');
            $browser->type('email', 'lucia@ikasgela.com');
            $browser->type('password', '12345Abcde');
            $browser->check('remember');
            $browser->press('Entrar');
            $browser->assertPathIs('/alumnos');

            $browser->visit('/alumnos');
            $browser->pause(500)->check("input[name='usuarios_seleccionados[1]']");
            $browser->check("input[name='seleccionadas[5]']");

            $browser->press('Guardar asignación');
            $browser->assertPathIs('/alumnos');

            $browser->assertSeeIn('div > main > div > div.table-responsive > table > tbody > tr:nth-child(1) > td:nth-child(4)', 'Marc');
            $browser->assertSeeIn('div > main > div > div.table-responsive > table > tbody > tr:nth-child(1) > td:nth-child(6)', '1');

            // Cerrar sesión
            $browser->logout();

            // Login de alumno
            $browser->visit('/login');
            $browser->type('email', 'marc@ikasgela.com');
            $browser->type('password', '12345Abcde');
            $browser->check('remember');
            $browser->press('Entrar');
            $browser->assertPathIs('/home');

            // Aceptar actividad
            $browser->assertSee('Tres en raya');

            $browser->press('Aceptar actividad');
            $browser->assertPathIs('/home');

            // Clonar el repositorio
            $browser->assertSee('Juego de tres en raya');

            $browser->clickLink('Clonar el proyecto');
            $browser->assertPathIs('/home');

            $browser->pause(1000)->visit('/home');

            $browser->assertSee('Abrir en IntelliJ IDEA');

            // Enviar para revisión
            $browser->press('Enviar para revisión');
            $browser->assertPathIs('/home');

            // Aparece la siguiente actividad
            $browser->assertSee('Agenda');

            // Cerrar sesión
            $browser->logout();

            // Login de profesor
            $browser->visit('/login');
            $browser->type('email', 'lucia@ikasgela.com');
            $browser->type('password', '12345Abcde');
            $browser->check('remember');
            $browser->press('Entrar');
            $browser->assertPathIs('/alumnos');

            // Corregir la tarea y darla por terminada
            $browser->visit('/alumnos/1/tareas');
            $browser->visit('/profesor/1/revisar/2');
            $browser->type('puntuacion', '80');
            $browser->press('Añadir');
            $browser->press('Terminada');
            $browser->assertPathIs('/alumnos/1/tareas');

            $browser->assertSeeIn('div > main > div > div.table-responsive > table > tbody > tr:nth-child(2) > td:nth-child(9)', '80');

            // Cerrar sesión
            $browser->logout();

            // Login de alumno
            $browser->visit('/login');
            $browser->type('email', 'marc@ikasgela.com');
            $browser->type('password', '12345Abcde');
            $browser->check('remember');
            $browser->press('Entrar');
            $browser->assertPathIs('/home');

            // Aceptar actividad
            $browser->assertSee('Tres en raya');

            $browser->press('Archivar');
            $browser->assertPathIs('/home');

            // No hay más tareas
            $browser->assertSee('Agenda');
        });
    }
    */
}
