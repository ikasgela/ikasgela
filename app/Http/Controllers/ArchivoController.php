<?php

namespace App\Http\Controllers;

use App\Actividad;
use Illuminate\Support\Facades\Auth;

class ArchivoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $actividades = $user->actividades_archivadas();

        // Recuento de asignadas
        $num_actividades = $user->actividades_asignadas()->count();

        if ($num_actividades > 0)
            session(['num_actividades' => $num_actividades]);
        else
            session()->forget('num_actividades');

        return view('archivo.index', compact('actividades'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $actividad = $user->actividades()->find($id);

        return view('archivo.show', compact(['actividad', 'user']));
    }

}
