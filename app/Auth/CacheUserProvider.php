<?php

namespace App\Auth;

use App\Models\User;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Support\Facades\Cache;

/**
 * Class CacheUserProvider
 * @package App\Auth
 */
class CacheUserProvider extends EloquentUserProvider
{
    /**
     * CacheUserProvider constructor.
     * @param HasherContract $hasher
     */
    public function __construct(HasherContract $hasher)
    {
        parent::__construct($hasher, User::class);
    }

    /**
     * @param mixed $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        return Cache::remember("user.$identifier", config('ikasgela.eloquent_cache_time'), function () use ($identifier) {
            return parent::retrieveById($identifier);
        });
    }

    public function retrieveByToken($identifier, $token)
    {
        return Cache::remember("user.$identifier.$token", config('ikasgela.eloquent_cache_time'), function () use ($identifier, $token) {
            parent::retrieveByToken($identifier, $token);
        });
    }
}
