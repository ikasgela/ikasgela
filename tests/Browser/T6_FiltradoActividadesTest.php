<?php

namespace Tests\Browser;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\Tarea;
use App\Models\Unidad;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class T6_FiltradoActividadesTest extends DuskTestCase
{
    public function testFiltradoActividades()
    {
        $this->browse(function (Browser $browser) {

            // Buscar al usuario
            $usuario = User::where('email', 'marc@ikasgela.com')->first();

            // Borrar todas las actividades
            $tareas = Actividad::all();
            foreach ($tareas as $tarea)
                $tarea->delete();

            // Crear una unidad nueva en el curso actual
            $curso_actual = Curso::find(setting_usuario('curso_actual', $usuario));

            $unidad = Unidad::factory()->create([
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

                $actividad = Actividad::factory()->create([
                    'nombre' => $nombre,
                    'unidad_id' => $unidad->id,
                ]);

                $usuario->actividades()->attach($actividad, ['estado' => $estado]);

                $browser->refresh();
                $browser->assertSee($nombre);

                $actividad->delete();
            }
            // Fin del test -----------------------------------------------------------------------

            // Test -------------------------------------------------------------------------------
            $estados = [10, 20];

            foreach ($estados as $estado) {
                $nombre = 'tarea_caducada_' . $estado;

                $actividad = Actividad::factory()->create([
                    'nombre' => $nombre,
                    'unidad_id' => $unidad->id,
                    'fecha_limite' => now()->addDays(-1),
                ]);

                $usuario->actividades()->attach($actividad, ['estado' => $estado]);

                $browser->refresh();
                $browser->assertDontSee(__('Accept activity'));
                $browser->assertDontSee(__('Submit for review'));

                $actividad->delete();
            }
            // Fin del test -----------------------------------------------------------------------

            // Test
            $estado = 30;
            $nombre = 'tarea_enviada_' . $estado;

            $actividad = Actividad::factory()->create([
                'nombre' => $nombre,
                'unidad_id' => $unidad->id
            ]);

            $usuario->actividades()->attach($actividad, ['estado' => $estado]);

            $browser->refresh();

            $browser->click('#pills-enviadas-tab');
            $browser->pause(1000);

            $browser->assertSee($nombre);

            $actividad->delete();
            // Fin del test

            // Test
            $estado = 30;
            $nombre = 'tarea_enviada_autoavance_en_plazo_' . $estado;

            $actividad = Actividad::factory()->create([
                'nombre' => $nombre,
                'unidad_id' => $unidad->id,
                'auto_avance' => true,
                'fecha_limite' => now()->addDays(1),
            ]);

            $usuario->actividades()->attach($actividad, ['estado' => $estado]);

            $browser->refresh();

            $browser->assertSee($nombre);

            $actividad->delete();
            // Fin del test

            // Test
            $estado = 30;
            $nombre = 'tarea_enviada_autoavance_caducada_' . $estado;

            $actividad = Actividad::factory()->create([
                'nombre' => $nombre,
                'unidad_id' => $unidad->id,
                'auto_avance' => true,
                'fecha_limite' => now()->addDays(-1),
            ]);

            $usuario->actividades()->attach($actividad, ['estado' => $estado]);

            $browser->refresh();
            $browser->assertDontSee(__('Reopen activity'));

            $actividad->delete();
            // Fin del test

            // Test -------------------------------------------------------------------------------
            $estados = [10, 20, 30];

            foreach ($estados as $estado) {
                $nombre = 'tarea_examen_caducada_' . $estado;

                $actividad = Actividad::factory()->create([
                    'nombre' => $nombre,
                    'unidad_id' => $unidad->id,
                    'tags' => 'examen',
                    'fecha_limite' => now()->addDays(-1),
                ]);

                $usuario->actividades()->attach($actividad, ['estado' => $estado]);

                $browser->refresh();
                $browser->assertDontSee(__('Accept activity'));
                $browser->assertDontSee(__('Submit for review'));
                $browser->assertDontSee(__('Reopen activity'));

                $actividad->delete();
            }
            // Fin del test -----------------------------------------------------------------------

            // Test -------------------------------------------------------------------------------
            $estados = [40, 41, 42];

            foreach ($estados as $estado) {
                $nombre = 'tarea_examen_caducada_corregida_' . $estado;

                $actividad = Actividad::factory()->create([
                    'nombre' => $nombre,
                    'unidad_id' => $unidad->id,
                    'tags' => 'examen',
                    'fecha_limite' => now()->addDays(-1),
                ]);

                $usuario->actividades()->attach($actividad, ['estado' => $estado]);

                $browser->refresh();
                $browser->assertSee($nombre);

                $actividad->delete();
            }
            // Fin del test -----------------------------------------------------------------------

        });
    }
}
