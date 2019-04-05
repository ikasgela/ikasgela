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

    public function index(User $user, Request $request)
    {
        $actividades = $user->actividades()->get();

        $unidades = Unidad::all();

        if ($request->has('unidad_id')) {
            session(['profesor_unidad_actual' => $request->input('unidad_id')]);
        }

        if (session('profesor_unidad_actual')) {
            // ->whereNotIn('id', $actividades)
            $disponibles = Actividad::where('plantilla', true)->where('unidad_id', session('profesor_unidad_actual'))->get();
        } else {
            $disponibles = Actividad::where('plantilla', true)->get();
        }

        return view('tareas.index', compact(['actividades', 'disponibles', 'user', 'unidades']));
    }

    public function asignar(User $user, Request $request)
    {
        $this->validate($request, [
            'seleccionadas' => 'required',
        ]);

        foreach (request('seleccionadas') as $actividad_id) {
            $actividad = Actividad::find($actividad_id);

            // Sacar un duplicado de la actividad y poner el campo plantilla a false
            // REF: https://github.com/BKWLD/cloner

            $clon = $actividad->duplicate();
            $clon->save();

            $user->actividades()->attach($clon);
        }

        return redirect(route('tareas.index', ['user' => $user->id]));
    }

    public function destroy(User $user, Tarea $tarea)
    {
        $tarea->delete();

        return redirect(route('tareas.index', ['user' => $user->id]));
    }

}
