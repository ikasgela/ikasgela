<?php

namespace App\Listeners;

use Lab404\Impersonate\Events\LeaveImpersonation;

class UserImpersonatedEnded
{
    public function __construct()
    {
    }

    public function handle(LeaveImpersonation $event)
    {
        session()->forget('filtrar_user_actual');
        session()->forget('num_enviadas');
    }
}
