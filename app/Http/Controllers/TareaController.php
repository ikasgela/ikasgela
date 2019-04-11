<?php

namespace App\Http\Controllers;

use App\Actividad;
use App\Unidad;
use Illuminate\Http\Request;

use App\User;
use App\Tarea;

class TareaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function destroy(User $user, Tarea $tarea)
    {
        $tarea->actividad->delete();

        $tarea->delete();

        return redirect(route('profesor.tareas', ['user' => $user->id]));
    }
}
