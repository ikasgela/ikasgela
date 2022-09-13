<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\PaginarUltima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArchivoController extends Controller
{
    use PaginarUltima;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();

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

        $actividades = $this->paginate_ultima($user->actividades_archivadas());

        // Lista de usuarios
        $curso = $user->curso_actual();
        $users = [];
        if (!is_null($curso))
            $users = $curso->users()->rolAlumno()->noBloqueado()->orderBy('surname')->orderBy('name')->get();

        return view('archivo.index', compact(['actividades', 'user', 'users']));
    }

    public function show($id)
    {
        $user = Auth::user();
        $actividad = $user->actividades()->findOrFail($id);

        return view('archivo.show', compact(['actividad', 'user']));
    }

    public function outline()
    {
        $user = Auth::user();

        if ($user->baja_ansiedad) {
            abort(404);
        }

        $curso = $user->curso_actual();

        if (!is_null($curso) && $curso->progreso_visible) {
            $unidades = $curso->unidades()->whereVisible(true)->tag('examen', false)->orderBy('orden')->get();
        } else {
            abort(404);
        }

        return view('archivo.outline', compact(['unidades', 'user', 'curso']));
    }
}
