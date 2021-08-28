<?php

namespace App\Observers;

use App\Models\User;

/**
 * User observer
 */
class UserObserver
{
    public function saved(User $user)
    {
        $user->clearCache();
    }

    public function deleted(User $user)
    {
        $user->clearCache();
    }

    public function deleting(User $user)
    {
        foreach ($user->files()->get() as $file) {
            $file->delete();
        }
    }
}
