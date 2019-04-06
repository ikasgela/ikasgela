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
        $tarea->delete();

        return redirect(route('alumnos.tareas', ['user' => $user->id]));
    }
}
