<?php

namespace App\Auth;

use App\Models\User;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Support\Facades\Cache;
use Override;

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
     * @return Authenticatable|null
     */
    #[Override]
    public function retrieveById($identifier)
    {
        return Cache::remember("user.$identifier", config('ikasgela.eloquent_cache_time'), fn() => parent::retrieveById($identifier));
    }

    #[Override]
    public function retrieveByToken($identifier, $token)
    {
        return Cache::remember("user.$identifier.$token", config('ikasgela.eloquent_cache_time'), function () use ($identifier, $token) {
            parent::retrieveByToken($identifier, $token);
        });
    }
}
