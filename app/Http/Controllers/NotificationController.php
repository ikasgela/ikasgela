<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function edit()
    {
        return view('notifications.edit');
    }

    public function update(Request $request)
    {
        setting_usuario(['notificacion_mensaje_recibido' => $request->has('notificacion_mensaje_recibido')]);

        return view('notifications.edit');
    }
}
