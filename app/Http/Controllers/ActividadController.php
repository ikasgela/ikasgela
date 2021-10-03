<?php

namespace App\Http\Controllers;

use App\Exports\ActividadesCursoExport;
use App\Jobs\RunJPlag;
use App\Mail\ActividadAsignada;
use App\Mail\FeedbackRecibido;
use App\Mail\PlazoAmpliado;
use App\Mail\TareaEnviada;
use App\Models\Actividad;
use App\Models\CacheClear;
use App\Models\Curso;
use App\Models\Qualification;
use App\Models\Registro;
use App\Models\Tarea;
use App\Models\Unidad;
use App\Models\User;
use App\Traits\InformeActividadesCurso;
use App\Traits\PaginarUltima;
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

    public function index(Request $request)
    {
        $cursos = Curso::orderBy('nombre')->get();

        $request->validate([
            'curso_id' => 'numeric|integer',
        ]);

        if (request('curso_id') >= -1) {
            session(['filtrar_curso_actual' => request('curso_id')]);
        } else if (empty(session('filtrar_curso_actual'))) {
            session(['filtrar_curso_actual' => Auth::user()->curso_actual()?->id]);
        }

        if (session('filtrar_curso_actual') == -1) {
            $results = Actividad::query();
        } else {
            $results = Actividad::whereHas('unidad', function ($query) {
                return $query->where('curso_id', session('filtrar_curso_actual'));
            });
        }

        $actividades = $this->paginate_ultima($results, 250);

        session(['ubicacion' => 'actividades.index']);

        $ids = $actividades->pluck('id')->toArray();

        $todas_unidades = Unidad::orderBy('curso_id')->orderBy('orden')->get();

        return view('actividades.index', compact(['actividades', 'ids', 'todas_unidades', 'cursos']));
    }

    public function export()
    {
        return Excel::download(new ActividadesCursoExport, 'actividades.xlsx');
    }

    public function plantillas(Request $request)
    {
        session(['ubicacion' => 'actividades.plantillas']);

        $unidades = Unidad::cursoActual()->orderBy('orden')->get();

        $todas_unidades = Unidad::orderBy('curso_id')->orderBy('orden')->get();

        if ($request->has('unidad_id_disponibles')) {
            session(['profesor_unidad_id_disponibles' => $request->input('unidad_id_disponibles')]);
        }

        if (session('profesor_unidad_id_disponibles')) {
            $actividades = Actividad::cursoActual()->plantilla()->where('unidad_id', session('profesor_unidad_id_disponibles'))->orderBy('orden')->orderBy('id');
        } else {
            $actividades = Actividad::cursoActual()->plantilla()->orderBy('orden')->orderBy('id');
        }

        $actividades = $this->paginate_ultima($actividades, 250);

        $ids = $actividades->pluck('id')->toArray();

        return view('actividades.plantillas', compact(['actividades', 'unidades', 'ids', 'todas_unidades']));
    }

    public function create()
    {
        $unidades = Unidad::cursoActual()->orderBy('orden')->get();
        $actividades = Actividad::cursoActual()->plantilla()->orderBy('slug')->orderBy('id')->get();
        $qualifications = Qualification::cursoActual()->orderBy('name')->get();

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

    public function show(Actividad $actividad)
    {
        $ids = $actividad->recursos->pluck('pivot.orden')->toArray();

        return view('actividades.show', compact(['actividad', 'ids']));
    }

    public function preview(Actividad $actividad)
    {
        $user = Auth::user();

        if ($user->hasAnyRole(['admin', 'profesor']) || $user->hasRole('alumno') && $actividad->plantilla) {

            $feedbacks = $actividad->feedbacks()->orderBy('orden')->get();
            $ids = $feedbacks->pluck('id')->toArray();

            return view('actividades.preview', compact(['actividad', 'feedbacks', 'ids']));
        } else {
            abort(404, __('Activity not found.'));
        }
    }

    public function edit(Actividad $actividad)
    {
        $unidades = $actividad->unidad->curso->unidades()->orderBy('orden')->get();
        $siguiente = $actividad->siguiente;
        $actividades = $actividad->unidad->curso->actividades()
            ->where('actividades.id', '!=', $actividad->id)
            ->orderBy('slug')->orderBy('id')->get();
        $plantillas = $actividad->unidad->curso->actividades()
            ->plantilla()
            ->where('actividades.id', '!=', $actividad->id)
            ->orderBy('slug')->orderBy('id')->get();
        $qualifications = $actividad->unidad->curso->qualifications()->orderBy('name')->get();

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
        $usuario_actual = Auth::user();

        $override_allowed = $usuario_actual->hasAnyRole(['admin', 'profesor']);

        if ($tarea->user_id != $usuario_actual->id && !$override_allowed && !$tarea->actividad->hasEtiqueta('trabajo en equipo'))
            abort('403');

        $nuevoestado = $request->input('nuevoestado');

        $estado_anterior = $tarea->estado;

        $actividad = $tarea->actividad;
        $usuario = $tarea->user;

        $registro = new Registro();
        $registro->user_id = $usuario->id;
        $registro->tarea_id = $tarea->id;
        $registro->timestamp = now();
        $registro->estado = $nuevoestado;
        $registro->curso_id = $usuario_actual->curso_actual()->id;

        switch ($nuevoestado) {
            case 10:
                if (!in_array($estado_anterior, [11]) && !$override_allowed) {
                    abort(400, __('Invalid task state.'));
                }

                $tarea->estado = $nuevoestado;
                break;
            case 20:
                if (!in_array($estado_anterior, [10]) && !$override_allowed) {
                    abort(400, __('Invalid task state.'));
                }

                $tarea->estado = $nuevoestado;
                break;
            case 21:
                if (!in_array($estado_anterior, [41]) && !$override_allowed) {
                    abort(400, __('Invalid task state.'));
                }

                $tarea->estado = $nuevoestado;
                break;
            case 30:
                if (!in_array($estado_anterior, [20, 21]) && !$override_allowed) {
                    abort(400, __('Invalid task state.'));
                }

                $tarea->estado = $nuevoestado;

                // Notificar que hay una actividad para corregir
                if (!$tarea->actividad->auto_avance) {
                    foreach ($tarea->actividad->unidad->curso->profesores as $profesor) {
                        if (setting_usuario('notificacion_tarea_enviada', $profesor))
                            Mail::to($profesor)->queue(new TareaEnviada($tarea));
                    }
                }

                // If there are repositories, run JPlag on them
                if ($tarea->actividad->intellij_projects->count() > 0) {
                    RunJPlag::dispatch($tarea);
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
                if (!in_array($estado_anterior, [30]) && !$override_allowed) {
                    abort(400, __('Invalid task state.'));
                }

                $tarea->estado = 20;

                $this->bloquearRepositorios($tarea, false);
                break;

            // Reabierta (consume un intento y resta puntuación si no es una tarea de examen)
            case 32:
                if (!in_array($estado_anterior, [30]) && !$override_allowed) {
                    abort(400, __('Invalid task state.'));
                }

                $tarea->estado = 20;

                $this->bloquearRepositorios($tarea, false);

                if (!$actividad->hasEtiqueta('examen')) {
                    if (is_null($tarea->puntuacion))
                        $tarea->puntuacion = $tarea->actividad->puntuacion - 5;
                    else
                        $tarea->decrement('puntuacion', 5);
                }

                $tarea->feedback .= '<p>=== ' . __('Reopened activity') . ' (v' . ($tarea->intentos + 1) . ')' . ' ===</p>';
                $tarea->increment('intentos');

                $registro->detalles = $tarea->feedback;

                $tarea->user->last_active = now();
                $tarea->user->save();

                break;

            // Revisada: ERROR
            /** @noinspection PhpMissingBreakStatementInspection */
            case 41:
                if (!in_array($estado_anterior, [30, 31]) && !$override_allowed) {
                    abort(400, __('Invalid task state.'));
                }

                $tarea->estado = $nuevoestado;

                $this->bloquearRepositorios($tarea, false);

            // Revisada: OK
            case 40:
                if (!in_array($estado_anterior, [30, 31]) && !$override_allowed) {
                    abort(400, __('Invalid task state.'));
                }

                $tarea->estado = $nuevoestado;

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
                if (!in_array($estado_anterior, [30, 31]) && !$override_allowed) {
                    abort(400, __('Invalid task state.'));
                }

                $tarea->estado = $nuevoestado;

                $tarea->feedback = __('Automatically completed task, not reviewed by any teacher.');
                $tarea->puntuacion = $actividad->puntuacion;
                break;
            case 50:
                if (!in_array($estado_anterior, [40, 41, 42]) && !$override_allowed) {
                    abort(400, __('Invalid task state.'));
                }

                $tarea->estado = $nuevoestado;
                break;
            case 60:
            case 62:
                if (!in_array($estado_anterior, [40, 41, 42, 50]) && !$override_allowed) {
                    abort(400, __('Invalid task state.'));
                }

                $tarea->estado = $nuevoestado;

                $tarea->save();
                $this->bloquearRepositorios($tarea, true);
                $tarea->archiveFiles();
                $this->mostrarSiguienteActividad($actividad, $usuario);
                break;

            // Ampliar plazo
            case 63:
                if (!$tarea->is_expired && !$override_allowed) {
                    abort(400, __('Invalid task state.'));
                }

                $dias = $request->input('ampliacion_plazo', 7);
                $plazo = now()->addDays($dias);
                $actividad->fecha_entrega = $plazo;
                $actividad->fecha_limite = $plazo;
                $actividad->save();

                $this->bloquearRepositorios($tarea, false);

                if (setting_usuario('notificacion_actividad_asignada', $usuario))
                    Mail::to($usuario->email)->queue(new PlazoAmpliado($usuario->name, $actividad->nombre));
                break;
            case 70:
                $tarea->estado = $nuevoestado;

                $actividad->final = !$actividad->final;
                $actividad->save();
                return back();
                break;
            case 71:
                $tarea->estado = $estado_anterior;

                $this->mostrarSiguienteActividad($actividad, $usuario, true);
                break;
            default:
                abort(400, __('Invalid task state.'));
        }

        // Si es compartida, sincronizar el estado con los demás componentes del equipo
        if ($actividad->hasEtiqueta('trabajo en equipo')) {
            $compartidas = Tarea::where('actividad_id', $actividad->id)->get();
            foreach ($compartidas as $compartida) {
                $compartida->estado = $tarea->estado;
                $compartida->feedback = $tarea->feedback;
                $compartida->puntuacion = $tarea->puntuacion;
                $compartida->intentos = $tarea->intentos;
                $compartida->save();
            }

            $compartida->user->last_active = now();
            $compartida->user->save();
        }

        $tarea->save();

        $registro->save();

        if ($usuario_actual->hasRole('alumno')) {
            return redirect(route('users.home'));
        } else if ($usuario_actual->hasRole('profesor')) {
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

    private function mover(Actividad $actividad, $unidad_id = null)
    {
        if (!is_null($unidad_id)) {
            $actividad->unidad_id = $unidad_id;
            $actividad->save();
        }
    }

    private function mover_multiple(Actividad $actividad, Actividad $destino)
    {
        $actividades = Actividad::cursoActual()->plantilla();

        if (session('profesor_unidad_id_disponibles')) {
            $actividades = $actividades->where('unidad_id', session('profesor_unidad_id_disponibles'));
        }

        if ($actividad->orden < $destino->orden) {
            // Mover hacia abajo en la tabla
            $actividades = $actividades->whereBetween('orden', [$actividad->orden, $destino->orden]);

            $actividades = $actividades->orderBy('orden')->get();

            $a1 = $actividades->first();
            for ($i = 0; $i < $actividades->count() - 1; $i++) {
                $a2 = $actividades->slice($i + 1, 1)->first();
                $this->reordenar($a1, $a2);
            }
        } elseif ($actividad->orden > $destino->orden) {
            // Mover hacia arriba en la tabla
            $actividades = $actividades->whereBetween('orden', [$destino->orden, $actividad->orden]);

            $actividades = $actividades->orderBy('orden')->get();

            $a1 = $actividades->first();
            for ($i = $actividades->count(); $i > 0; $i--) {
                $a2 = $actividades->slice($i - 1, 1)->first();
                $this->reordenar($a1, $a2);
            }
        }
    }

    public function duplicar_grupo(Request $request)
    {
        $this->validate($request, [
            'seleccionadas' => 'required',
            'action' => 'required',
        ]);

        // Al mover varias, habría que empezar por el final para que queden en orden

        switch (request('action')) {
            case 'duplicate':
                foreach ($request->input('seleccionadas') as $id) {
                    $actividad = Actividad::findOrFail($id);
                    $this->crear_duplicado($actividad, $request->input('unidad_id'));
                }
                break;
            case 'move':
                foreach ($request->input('seleccionadas') as $id) {
                    $actividad = Actividad::findOrFail($id);
                    $this->mover($actividad, $request->input('unidad_id'));
                }
                break;
            default:
                $accion = explode('_', request('action'))[0];
                $id_destino = explode('_', request('action'))[1];
                if ($accion == 'mm' && is_numeric($id_destino)) {
                    $destino = Actividad::findOrFail($id_destino);

                    $bajar = Actividad::whereIn('id', request('seleccionadas'))->where('orden', '<', $destino->orden)->get()->pluck('id')->toArray();
                    foreach ($bajar as $id) {
                        $actividad = Actividad::findOrFail($id);
                        $this->mover_multiple($actividad, $destino);
                    }

                    $subir = Actividad::whereIn('id', request('seleccionadas'))->where('orden', '>', $destino->orden)->orderBy('orden', 'desc')->get()->pluck('id')->toArray();
                    foreach ($subir as $id) {
                        $actividad = Actividad::findOrFail($id);
                        $this->mover_multiple($actividad, $destino);
                    }
                }
                break;
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
                $clon->plantilla_id = $plantilla->siguiente->id;
            } else {
                $clon = $actividad->siguiente->duplicate();
                $clon->plantilla_id = $actividad->siguiente->id;
            }

            $ahora = now();
            $clon->fecha_disponibilidad = $ahora;
            $plazo = $ahora->addDays($actividad->unidad->curso->plazo_actividad);
            $clon->fecha_entrega = $plazo;
            $clon->fecha_limite = $plazo;
            $clon->save();
            $clon->orden = $clon->id;
            $clon->save();

            $actividad->siguiente_id = null;
            $actividad->save();

            // Dejar pendiente el borrado de caché para cuando llegue la fecha
            CacheClear::create(['fecha' => $clon->fecha_disponibilidad, 'user_id' => $usuario->id]);
            CacheClear::create(['fecha' => $clon->fecha_entrega, 'user_id' => $usuario->id]);
            CacheClear::create(['fecha' => $clon->fecha_limite, 'user_id' => $usuario->id]);

            if (!$actividad->final) {
                // Pendiente de aceptar
                $usuario->actividades()->attach($clon, ['estado' => 10]);

                // Notificar
                if (setting_usuario('notificacion_actividad_asignada', $usuario)) {
                    $asignada = "- " . $clon->unidad->nombre . " - " . $clon->nombre . ".\n";
                    Mail::to($usuario->email)->queue(new ActividadAsignada($usuario->name, $asignada));
                }
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

    public function reordenar_recursos(Request $request, Actividad $actividad)
    {
        $recursos = $actividad->recursos->keyBy('pivot.orden');

        $a1 = $recursos->get(request('a1'));
        $a2 = $recursos->get(request('a2'));

        $temp = $a1->pivot->orden;
        $a1->pivot->orden = $a2->pivot->orden;
        $a2->pivot->orden = $temp;

        $a1->pivot->save();
        $a2->pivot->save();

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
