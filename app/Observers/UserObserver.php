<?php

namespace App\Observers;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Cache;

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
        Cache::forget("user.{$user->id}");
        Cache::forget("user.{$user->id}.{$user->getRememberToken()}");
        Cache::forget("roles_{$user->id}");
    }

    /**
     * @param User $user
     */
    public function deleted(User $user)
    {
        Cache::forget("user.{$user->id}");
        Cache::forget("user.{$user->id}.{$user->getRememberToken()}");
        Cache::forget("roles_{$user->id}");
    }
}
