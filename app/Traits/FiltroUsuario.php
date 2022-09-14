<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Http\Request;

trait FiltroUsuario
{
    public function filtrar_por_usuario(Request $request, User $user): User
    {
        $request->validate([
            'user_id' => 'numeric|integer',
        ]);

        if (!empty($request->input('user_id'))) {
            $user_id = $request->input('user_id');
            if ($user_id == -1) {
                session()->forget('filtrar_user_actual');
            } else {
                $user = User::find($user_id);
                session(['filtrar_user_actual' => $user_id]);
            }
        } else if (!empty(session('filtrar_user_actual'))) {
            $user = User::find(session('filtrar_user_actual'));
        }

        return $user;
    }
}
