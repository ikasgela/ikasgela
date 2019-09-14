<?php

namespace App\Http\Controllers;

use App\Actividad;
use App\Mail\ActividadAsignada;
use App\Mail\FeedbackRecibido;
use App\Mail\TareaEnviada;
use App\Qualification;
use App\Registro;
use App\Tarea;
use App\Unidad;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ActividadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin')->except(['actualizarEstado', 'preview']);
    }

    public function index()
    {
        memorizar_ruta();

        $actividades = Actividad::orderBy('orden')->get();

        session(['ubicacion' => 'actividades.index']);

        $ids = $actividades->pluck('id')->toArray();

        return view('actividades.index', compact(['actividades', 'ids']));
    }

    public function plantillas(Request $request)
    {
        memorizar_ruta();

        session(['ubicacion' => 'actividades.plantillas']);

        $unidades = Unidad::cursoActual()->orderBy('codigo')->orderBy('nombre')->get();

        if ($request->has('unidad_id')) {
            session(['profesor_unidad_actual' => $request->input('unidad_id')]);
        }

        if (session('profesor_unidad_actual')) {
            $actividades = Actividad::cursoActual()->plantilla()->where('unidad_id', session('profesor_unidad_actual'))->orderBy('orden')->get();
        } else {
            $actividades = Actividad::cursoActual()->plantilla()->where('plantilla', true)->orderBy('orden')->get();
        }

        $ids = $actividades->pluck('id')->toArray();

        return view('actividades.plantillas', compact(['actividades', 'unidades', 'ids']));
    }

    public function create()
    {
        $unidades = Unidad::cursoActual()->orderBy('nombre')->get();
        $actividades = Actividad::cursoActual()->where('plantilla', true)->whereNull('siguiente_id')->orderBy('nombre')->get();
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

            'qualification_id' => request('qualification_id'),
        ]);

        $actividad->orden = $actividad->id;
        $actividad->save();

        if (!is_null($request->input('siguiente_id'))) {
            $siguiente = Actividad::find($request->input('siguiente_id'));
            $actividad->siguiente()->save($siguiente);
        }

        return redirect(ruta_memorizada());
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
        return view('actividades.show', compact('actividad'));
    }

    public function preview(Actividad $actividad)
    {
        return view('actividades.preview', compact('actividad'));
    }

    public function edit(Actividad $actividad)
    {
        $unidades = Unidad::orderBy('curso_id')->orderBy('codigo')->orderBy('nombre')->get();
        $siguiente = !is_null($actividad->siguiente) ? $actividad->siguiente->id : null;
        $actividades = Actividad::cursoActual()->where('id', '!=', $actividad->id)->whereNull('siguiente_id')->orWhere('id', $siguiente)->orderBy('nombre')->get();
        $plantillas = Actividad::cursoActual()->where('plantilla', true)->where('id', '!=', $actividad->id)->whereNull('siguiente_id')->orWhere('id', $siguiente)->orderBy('nombre')->get();
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
        ]);

        if (!is_null($request->input('siguiente_id'))) {
            $siguiente = Actividad::find($request->input('siguiente_id'));
            if (is_null($actividad->siguiente)) {
                $actividad->siguiente()->save($siguiente);
            } else {
                if ($actividad->siguiente->id != $request->input('siguiente_id')) {
                    $actividad->siguiente->siguiente_id = null;
                    $actividad->siguiente->save();
                    $actividad->siguiente()->save($siguiente);
                }
            }
        } else {
            if (!is_null($actividad->siguiente)) {
                $actividad->siguiente->siguiente_id = null;
                $actividad->siguiente->save();
            }
        }

        $actividad->save();

        return redirect(ruta_memorizada());
    }

    public function destroy(Actividad $actividad)
    {
        $actividad->delete();

        return back();
    }

    public function actualizarEstado(Tarea $tarea, Request $request)
    {
        $nuevoestado = $request->input('nuevoestado');

        $tarea->estado = $nuevoestado;

        $actividad = $tarea->actividad;
        $usuario = $tarea->user;

        $registro = new Registro();
        $registro->user_id = $usuario->id;
        $registro->tarea_id = $tarea->id;
        $registro->timestamp = Carbon::now();
        $registro->estado = $nuevoestado;

        switch ($nuevoestado) {
            case 10:
                break;
            case 20:
                break;
            case 30:
                // Notificar que hay una actividad para corregir
                if (!$tarea->actividad->auto_avance) {
                    foreach ($tarea->actividad->unidad->curso->profesores as $profesor) {
                        Mail::to($profesor)->queue(new TareaEnviada($tarea));
                    }
                }

                $tarea->user->last_active = Carbon::now();
                $tarea->user->save();

                $tarea->save();
                $this->mostrarSiguienteActividad($actividad, $usuario);
                break;
            case 31:
                $tarea->estado = 20;    // Botón de reset, para cuando se confunden
                break;
            case 40:
            case 41:
                $tarea->puntuacion = $request->input('puntuacion');
                $tarea->feedback = $request->input('feedback');
                $tarea->increment('intentos');

                $registro->detalles = $tarea->feedback;

                $tarea->user->last_active = Carbon::now();
                $tarea->user->save();

                Mail::to($tarea->user->email)->queue(new FeedbackRecibido($tarea));
                break;
            case 42:
                $tarea->feedback = 'Tarea completada automáticamente, no revisada por ningún profesor.';
                $tarea->puntuacion = $actividad->puntuacion;
                break;
            case 50:
                break;
            case 60:
                $tarea->save();
                $this->mostrarSiguienteActividad($actividad, $usuario);
                break;
            case 70:
                $actividad->final = !$actividad->final;
                $actividad->save();
                return back();
                break;
            default:
        }

        $tarea->save();

        $registro->save();

        if (isset($registro_nueva_tarea))
            $registro_nueva_tarea->save();

        if (Auth::user()->hasRole('alumno')) {
            return redirect(route('users.home'));
        } else if (Auth::user()->hasRole('profesor')) {
            return redirect(route('profesor.tareas', ['usuario' => $tarea->user->id]));
        } else {
            return redirect(route('home'));
        }
    }

    public function duplicar(Actividad $actividad)
    {
        $clon = $actividad->duplicate();
        $clon->plantilla = $actividad->plantilla;
        $clon->siguiente_id = null;
        $clon->nombre = $clon->nombre . " - " . __("Copy");
        $clon->slug = Str::slug($clon->nombre);

        $clon->save();
        $clon->orden = $clon->id;

        $clon->save();

        return back();
    }

    private function mostrarSiguienteActividad($actividad, $usuario)
    {
        // Pasar a la siguiente si no es final y no hay otra activa
        if (!is_null($actividad->siguiente) && $usuario->actividades_asignadas()->count() < 2) {
            if (!$actividad->final) {
                // Visible
                $usuario->actividades()->attach($actividad->siguiente);

                // Notificar
                $asignada = "- " . $actividad->siguiente->unidad->nombre . " - " . $actividad->siguiente->nombre . ".\n\n";
                Mail::to($usuario->email)->queue(new ActividadAsignada($usuario->name, $asignada));
            } else {
                // Oculta
                $usuario->actividades()->attach($actividad->siguiente, ['estado' => 11]);
            }

            // Registrar la nueva tarea
            $nueva_tarea = Tarea::where('user_id', $usuario->id)->where('actividad_id', $actividad->siguiente->id)->first();

            // Anular el enlace, para no volver a crear una copia si la tarea no se corrige a la primera
            $actividad->siguiente->siguiente_id = null;
            $actividad->siguiente->save();

            $registro_nueva_tarea = Registro::make([
                'user_id' => $usuario->id,
                'tarea_id' => $nueva_tarea->id,
                'estado' => !$actividad->final ? 10 : 11,
                'timestamp' => Carbon::now(),
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
}
