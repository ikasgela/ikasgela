<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;

class LogoutSuccess
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
    public function handle(Logout $event)
    {
        //
    }
}
