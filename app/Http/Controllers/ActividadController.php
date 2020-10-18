<?php

namespace App\Http\Controllers;

use App\Actividad;
use App\Exports\ActividadesCursoExport;
use App\Mail\ActividadAsignada;
use App\Mail\FeedbackRecibido;
use App\Mail\TareaEnviada;
use App\Qualification;
use App\Registro;
use App\Tarea;
use App\Traits\InformeActividadesCurso;
use App\Traits\PaginarUltima;
use App\Unidad;
use App\User;
use BadMethodCallException;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ActividadController extends Controller
{
    use PaginarUltima;
    use InformeActividadesCurso;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin')->except(['actualizarEstado', 'preview']);
    }

    public function index()
    {
        $actividades = $this->paginate_ultima(Actividad::query(), 250);

        session(['ubicacion' => 'actividades.index']);

        $ids = $actividades->pluck('id')->toArray();

        $todas_unidades = Unidad::orderBy('curso_id')->orderBy('codigo')->orderBy('nombre')->get();

        return view('actividades.index', compact(['actividades', 'ids', 'todas_unidades']));
    }

    public function export()
    {
        return Excel::download(new ActividadesCursoExport, 'actividades.xlsx');
    }

    public function plantillas(Request $request)
    {
        session(['ubicacion' => 'actividades.plantillas']);

        $unidades = Unidad::cursoActual()->orderBy('codigo')->orderBy('nombre')->get();

        $todas_unidades = Unidad::orderBy('curso_id')->orderBy('codigo')->orderBy('nombre')->get();

        if ($request->has('unidad_id')) {
            session(['profesor_unidad_actual' => $request->input('unidad_id')]);
        }

        if (session('profesor_unidad_actual')) {
            $actividades = Actividad::cursoActual()->plantilla()->where('unidad_id', session('profesor_unidad_actual'))->orderBy('orden')->orderBy('id');
        } else {
            $actividades = Actividad::cursoActual()->plantilla()->where('plantilla', true)->orderBy('orden')->orderBy('id');
        }

        $actividades = $this->paginate_ultima($actividades, 250);

        $ids = $actividades->pluck('id')->toArray();

        return view('actividades.plantillas', compact(['actividades', 'unidades', 'ids', 'todas_unidades']));
    }

    public function create()
    {
        $unidades = Unidad::cursoActual()->orderBy('codigo')->orderBy('slug')->get();
        $actividades = Actividad::cursoActual()->plantilla()->orderBy('slug')->orderBy('id')->get();
        $qualifications = Qualification::organizacionActual()->orderBy('name')->get();

        return view('actividades.create', compact(['unidades', 'actividades', 'qualifications']));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'unidad_id' => 'required',
            'nombre' => 'required',
        ]);

        $actividad = Actividad::create([
            'unidad_id' => request('unidad_id'),

            'nombre' => $request->input('nombre'),
            'descripcion' => $request->input('descripcion'),
            'puntuacion' => $request->input('puntuacion'),

            'plantilla' => $request->has('plantilla'),
            'final' => $request->has('final'),
            'auto_avance' => $request->has('auto_avance'),

            'slug' => Str::slug(request('nombre')),

            'siguiente_id' => request('siguiente_id'),

            'qualification_id' => request('qualification_id'),

            'fecha_disponibilidad' => request('fecha_disponibilidad'),
            'fecha_entrega' => request('fecha_entrega'),
            'fecha_limite' => request('fecha_limite'),

            'destacada' => $request->has('destacada'),
            'tags' => request('tags'),

            'multiplicador' => request('multiplicador'),
        ]);

        $actividad->orden = $actividad->id;
        $actividad->save();

        return retornar();
    }

    protected $table = 'actividades';

    /**
     * Display the specified resource.
     *
     * @param \App\Actividad $actividad
     * @return \Illuminate\Http\Response
     */
    public function show(Actividad $actividad)
    {
        return abort(501);
    }

    public function preview(Actividad $actividad)
    {
        $user = Auth::user();

        if ($user->hasAnyRole(['admin', 'profesor']) || $user->hasRole('alumno') && $actividad->plantilla) {
            return view('actividades.preview', compact('actividad'));
        } else {
            abort(404, __('Activity not found.'));
        }
    }

    public function edit(Actividad $actividad)
    {
        $unidades = Unidad::cursoActual()->orderBy('codigo')->orderBy('slug')->get();
        $siguiente = $actividad->siguiente;
        $actividades = Actividad::cursoActual()->where('id', '!=', $actividad->id)->orderBy('slug')->orderBy('id')->get();
        $plantillas = Actividad::cursoActual()->plantilla()->where('id', '!=', $actividad->id)->orderBy('slug')->orderBy('id')->get();
        $qualifications = Qualification::organizacionActual()->orderBy('name')->get();

        return view('actividades.edit', compact(['actividad', 'unidades', 'actividades', 'plantillas', 'qualifications']));
    }

    public function update(Request $request, Actividad $actividad)
    {
        $this->validate($request, [
            'unidad_id' => 'required',
            'nombre' => 'required',
        ]);

        $actividad->update([
            'unidad_id' => $request->input('unidad_id'),

            'nombre' => $request->input('nombre'),
            'descripcion' => $request->input('descripcion'),
            'puntuacion' => $request->input('puntuacion'),

            'plantilla' => $request->has('plantilla'),
            'final' => $request->has('final'),
            'auto_avance' => $request->has('auto_avance'),

            'siguiente_id' => $request->input('siguiente_id'),

            'slug' => strlen($request->input('slug')) > 0
                ? Str::slug($request->input('slug'))
                : Str::slug($request->input('nombre')),

            'qualification_id' => request('qualification_id'),

            'orden' => request('orden'),

            'fecha_disponibilidad' => request('fecha_disponibilidad'),
            'fecha_entrega' => request('fecha_entrega'),
            'fecha_limite' => request('fecha_limite'),

            'destacada' => $request->has('destacada'),
            'tags' => request('tags'),

            'multiplicador' => request('multiplicador'),

            'siguiente_overriden' => $actividad->siguiente_id != request('siguiente_id'),
        ]);

        return retornar();
    }

    public function destroy(Actividad $actividad)
    {
        $actividad->delete();

        return back();
    }

    public function actualizarEstado(Tarea $tarea, Request $request)
    {
        $nuevoestado = $request->input('nuevoestado');

        $estado_anterior = $tarea->estado;

        $actividad = $tarea->actividad;
        $usuario = $tarea->user;

        $registro = new Registro();
        $registro->user_id = $usuario->id;
        $registro->tarea_id = $tarea->id;
        $registro->timestamp = now();
        $registro->estado = $nuevoestado;
        $registro->curso_id = Auth::user()->curso_actual()->id;

        switch ($nuevoestado) {
            case 10:
                break;
            case 20:
                if (!in_array($estado_anterior, [10])) {
                    return abort(400, __('Invalid task state.'));
                }

                $tarea->estado = 20;
                break;
            case 30:
                // Notificar que hay una actividad para corregir
                if (!$tarea->actividad->auto_avance) {
                    foreach ($tarea->actividad->unidad->curso->profesores as $profesor) {
                        if (setting_usuario('notificacion_tarea_enviada', $profesor))
                            Mail::to($profesor)->queue(new TareaEnviada($tarea));
                    }
                }

                $tarea->user->last_active = now();
                $tarea->user->save();

                $tarea->save();

                if (!$tarea->actividad->auto_avance) {
                    $this->mostrarSiguienteActividad($actividad, $usuario);
                    $this->bloquearRepositorios($tarea, true);
                    $tarea->archiveFiles();
                }
                break;

            // Reiniciada (botón de reset, para cuando se confunden y envian sin querer)
            case 31:
                $tarea->estado = 20;
                break;

            // Reabierta (consume un intento y resta puntuación)
            case 32:
                $tarea->estado = 20;

                $this->bloquearRepositorios($tarea, false);

                if (is_null($tarea->puntuacion))
                    $tarea->puntuacion = $tarea->actividad->puntuacion - 5;
                else
                    $tarea->decrement('puntuacion', 5);

                $tarea->feedback .= '<p>=== ' . __('Reopened activity') . ' (v' . ($tarea->intentos + 1) . ')' . ' ===</p>';
                $tarea->increment('intentos');

                $registro->detalles = $tarea->feedback;

                $tarea->user->last_active = now();
                $tarea->user->save();

                break;

            // Revisada: ERROR
            case 41:
                $this->bloquearRepositorios($tarea, false);

            // Revisada: OK
            case 40:
                $tarea->puntuacion = $request->input('puntuacion');
                $tarea->feedback = $request->input('feedback');
                $tarea->increment('intentos');

                $registro->detalles = $tarea->feedback;

                $plazo = now()->addDays($actividad->unidad->curso->plazo_actividad);
                $actividad->fecha_entrega = $plazo;
                $actividad->fecha_limite = $plazo;
                $actividad->save();

                $tarea->user->last_active = now();
                $tarea->user->save();

                $tarea->archiveFiles();

                if (setting_usuario('notificacion_feedback_recibido', $tarea->user))
                    Mail::to($tarea->user->email)->queue(new FeedbackRecibido($tarea));
                break;

            // Avance automático
            case 42:
                $tarea->feedback = __('Automatically completed task, not reviewed by any teacher.');
                $tarea->puntuacion = $actividad->puntuacion;
                break;
            case 50:
                break;
            case 60:
            case 62:
                $tarea->save();
                $this->bloquearRepositorios($tarea, true);
                $tarea->archiveFiles();
                $this->mostrarSiguienteActividad($actividad, $usuario);
                break;
            case 70:
                $actividad->final = !$actividad->final;
                $actividad->save();
                return back();
                break;
            case 71:
                $tarea->estado = $estado_anterior;
                $this->mostrarSiguienteActividad($actividad, $usuario, true);
                break;
            default:
        }

        $tarea->save();

        $registro->save();

        if (Auth::user()->hasRole('alumno')) {
            return redirect(route('users.home'));
        } else if (Auth::user()->hasRole('profesor')) {
            return redirect(route('profesor.tareas', ['user' => $tarea->user->id]));
        } else {
            return redirect(route('home'));
        }
    }

    public function duplicar(Actividad $actividad)
    {
        $this->crear_duplicado($actividad);

        return back();
    }

    private function crear_duplicado(Actividad $actividad, $unidad_id = null)
    {
        $clon = $actividad->duplicate();
        $clon->plantilla = $actividad->plantilla;
        $clon->siguiente = null;
        $clon->nombre = $clon->nombre . " (" . __("Copy") . ')';
        $clon->slug = Str::slug($clon->nombre);

        $clon->save();
        $clon->orden = $clon->id;

        if (!is_null($unidad_id))
            $clon->unidad_id = $unidad_id;

        $clon->save();
    }

    public function duplicar_grupo(Request $request)
    {
        $this->validate($request, [
            'seleccionadas' => 'required',
        ]);

        foreach ($request->input('seleccionadas') as $id) {
            $actividad = Actividad::where('id', $id)->first();
            $this->crear_duplicado($actividad, $request->input('unidad_id'));
        }

        return back();
    }

    private function mostrarSiguienteActividad(Actividad $actividad, User $usuario, bool $sin_limite = false)
    {
        // Calcular el límite máximo de actividades: Usuario -> Curso -> 1000
        $max_simultaneas = $usuario->max_simultaneas ?? $usuario->curso_actual()->max_simultaneas ?? 1000;

        // Pasar a la siguiente si no es final y no hay otra activa
        if (!is_null($actividad->siguiente)
            && $actividad->siguiente->plantilla
            && ($usuario->actividades_enviadas_noautoavance()->tag('base')->count() < $max_simultaneas || $sin_limite)) {

            // Crear el clon de la siguiente y guardarlo
            $plantilla = Actividad::find($actividad->plantilla_id);

            if ($plantilla->siguiente_id != $actividad->siguiente_id && !$actividad->siguiente_overriden) {
                $clon = $plantilla->siguiente->duplicate();
            } else {
                $clon = $actividad->siguiente->duplicate();
            }

            $clon->plantilla_id = $actividad->siguiente->id;
            $ahora = now();
            $clon->fecha_disponibilidad = $ahora;
            $plazo = $ahora->addDays($actividad->unidad->curso->plazo_actividad);
            $clon->fecha_entrega = $plazo;
            $clon->fecha_limite = $plazo;
            $clon->save();

            $actividad->siguiente_id = null;
            $actividad->save();

            if (!$actividad->final) {
                $usuario->actividades()->attach($clon);

                // Notificar
                $asignada = "- " . $clon->unidad->nombre . " - " . $clon->nombre . ".\n";
                if (setting_usuario('notificacion_actividad_asignada', $usuario))
                    Mail::to($usuario->email)->queue(new ActividadAsignada($usuario->name, $asignada));
            } else {
                // Oculta
                $usuario->actividades()->attach($clon, ['estado' => 11]);
            }

            // Registrar la nueva tarea
            $nueva_tarea = Tarea::where('user_id', $usuario->id)->where('actividad_id', $clon->id)->first();

            Registro::create([
                'user_id' => $usuario->id,
                'tarea_id' => $nueva_tarea->id,
                'estado' => !$actividad->final ? 10 : 11,
                'timestamp' => now(),
                'curso_id' => Auth::user()->curso_actual()->id,
            ]);
        }
    }

    public function reordenar(Actividad $a1, Actividad $a2)
    {
        $temp = $a1->orden;
        $a1->orden = $a2->orden;
        $a2->orden = $temp;

        $a1->save();
        $a2->save();

        return back();
    }

    private function bloquearRepositorios(Tarea $tarea, bool $solo_lectura)
    {
        foreach ($tarea->actividad->intellij_projects as $intellij_project) {

            if ($solo_lectura) {
                $intellij_project->archive();
            } else {
                $intellij_project->unarchive();
            }
        }
    }
}
