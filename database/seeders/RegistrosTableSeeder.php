<?php

namespace Database\Seeders;

use App\Registro;
use App\Tarea;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RegistrosTableSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $usuario = User::where('email', 'marc@ikasgela.com')->first();
        $tarea = Tarea::find(1);

        $registro = new Registro();
        $registro->user_id = $usuario->id;
        $registro->tarea_id = $tarea->id;
        $registro->estado = 10;
        $registro->timestamp = Carbon::now();

        $registro->save();
    }
}
