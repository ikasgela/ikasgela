<?php

namespace App\Observers;

use App\Curso;
use App\Organization;
use App\Unidad;
use App\User;
use Cache;

/**
 * User observer
 */
class UserObserver
{
    use SharedKeys;

    public function saved(User $user)
    {
        $this->clearCache($user);
    }

    public function deleted(User $user)
    {
        $this->clearCache($user);
    }

    private function clearCache(User $user): void
    {
        User::flushCache();

        foreach ($this->keys as $key) {
            Cache::forget($key . $user->id);
        }

        Cache::forget("user.{$user->id}");
        Cache::forget("user.{$user->id}.{$user->getRememberToken()}");
        Cache::forget("roles_{$user->id}");

        Organization::flushCache();
        Curso::flushCache();
        Unidad::flushCache();
    }
}
