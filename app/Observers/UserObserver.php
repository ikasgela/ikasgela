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
}
