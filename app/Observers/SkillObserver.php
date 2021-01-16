<?php

namespace App\Observers;

use App\Skill;
use Cache;

class SkillObserver
{
    public function saved(Skill $skill)
    {
        Skill::flushCache();
        $this->clearCache($skill);
    }

    public function deleted(Skill $skill)
    {
        Skill::flushCache();
        $this->clearCache($skill);
    }

    private function clearCache(Skill $skill): void
    {
        foreach ($skill->qualifications()->with('cursos.users')->get() as $qualification) {
            foreach ($qualification->cursos as $curso) {
                foreach ($curso->users as $user) {
                    Cache::forget('calificaciones_' . $user->id);
                }
            }
        }
    }
}
