<?php

namespace App\Observers;

use App\Models\Actividad;
use App\Models\Tarea;
use Cache;
use Illuminate\Support\Facades\Storage;

class ActividadObserver
{
    use SharedKeys;

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
                    Storage::disk('s3')->delete('images/' . $file->path);
                    Storage::disk('s3')->delete('thumbnails/' . $file->path);
                    Storage::disk('s3')->delete('documents/' . $file->path);
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
            foreach ($this->keys as $key) {
                Cache::forget($key . $tarea->user_id);
            }
        }
    }
}
