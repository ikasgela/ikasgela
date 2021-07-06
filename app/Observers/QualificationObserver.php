<?php

namespace App\Observers;

use App\Qualification;
use App\Skill;
use Cache;

class QualificationObserver
{
    public function saved(Qualification $qualification)
    {
        Qualification::flushCache();
        $this->clearCache($qualification);

        Skill::flushCache();
    }

    public function deleted(Qualification $qualification)
    {
        Qualification::flushCache();
        $this->clearCache($qualification);

        Skill::flushCache();
    }

    private function clearCache(Qualification $qualification): void
    {
        foreach ($qualification->cursos()->with('users')->get() as $curso) {
            foreach ($curso->users as $user) {
                Cache::forget('calificaciones_' . $user->id);
            }
        }
    }
}
