<?php

namespace App\Observers;

use App\Models\Skill;
use Illuminate\Support\Facades\Cache;

class SkillObserver
{
    public function saved(Skill $skill)
    {
        $this->clearCache($skill);
    }

    public function deleted(Skill $skill)
    {
        $this->clearCache($skill);
    }

    private function clearCache(Skill $skill): void
    {
        foreach ($skill->qualifications()->with('cursos.users')->get() as $qualification) {
            foreach ($qualification->cursos as $curso) {
                foreach ($curso->users as $user) {
                    Cache::tags('user_' . $user->id)->flush();
                }
            }
        }
    }
}
