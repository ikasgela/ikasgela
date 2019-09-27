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
        memorizar_ruta();

        $user = Auth::user();

        if (!empty($request->input('user_id'))) {
            $user = User::find($request->input('user_id'));
            session(['filtrar_user_actual' => $request->input('user_id')]);
        } else {
            session()->forget('filtrar_user_actual');
        }

        $actividades = $user->actividades_archivadas()->paginate(25);

        // Recuento de asignadas
        $num_actividades = $user->actividades_asignadas()->count();

        if ($num_actividades > 0)
            session(['num_actividades' => $num_actividades]);
        else
            session()->forget('num_actividades');

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
