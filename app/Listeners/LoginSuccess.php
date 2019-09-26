<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class LoginSuccess
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle(Login $event)
    {
        session(['tutorial' => $event->user->tutorial]);
        session(['enviar_emails' => $event->user->enviar_emails]);

        activity()
            ->causedBy($event->user)
            ->log('Sesión iniciada');
    }
}
