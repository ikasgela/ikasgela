<?php

use App\Actividad;
use App\Cuestionario;
use App\FileUpload;
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
        // --- Tarea de bienvenida

        foreach (['ikasgela', 'egibide', 'deusto'] as $organizacion) {

            $unidad = Unidad::whereHas('curso.category.period.organization', function ($query) use ($organizacion) {
                $query->where('organizations.slug', $organizacion);
            })
                ->where('slug', 'introduccion')
                ->first();

            $nombre = 'Tarea de bienvenida';
            $actividad = new Actividad();
            $actividad->nombre = $nombre;
            $actividad->descripcion = 'Actividad de ejemplo que explica el flujo de trabajo.';
            $actividad->puntuacion = 10;
            $actividad->slug = Str::slug($nombre);
            $actividad->plantilla = true;
            $actividad->auto_avance = true;
            $unidad->actividades()->save($actividad);

            $video = YoutubeVideo::where('titulo', 'Primeros pasos')->first();
            $actividad->youtube_videos()->attach($video);
        }

        // --- GUI - Agenda

        $unidad = Unidad::whereHas('curso.category.period.organization', function ($query) {
            $query->where('organizations.slug', 'ikasgela');
        })
            ->where('slug', 'gui')
            ->first();

        $nombre = 'Agenda';
        $actividad = new Actividad();
        $actividad->nombre = $nombre;
        $actividad->descripcion = 'Agenda de contactos con varias ventanas que comparten datos.';
        $actividad->puntuacion = 100;
        $actividad->slug = Str::slug($nombre);
        $actividad->plantilla = true;
        $actividad->tags = 'base';
        $unidad->actividades()->save($actividad);

        $proyecto = IntellijProject::where('repositorio', 'root/agenda')->first();
        $actividad->intellij_projects()->attach($proyecto);

        $siguiente = $actividad;

        // --- GUI - Tres en raya

        $unidad = Unidad::whereHas('curso.category.period.organization', function ($query) {
            $query->where('organizations.slug', 'ikasgela');
        })
            ->where('slug', 'gui')
            ->first();

        $nombre = 'Tres en raya';
        $actividad = new Actividad();
        $actividad->nombre = $nombre;
        $actividad->descripcion = 'Juego de tres en raya con GUI.';
        $actividad->puntuacion = 100;
        $actividad->slug = Str::slug($nombre);
        $actividad->plantilla = true;
        $actividad->tags = 'base';
        $unidad->actividades()->save($actividad);

        $proyecto = IntellijProject::where('repositorio', 'root/tres-en-raya')->first();
        $actividad->intellij_projects()->attach($proyecto);

        $actividad->siguiente()->save($siguiente);

        // --- Colecciones - Reservas

        $unidad = Unidad::whereHas('curso.category.period.organization', function ($query) {
            $query->where('organizations.slug', 'ikasgela');
        })
            ->where('slug', 'colecciones')
            ->first();

        $nombre = 'Reservas';
        $actividad = new Actividad();
        $actividad->nombre = $nombre;
        $actividad->descripcion = 'Reservas, huéspedes y hoteles.';
        $actividad->puntuacion = 100;
        $actividad->slug = Str::slug($nombre);
        $actividad->plantilla = true;
        $actividad->tags = 'base';
        $unidad->actividades()->save($actividad);

        $proyecto = IntellijProject::where('repositorio', 'root/reservas')->first();
        $actividad->intellij_projects()->attach($proyecto);

        // --- Diseño de algoritmos - Alternativa simple

        $unidad = Unidad::whereHas('curso.category.period.organization', function ($query) {
            $query->where('organizations.slug', 'ikasgela');
        })
            ->where('slug', 'programacion-estructurada')
            ->first();

        $nombre = 'Alternativa simple';
        $actividad = new Actividad();
        $actividad->nombre = $nombre;
        $actividad->descripcion = 'Instrucción if-else.';
        $actividad->puntuacion = 100;
        $actividad->slug = Str::slug($nombre);
        $actividad->plantilla = true;
        $actividad->tags = 'extra';
        $unidad->actividades()->save($actividad);

        $cuestionario = Cuestionario::where('titulo', 'Cuestionario de ejemplo')->first();
        $actividad->cuestionarios()->attach($cuestionario);

        $video = YoutubeVideo::where('codigo', 'bvim4rsNHkQ')->first();
        $actividad->youtube_videos()->attach($video);

        $file_upload = FileUpload::where('titulo', 'Diagrama de flujo')->first();
        $actividad->file_uploads()->attach($file_upload);
    }
}
