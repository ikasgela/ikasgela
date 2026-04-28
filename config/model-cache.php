<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cache Duration
    |--------------------------------------------------------------------------
    |
    | This value determines the default number of MINUTES to cache query results.
    | Use MODEL_CACHE_DURATION to set this independently of ELOQUENT_CACHE_TIME,
    | which is used in seconds by other parts of the application (e.g. setting_usuario).
    | Default: 1440 minutes (24 hours). HasCachedQueries auto-invalidates on save/delete,
    | so correctness is not affected by TTL — only Redis memory pressure is.
    |
    */
    'cache_duration' => intval(env('MODEL_CACHE_DURATION', 1440)),

    /*
    |--------------------------------------------------------------------------
    | Cache Key Prefix
    |--------------------------------------------------------------------------
    |
    | This prefix will be used for all cache keys to avoid collisions.
    |
    */
    'cache_key_prefix' => 'model_cache_',

    /*
    |--------------------------------------------------------------------------
    | Cache Store
    |--------------------------------------------------------------------------
    |
    | This option controls the cache store that gets used for storing
    | and retrieving queries. Use env('MODEL_CACHE_STORE') to specify
    | a different store than your main application cache.
    |
    | Note: For tag support, use Redis or Memcached drivers.
    |
    */
    'cache_store' => env('MODEL_CACHE_STORE', null),

    /*
    |--------------------------------------------------------------------------
    | Enable Query Caching
    |--------------------------------------------------------------------------
    |
    | This option provides an easy way to globally enable/disable query caching.
    |
    */
    'enabled' => env('MODEL_CACHE_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Debug Mode
    |--------------------------------------------------------------------------
    |
    | When enabled, this will log detailed information about cache keys and
    | queries being cached. Useful for troubleshooting cache-related issues.
    |
    */
    'debug_mode' => env('MODEL_CACHE_DEBUG', false),
];
