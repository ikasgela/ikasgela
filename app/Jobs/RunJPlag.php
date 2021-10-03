<?php

namespace App\Jobs;

use App\Models\Tarea;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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
        foreach ($this->tarea->actividad->intellij_projects()->get() as $intellij_project) {
            Log::debug("Running JPlag", [
                'user' => $this->tarea->user->username,
                'repository' => $intellij_project->repository(),
                'fork' => $intellij_project->pivot->fork,
            ]);
            
            // Crear una carpeta temporal
            // Buscar todos los repositorios que compartan plantilla con el actual
            // Descargar los repositorios
            // Descomprimir los .zip
            // Ejecutar JPlag
            // Cargar el CSV del resultado
            // Insertar los resultados en la tabla RegistrosJPlag
            // Borrar la carpeta temporal

        }
    }
}
