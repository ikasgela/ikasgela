<?php

namespace App\Observers;

use App\Curso;
use App\Organization;
use App\User;
use Cache;

/**
 * User observer
 */
class UserObserver
{
    /**
     * @param User $user
     */
    public function saved(User $user)
    {
        $this->clearCache($user);
    }

    /**
     * @param User $user
     */
    public function deleted(User $user)
    {
        $this->clearCache($user);
    }

    private function clearCache(User $user): void
    {
        User::flushCache();

        Cache::forget("user.{$user->id}");
        Cache::forget("user.{$user->id}.{$user->getRememberToken()}");
        Cache::forget("roles_{$user->id}");

        Curso::flushCache();
        Organization::flushCache();
    }
}
