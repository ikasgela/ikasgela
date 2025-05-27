<?php

namespace App\Observers;

use App\Models\Curso;
use Illuminate\Support\Facades\Cache;

class CursoObserver
{
    public function saved(Curso $curso)
    {
        $this->clearCache($curso);
    }

    public function deleted(Curso $curso)
    {
        $this->clearCache($curso);
    }

    private function clearCache(Curso $curso): void
    {
        foreach ($curso->users()->get() as $user) {
            Cache::forget('calificaciones_' . $user->id);
        }
    }
}
