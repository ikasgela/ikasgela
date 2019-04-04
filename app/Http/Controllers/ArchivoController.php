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
        $actividades = $user->actividades()->wherePivot('estado', 60)->get();

        return view('archivo.index', compact('actividades'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $actividad = $user->actividades()->find($id);

        return view('archivo.show', compact(['actividad', 'user']));
    }

}
