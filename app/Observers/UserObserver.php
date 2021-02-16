<?php

namespace App\Observers;

use App\User;

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
