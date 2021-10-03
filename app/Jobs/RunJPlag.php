<?php

namespace App\Jobs;

use App\Models\Tarea;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use TitasGailius\Terminal\Terminal;

class RunJPlag implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Tarea $tarea;

    public function __construct(Tarea $tarea)
    {
        $this->onQueue('low');

        $this->tarea = $tarea;
    }

    public function handle()
    {
        // Crear el directorio temporal
        $directorio = '/' . Str::uuid() . '/';
        Storage::disk('temp')->makeDirectory($directorio);
        $ruta = Storage::disk('temp')->path($directorio);

        // Buscar todas las actividades de compartan plantilla con la actual
        $curso = $this->tarea->user->curso_actual();
        $actividades = $curso->actividades()->where('plantilla_id', $this->tarea->actividad->plantilla_id)->get();

        // Descargar los repositorios
        foreach ($actividades as $actividad) {
            $intellij_projects = $actividad->intellij_projects()->get();

            foreach ($intellij_projects as $intellij_project) {
                $repositorio = $intellij_project->repository();
                Terminal::in($ruta)
                    ->run('git clone http://root:' . config('gitea.token') . '@gitea:3000/'
                        . $repositorio['path_with_namespace'] . '.git '
                        . $repositorio['owner'] . '@' . $repositorio['name']);
            }
        }

        // Ejecutar JPlag
        Terminal::in($ruta)
            ->run('java -jar /opt/jplag.jar -l java19 -s -r "./__resultados" .');

        // Cargar el CSV del resultado
        // Insertar los resultados en la tabla RegistrosJPlag

        // Borrar el directorio temporal
        Storage::disk('temp')->deleteDirectory($directorio);
    }
}
