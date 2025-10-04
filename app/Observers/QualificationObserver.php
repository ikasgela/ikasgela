<?php

namespace App\Observers;

use App\Models\Qualification;
use Illuminate\Support\Facades\Cache;

class QualificationObserver
{
    public function saved(Qualification $qualification)
    {
        $this->clearCache($qualification);
    }

    public function deleted(Qualification $qualification)
    {
        $this->clearCache($qualification);
    }

    private function clearCache(Qualification $qualification): void
    {
        foreach ($qualification->cursos()->with('users')->get() as $curso) {
            foreach ($curso->users as $user) {
                Cache::tags('user_' . $user->id)->flush();
            }
        }
    }
}
