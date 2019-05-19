<?php

namespace App\Http\Controllers;

use App\Actividad;
use App\Mail\ActividadAsignada;
use App\Registro;
use App\Tarea;
use App\Unidad;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ProfesorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->recuento_enviadas();

        $usuarios = User::orderBy('id')->whereHas('roles', function ($query) {
            $query->where('name', 'alumno');
        })->get();

        $unidades = Unidad::orderBy('nombre')->get();

        if ($request->has('unidad_id')) {
            session(['profesor_unidad_actual' => $request->input('unidad_id')]);
        }

        if (session('profesor_unidad_actual')) {
            $disponibles = Actividad::where('plantilla', true)->where('unidad_id', session('profesor_unidad_actual'))->get();
        } else {
            $disponibles = Actividad::where('plantilla', true)->get();
        }

        return view('profesor.index', compact(['usuarios', 'unidades', 'disponibles']));
    }

    public function tareas(User $user, Request $request)
    {
        $this->recuento_enviadas();

        $actividades = $user->actividades()->get();

        $unidades = Unidad::orderBy('nombre')->get();

        // https://gist.github.com/ermand/5458012

        $user_anterior = User::orderBy('id')->whereHas('roles', function ($query) {
            $query->where('name', 'alumno');
        })->where('id', '<', $user->id)->get()->max('id');

        $user_siguiente = User::orderBy('id')->whereHas('roles', function ($query) {
            $query->where('name', 'alumno');
        })->where('id', '>', $user->id)->get()->min('id');

        if ($request->has('unidad_id')) {
            session(['profesor_unidad_actual' => $request->input('unidad_id')]);
        }

        if (session('profesor_unidad_actual')) {
            // ->whereNotIn('id', $actividades)
            $disponibles = Actividad::where('plantilla', true)->where('unidad_id', session('profesor_unidad_actual'))->get();
        } else {
            $disponibles = Actividad::where('plantilla', true)->get();
        }

        return view('profesor.tareas', compact(['actividades', 'disponibles', 'user', 'unidades', 'user_anterior', 'user_siguiente']));
    }

    public function asignarTarea(User $user, Request $request)
    {
        $this->validate($request, [
            'seleccionadas' => 'required',
        ]);

        $this->asignarTareasUsuario($user);

        return redirect(route('profesor.tareas', ['user' => $user->id]));
    }

    public function asignarTareasGrupo(Request $request)
    {
        $this->validate($request, [
            'usuarios_seleccionados' => 'required',
            'seleccionadas' => 'required',
        ]);

        foreach (request('usuarios_seleccionados') as $user_id) {

            $user = User::find($user_id);

            $this->asignarTareasUsuario($user);
        }

        return redirect(route('profesor.index'));
    }

    public function revisar(User $user, Tarea $tarea)
    {
        $actividad = $tarea->actividad;

        return view('profesor.revisar', compact(['user', 'tarea', 'actividad']));
    }

    private function recuento_enviadas(): void
    {
        $tareas = Tarea::where('estado', 30)->get();

        $num_enviadas = count($tareas);
        if ($num_enviadas > 0)
            session(['num_enviadas' => $num_enviadas]);
        else
            session()->forget('num_enviadas');
    }

    /**
     * @param $user
     */
    private function asignarTareasUsuario($user): void
    {
        $asignadas = '';

        foreach (request('seleccionadas') as $actividad_id) {
            $actividad = Actividad::find($actividad_id);

            // Sacar un duplicado de la actividad y poner el campo plantilla a false
            // REF: https://github.com/BKWLD/cloner

            $clon = $actividad->duplicate();
            $clon->save();

            $asignadas .= "- " . $clon->unidad->nombre . " - " . $clon->nombre . ".\n\n";

            $user->actividades()->attach($clon);

            $tarea = Tarea::where('user_id', $user->id)->where('actividad_id', $clon->id)->first();

            Registro::create([
                'user_id' => $user->id,
                'tarea_id' => $tarea->id,
                'estado' => 10,
                'timestamp' => Carbon::now(),
            ]);
        }

        Mail::to($user->email)->queue(new ActividadAsignada($user->name, $asignadas));
    }
}
