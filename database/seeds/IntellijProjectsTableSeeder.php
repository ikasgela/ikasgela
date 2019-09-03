<?php

use App\IntellijProject;
use Illuminate\Database\Seeder;

class IntellijProjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $proyecto = new IntellijProject();
        $proyecto->titulo = '¡Hola mundo!';
        $proyecto->descripcion = 'Primer proyecto de prueba.';
        $proyecto->repositorio = 'programacion/introduccion/hola-mundo';
        $proyecto->save();

        $proyecto = new IntellijProject();
        $proyecto->titulo = 'Agenda';
        $proyecto->repositorio = 'programacion/introduccion/hola-mundo';
        $proyecto->save();

        $proyecto = new IntellijProject();
        $proyecto->titulo = 'Tres en raya';
        $proyecto->repositorio = 'programacion/introduccion/hola-mundo';
        $proyecto->save();

        $proyecto = new IntellijProject();
        $proyecto->titulo = 'Reservas';
        $proyecto->repositorio = 'programacion/introduccion/hola-mundo';
        $proyecto->save();

        $proyecto = new IntellijProject();
        $proyecto->titulo = 'Alternativa simple';
        $proyecto->repositorio = 'programacion/introduccion/hola-mundo';
        $proyecto->save();
    }
}
