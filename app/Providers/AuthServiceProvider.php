<?php

namespace App\Providers;

use App\Auth\CacheUserProvider;
use Honey;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        // Caching user
        Auth::provider('cache-user', function () {
            return resolve(CacheUserProvider::class);
        });

        $this->registerPolicies();

        Validator::extend('allowed_domains', function ($attribute, $value, $parameters, $validator) {
            return in_array('*', $parameters) || in_array(explode('@', $value)[1], $parameters);
        }, __('Invalid email address.'));

        Validator::extend('forbidden_domains', function ($attribute, $value, $parameters, $validator) {
            return !in_array(explode('@', $value)[1], $parameters);
        }, __('Invalid email address.'));

        Honey::failUsing(function () {
            abort(429); // Too many requests
        });
    }
}
