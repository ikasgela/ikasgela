<?php

namespace App\Observers;

use App\Models\Actividad;
use App\Models\Tarea;
use Illuminate\Support\Facades\Cache;

class ActividadObserver
{
    public function saved(Actividad $actividad)
    {
        $this->clearCache($actividad);
    }

    public function deleted(Actividad $actividad)
    {
        $this->clearCache($actividad);
    }

    public function deleting(Actividad $actividad)
    {
        foreach ($actividad->file_uploads()->get() as $recurso) {
            if (!$recurso->plantilla) {
                foreach ($recurso->files()->get() as $file) {
                    $file->delete();
                }
                $recurso->delete();
            }
        }

        foreach ($actividad->cuestionarios()->get() as $recurso) {
            if (!$recurso->plantilla) {
                $recurso->delete();
            }
        }
    }

    private function clearCache(Actividad $actividad): void
    {
        $tareas = Tarea::where('actividad_id', $actividad->id)->get();

        foreach ($tareas as $tarea) {
            Cache::tags('user_' . $tarea->user_id)->flush();
        }
    }
}
