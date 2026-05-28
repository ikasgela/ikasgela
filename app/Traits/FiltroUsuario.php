<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                $candidate = User::find($user_id);
                if ($candidate && $this->usuarioPerteneceAlCursoActual($candidate)) {
                    $user = $candidate;
                    session(['filtrar_user_actual' => $user_id]);
                }
            }
        } else if (!empty(session('filtrar_user_actual'))) {
            $candidate = User::find(session('filtrar_user_actual'));
            if ($candidate && $this->usuarioPerteneceAlCursoActual($candidate)) {
                $user = $candidate;
            }
        }

        return $user;
    }

    private function usuarioPerteneceAlCursoActual(User $user): bool
    {
        if (Auth::user()->hasRole('admin')) {
            return true;
        }

        return Auth::user()->curso_actual()?->users()->where('user_id', $user->id)->exists() ?? false;
    }
}
