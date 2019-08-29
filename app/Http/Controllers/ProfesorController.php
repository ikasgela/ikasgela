<?php

namespace App\Http\Controllers;

use App\Actividad;
use App\Feedback;
use App\Mail\ActividadAsignada;
use App\Organization;
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

        $organization = Organization::find(setting_usuario('_organization_id'));

        $usuarios = User::organizacionActual()->rolAlumno()->orderBy('id')->get();

        $unidades = Unidad::organizacionActual()->cursoActual()->orderBy('codigo')->orderBy('nombre')->get();

        if ($request->has('unidad_id')) {
            session(['profesor_unidad_actual' => $request->input('unidad_id')]);
        }

        $disponibles = $this->actividadesDisponibles();

        return view('profesor.index', compact(['usuarios', 'unidades', 'disponibles', 'organization']));
    }

    public function tareas(User $user, Request $request)
    {
        $this->recuento_enviadas();

        $actividades = $user->actividades()->get();

        $unidades = Unidad::organizacionActual()->cursoActual()->orderBy('codigo')->orderBy('nombre')->get();

        // https://gist.github.com/ermand/5458012

        $user_anterior = User::organizacionActual()->rolAlumno()->orderBy('id')
            ->where('id', '<', $user->id)->get()->max('id');

        $user_siguiente = User::organizacionActual()->rolAlumno()->orderBy('id')
            ->where('id', '>', $user->id)->get()->min('id');

        if ($request->has('unidad_id')) {
            session(['profesor_unidad_actual' => $request->input('unidad_id')]);
        }

        $disponibles = $this->actividadesDisponibles();

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
        $feedbacks = Feedback::orderBy('mensaje')->get();

        return view('profesor.revisar', compact(['user', 'tarea', 'actividad', 'feedbacks']));
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

    private function asignarTareasUsuario($user): void
    {
        $asignadas = '';

        foreach (request('seleccionadas') as $actividad_id) {
            $actividad = Actividad::find($actividad_id);

            // Sacar un duplicado de la actividad y poner el campo plantilla a false
            // REF: https://github.com/BKWLD/cloner

            $clon = $actividad->duplicate();
            $clon->save();

            if (!is_null($actividad->qualification)) {
                $cualificacion = $actividad->qualification->duplicate();
                $cualificacion->name .= " - " . $actividad->nombre . ' (' . $actividad->id . ')';
                $cualificacion->save();
                $clon->save(['qualification_id' => $cualificacion]);
            }
            $asignadas .= "- " . $clon->unidad->nombre . " - " . $clon->nombre . ".\n\n";

            $user->actividades()->attach($clon);

            foreach ($clon->cuestionarios as $cuestionario) {
                $copia = $cuestionario->duplicate();
                $clon->cuestionarios()->detach($cuestionario);
                $clon->cuestionarios()->attach($copia);
            }

            foreach ($clon->file_uploads as $file_upload) {
                $copia = $file_upload->duplicate();
                $clon->file_uploads()->detach($file_upload);
                $clon->file_uploads()->attach($copia);
            }

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

    private function actividadesDisponibles()
    {
        $actividades_curso = Actividad::plantilla()->cursoActual();

        if (session('profesor_unidad_actual')) {
            $disponibles = $actividades_curso->where('unidad_id', session('profesor_unidad_actual'))->get();
        } else {
            $disponibles = $actividades_curso->get();
        }

        return $disponibles;
    }
}
