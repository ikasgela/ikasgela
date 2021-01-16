<?php

namespace App\Listeners;

use Lab404\Impersonate\Events\TakeImpersonation;

class UserImpersonated
{
    public function __construct()
    {
    }

    public function handle(TakeImpersonation $event)
    {
        session()->forget('filtrar_user_actual');
        session()->forget('num_enviadas');
    }
}
