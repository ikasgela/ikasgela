<?php

namespace App\Http\Controllers;

use App\Curso;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArchivoController extends Controller
{
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

        // Situar el paginador en la última página
        $temp = $user->actividades_archivadas()->paginate(25, ['*'], 'pagina');

        if (!$request->has('pagina'))
            $actividades = $user->actividades_archivadas()->paginate(25, ['*'], 'pagina', $temp->lastPage());
        else
            $actividades = $temp;

        // Lista de usuarios
        $curso = Curso::find(setting_usuario('curso_actual'));
        $users = null;
        if (!is_null($curso))
            $users = $curso->users()->orderBy('name')->get();

        return view('archivo.index', compact(['actividades', 'user', 'users']));
    }

    public function show($id)
    {
        $user = Auth::user();
        $actividad = $user->actividades()->find($id);

        return view('archivo.show', compact(['actividad', 'user']));
    }

}
