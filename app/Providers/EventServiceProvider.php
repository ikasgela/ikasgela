<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        'Illuminate\Auth\Events\Verified' => [
            'App\Listeners\ActivarUsuario',
        ],
        'Illuminate\Auth\Events\Login' => [
            'App\Listeners\LoginSuccess',
        ],
        'Illuminate\Auth\Events\Logout' => [
            'App\Listeners\LogoutSuccess',
        ],
        'Lab404\Impersonate\Events\TakeImpersonation' => [
            'App\Listeners\UserImpersonated',
        ],
        'Lab404\Impersonate\Events\LeaveImpersonation' => [
            'App\Listeners\UserImpersonatedEnded',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
