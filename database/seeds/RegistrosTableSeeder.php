<?php

use App\IntellijProject;
use App\Registro;
use App\Tarea;
use App\Team;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RegistrosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $usuario = User::where('email', 'test@ikasgela.com')->first();
        $tarea = Tarea::find(1);

        $registro = new Registro();
        $registro->user_id = $usuario->id;
        $registro->tarea_id = $tarea->id;
        $registro->estado = 60;
        $registro->timestamp = Carbon::now();
        $registro->detalles = 'Tarea archivada.';

        $registro->save();
    }
}
