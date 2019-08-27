<?php

use App\Actividad;
use App\User;
use Illuminate\Database\Seeder;

class TareasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $usuario = User::where('email', 'marc@ikasgela.com')->first();
        $actividad = Actividad::where('nombre', 'Tarea de bienvenida')->first();

        $clon = $actividad->duplicate();
        $clon->final = true;
        $clon->save();
        $usuario->actividades()->attach($clon, ['puntuacion' => 0]);
    }
}
