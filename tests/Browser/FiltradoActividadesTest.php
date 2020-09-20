<?php

namespace Tests\Browser;

use App\Actividad;
use App\Curso;
use App\Tarea;
use App\Unidad;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class FiltradoActividadesTest extends DuskTestCase
{
    public function testTareaBienvenida()
    {
        $this->browse(function (Browser $browser) {

            // Buscar al usuario
            $usuario = User::where('email', 'marc@ikasgela.com')->first();

            // Ocultar la tarea de bienvenida
            $tarea_bienvenida = Tarea::where('user_id', $usuario->id)->whereHas('actividad', function ($query) {
                $query->where('slug', 'tarea-de-bienvenida');
            })->first();
            $tarea_bienvenida->estado = 11;
            $tarea_bienvenida->save();

            // Crear una unidad nueva en el curso actual
            $curso_actual = Curso::find(setting_usuario('curso_actual', $usuario));

            $unidad = factory(Unidad::class)->create([
                'curso_id' => $curso_actual->id
            ]);

            // Login de alumno
            $browser->visit('/login');
            $browser->type('email', 'marc@ikasgela.com');
            $browser->type('password', '12345Abcde');
            $browser->check('remember');
            $browser->press('Entrar');
            $browser->assertPathIs('/home');

            // Test -------------------------------------------------------------------------------
            $estados = [10, 20, 40, 41];

            foreach ($estados as $estado) {
                $nombre = 'tarea_en_plazo_' . $estado;

                $actividad = factory(Actividad::class)->create([
                    'nombre' => $nombre,
                    'unidad_id' => $unidad->id,
                ]);

                $usuario->actividades()->attach($actividad, ['estado' => $estado]);

                $browser->refresh();
                $browser->assertSee($nombre);

                $browser->screenshot($nombre);

                $actividad->delete();
            }
            // Fin del test -----------------------------------------------------------------------

            // Test -------------------------------------------------------------------------------
            $estados = [10, 20];

            foreach ($estados as $estado) {
                $nombre = 'tarea_caducada_' . $estado;

                $actividad = factory(Actividad::class)->create([
                    'nombre' => $nombre,
                    'unidad_id' => $unidad->id,
                    'fecha_limite' => now()->addDays(-1),
                ]);

                $usuario->actividades()->attach($actividad, ['estado' => $estado]);

                $browser->refresh();
                $browser->assertDontSee($nombre);

                $browser->screenshot($nombre);

                $actividad->delete();
            }
            // Fin del test -----------------------------------------------------------------------

            // Test
            $estado = 30;
            $nombre = 'tarea_enviada_' . $estado;

            $actividad = factory(Actividad::class)->create([
                'nombre' => $nombre,
                'unidad_id' => $unidad->id
            ]);

            $usuario->actividades()->attach($actividad, ['estado' => $estado]);

            $browser->refresh();

            $browser->click('#pills-enviadas-tab');
            $browser->pause(1000);

            $browser->assertSee($nombre);

            $browser->screenshot($nombre);

            $actividad->delete();
            // Fin del test

            // Test
            $estado = 30;
            $nombre = 'tarea_enviada_autoavance_en_plazo_' . $estado;

            $actividad = factory(Actividad::class)->create([
                'nombre' => $nombre,
                'unidad_id' => $unidad->id,
                'auto_avance' => true,
                'fecha_limite' => now()->addDays(1),
            ]);

            $usuario->actividades()->attach($actividad, ['estado' => $estado]);

            $browser->refresh();

            $browser->assertSee($nombre);

            $browser->screenshot($nombre);

            $actividad->delete();
            // Fin del test

            // Test
            $estado = 30;
            $nombre = 'tarea_enviada_autoavance_caducada_' . $estado;

            $actividad = factory(Actividad::class)->create([
                'nombre' => $nombre,
                'unidad_id' => $unidad->id,
                'auto_avance' => true,
                'fecha_limite' => now()->addDays(-1),
            ]);

            $usuario->actividades()->attach($actividad, ['estado' => $estado]);

            $browser->refresh();

            $browser->assertDontSee($nombre);

            $browser->screenshot($nombre);

            $actividad->delete();
            // Fin del test

            // Test
            $estado = 40;
            $nombre = 'tarea_examen_caducada_corregida_' . $estado;

            $actividad = factory(Actividad::class)->create([
                'nombre' => $nombre,
                'unidad_id' => $unidad->id,
                'tags' => 'examen',
                'fecha_limite' => now()->addDays(-1),
            ]);

            $usuario->actividades()->attach($actividad, ['estado' => $estado]);

            $browser->refresh();

            //$browser->click('#pills-enviadas-tab');
            //$browser->pause(1000);

            $browser->assertSee($nombre);

            $browser->screenshot($nombre);

            $actividad->delete();
            // Fin del test

            // Restaurar la tarea de bienvenida
            $tarea_bienvenida->estado = 10;
            $tarea_bienvenida->save();

            $browser->refresh();
            $browser->assertSee('Tarea de bienvenida');
        });
    }
}
