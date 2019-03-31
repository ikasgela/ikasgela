<?php

use App\Actividad;
use App\IntellijProject;
use App\Unidad;
use App\YoutubeVideo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ActividadesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $unidad = Unidad::where('nombre', 'Introducción')->first();
        $video = YoutubeVideo::where('titulo', 'Primeros pasos')->first();
        $proyecto = IntellijProject::where('repositorio', 'programacion/introduccion/hola-mundo')->first();

        $nombre = 'Tarea de bienvenida';
        $actividad = new Actividad();
        $actividad->unidad_id = $unidad->id;
        $actividad->nombre = $nombre;
        $actividad->descripcion = 'Actividad de ejemplo que explica el flujo de trabajo.';
        $actividad->puntuacion = 10;
        $actividad->slug = Str::slug($nombre);
        $actividad->plantilla = true;
        $actividad->save();

        $actividad->youtube_videos()->attach($video);
        $actividad->intellij_projects()->attach($proyecto);

        $nombre = 'Segunda tarea';
        $siguiente = new Actividad();
        $siguiente->unidad_id = $unidad->id;
        $siguiente->nombre = $nombre;
        $siguiente->descripcion = 'Esta va después de la de bienvenida.';
        $siguiente->puntuacion = 10;
        $siguiente->slug = Str::slug($nombre);
        $siguiente->plantilla = true;
        $siguiente->save();

        $actividad->siguiente()->save($siguiente);
    }
}
