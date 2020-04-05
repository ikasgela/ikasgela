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
use NumberFormatter;

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

        if ($request->has('filtro_alumnos')) {
            session(['profesor_filtro_alumnos' => $request->input('filtro_alumnos')]);
        }

        switch (session('profesor_filtro_alumnos')) {
            case 'R':
                $usuarios = User::organizacionActual()->rolAlumno()
                    ->whereHas('actividades', function ($query) {
                        $query->where('auto_avance', false)->where('estado', 30);
                    })
                    ->orderBy('last_active')->get();
                break;
            case 'P':
                $usuarios = User::organizacionActual()->rolAlumno()->orderBy('name')->get()->sortBy('num_completadas_base');
                break;
            default:
                $usuarios = User::organizacionActual()->rolAlumno()->orderBy('name')->get();
                break;
        }

        $unidades = Unidad::organizacionActual()->cursoActual()->orderBy('codigo')->orderBy('nombre')->get();

        if ($request->has('unidad_id')) {
            session(['profesor_unidad_actual' => $request->input('unidad_id')]);
        }

        $disponibles = $this->actividadesDisponibles();

        $usuarios_activos = User::organizacionActual()->rolAlumno()->noBloqueado()->get();

        $total_actividades_grupo = 0;
        foreach ($usuarios_activos as $usuario) {
            $total_actividades_grupo += $usuario->num_completadas('base');
        }

        $media_grupo = $total_actividades_grupo / $usuarios_activos->count();

        // Formateador con 2 decimales y en el idioma del usuario
        $locale = app()->getLocale();
        $formatStyle = NumberFormatter::DECIMAL;
        $formatter = new NumberFormatter($locale, $formatStyle);
        $formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 2);
        $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 2);

        $media_grupo_formato = $formatter->format($media_grupo);

        return view('profesor.index', compact(['usuarios', 'unidades', 'disponibles', 'organization',
            'total_actividades_grupo', 'media_grupo', 'media_grupo_formato']));
    }

    public function tareas(User $user, Request $request)
    {
        $this->recuento_enviadas();

        if ($request->has('filtro_alumnos')) {
            session(['profesor_filtro_alumnos' => $request->input('filtro_alumnos')]);
        }

        switch (session('profesor_filtro_alumnos')) {
            case 'R':
                $actividades = $user->actividades_enviadas_noautoavance();
                break;
            case 'E':
                $actividades = $user->actividades_examen();
                break;
            default:
                $actividades = $user->actividades();
                break;
        }

        $temp = $actividades->paginate(25, ['*'], 'asignadas');

        if (!$request->has('asignadas'))
            $actividades = $actividades->paginate(25, ['*'], 'asignadas', $temp->lastPage());
        else
            $actividades = $temp;

        $unidades = Unidad::organizacionActual()->cursoActual()->orderBy('codigo')->orderBy('nombre')->get();

        // Obtener el id del anterior y el siguiente

        $usuarios = User::organizacionActual()->rolAlumno()->orderBy('name')->pluck('id')->toArray();

        $pos = array_search($user->id, $usuarios);

        $user_anterior = null;
        if (isset($usuarios[$pos - 1])) {
            $user_anterior = $usuarios[$pos - 1];
        }

        $user_siguiente = null;
        if (isset($usuarios[$pos + 1])) {
            $user_siguiente = $usuarios[$pos + 1];
        }

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
        $tareas = Tarea::cursoActual()->noAutoAvance()->where('estado', 30)->get();

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

            $primero = true;
            $anterior = null;

            while ($actividad != null) {

                $clon = $actividad->duplicate();
                $clon->save();

                if ($primero) {
                    $asignadas .= "- " . $clon->unidad->nombre . " - " . $clon->nombre . ".\n";
                    $user->actividades()->attach($clon);
                    $tarea = Tarea::where('user_id', $user->id)->where('actividad_id', $clon->id)->first();
                } else {
                    $clon->siguiente_id = $anterior->id;
                    $clon->save();
                }

                if (!is_null($actividad->qualification)) {
                    $cualificacion = $actividad->qualification->duplicate();
                    $cualificacion->name .= " - " . $actividad->nombre . ' (' . $actividad->id . ')';
                    $cualificacion->save();
                    $clon->save(['qualification_id' => $cualificacion]);
                }

                foreach ($actividad->cuestionarios as $cuestionario) {
                    $copia = $cuestionario->duplicate();
                    $clon->cuestionarios()->detach($cuestionario);
                    $clon->cuestionarios()->attach($copia);
                }

                foreach ($actividad->file_uploads as $file_upload) {
                    $copia = $file_upload->duplicate();
                    $clon->file_uploads()->detach($file_upload);
                    $clon->file_uploads()->attach($copia);
                }

                $actividad = $actividad->siguiente;
                $anterior = $clon;
                $primero = false;
            }

            Registro::create([
                'user_id' => $user->id,
                'tarea_id' => $tarea->id,
                'estado' => 10,
                'timestamp' => Carbon::now(),
            ]);
        }

        if (setting_usuario('notificacion_actividad_asignada', $user))
            Mail::to($user->email)->queue(new ActividadAsignada($user->name, $asignadas));
    }

    private function actividadesDisponibles()
    {
        $actividades_curso = Actividad::plantilla()->cursoActual()->orderBy('orden');

        if (session('profesor_unidad_actual')) {
            $disponibles = $actividades_curso->where('unidad_id', session('profesor_unidad_actual'));
        } else {
            $disponibles = $actividades_curso;
        }

        $temp = $disponibles->paginate(25, ['*'], 'disponibles');

        if (empty(request('disponibles')))
            return $disponibles->paginate(25, ['*'], 'disponibles', $temp->lastPage());
        else
            return $temp;
    }
}
