<?php

namespace App\Observers;

use App\Models\Curso;
use App\Models\Milestone;
use Illuminate\Support\Facades\Cache;

class MilestoneObserver
{
    public function saved(Milestone $milestone)
    {
        $this->clearCache($milestone);
    }

    public function deleted(Milestone $milestone)
    {
        $this->clearCache($milestone);
    }

    private function clearCache(Milestone $milestone): void
    {
        // No usar $milestone->curso: cargaría la relación en el modelo (y acabaría
        // en toArray())
        $curso = Curso::find($milestone->curso_id);

        if (is_null($curso)) {
            return;
        }

        foreach ($curso->users()->get() as $user) {
            Cache::tags('user_' . $user->id)->flush();
        }
    }
}
