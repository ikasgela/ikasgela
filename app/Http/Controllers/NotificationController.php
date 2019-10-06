<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function edit()
    {
        $user = Auth::user();

        return view('notifications.edit', compact(['user']));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $user->enviar_emails = $request->has('enviar_emails');
        $user->save();

        setting_usuario(['notificacion_mensaje_recibido' => $request->has('notificacion_mensaje_recibido')]);

        return view('notifications.edit', compact(['user']));
    }
}
