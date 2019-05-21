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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $actividades = Actividad::all();

        session(['ubicacion' => 'actividades.index']);

        return view('actividades.index', compact('actividades'));
    }

    public function plantillas(Request $request)
    {
        session(['ubicacion' => 'actividades.plantillas']);

        $unidades = Unidad::orderBy('nombre')->get();

        if ($request->has('unidad_id')) {
            session(['profesor_unidad_actual' => $request->input('unidad_id')]);
        }

        if (session('profesor_unidad_actual')) {
            $actividades = Actividad::where('plantilla', true)->where('unidad_id', session('profesor_unidad_actual'))->get();
        } else {
            $actividades = Actividad::where('plantilla', true)->get();
        }

        return view('actividades.plantillas', compact(['actividades', 'unidades']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $unidades = Unidad::orderBy('nombre')->get();
        $actividades = Actividad::whereNull('siguiente_id')->orderBy('nombre')->get();
        $qualifications = Qualification::orderBy('name')->get();

        return view('actividades.create', compact(['unidades', 'actividades', 'qualifications']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
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

        if (!is_null($request->input('siguiente_id'))) {
            $siguiente = Actividad::find($request->input('siguiente_id'));
            $actividad->siguiente()->save($siguiente);
        }

        switch (session('ubicacion')) {
            case 'actividades.index':
                return redirect(route('actividades.index'));
            case 'actividades.plantillas':
                return redirect(route('actividades.plantillas'));
        }
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Actividad $actividad
     * @return \Illuminate\Http\Response
     */
    public function edit(Actividad $actividad)
    {
        $unidades = Unidad::orderBy('nombre')->get();
        $siguiente = !is_null($actividad->siguiente) ? $actividad->siguiente->id : null;
        $actividades = Actividad::where('id', '!=', $actividad->id)->whereNull('siguiente_id')->orWhere('id', $siguiente)->orderBy('nombre')->get();
        $plantillas = Actividad::where('plantilla', true)->where('id', '!=', $actividad->id)->whereNull('siguiente_id')->orWhere('id', $siguiente)->orderBy('nombre')->get();
        $qualifications = Qualification::orderBy('name')->get();

        return view('actividades.edit', compact(['actividad', 'unidades', 'actividades', 'plantillas', 'qualifications']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Actividad $actividad
     * @return \Illuminate\Http\Response
     */
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

        switch (session('ubicacion')) {
            case 'actividades.index':
                return redirect(route('actividades.index'));
            case 'actividades.plantillas':
                return redirect(route('actividades.plantillas'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Actividad $actividad
     * @return \Illuminate\Http\Response
     */
    public function destroy(Actividad $actividad)
    {
        $actividad->delete();

        return redirect(route('actividades.index'));
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
                if (!$tarea->actividad->auto_avance) {
                    Mail::to('info@ikasgela.com')->queue(new TareaEnviada($tarea));
                }
                break;
            case 31:
                $tarea->estado = 20;    // Botón de reset, para cuando se confunden
                break;
            case 40:
                $tarea->puntuacion = $request->input('puntuacion');
            case 41:
                $tarea->feedback = $request->input('feedback');
                $registro->detalles = $tarea->feedback;
                Mail::to($tarea->user->email)->queue(new FeedbackRecibido($tarea));
                break;
            case 42:
                $tarea->feedback = 'Tarea completada automáticamente, no revisada por ningún profesor.';
                $tarea->puntuacion = $actividad->puntuacion;
                break;
            case 50:
                break;
            case 60:
                // Pasar a la siguiente si no es final
                if (!is_null($actividad->siguiente)) {
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

                    Registro::create([
                        'user_id' => $usuario->id,
                        'tarea_id' => $nueva_tarea->id,
                        'estado' => !$actividad->final ? 10 : 11,
                        'timestamp' => Carbon::now(),
                    ]);
                }
                break;
            default:
        }

        $tarea->save();

        $registro->save();

        if (Auth::user()->hasRole('alumno')) {
            return redirect(route('users.home'));
        } else if (Auth::user()->hasRole('profesor')) {
            return redirect(route('profesor.tareas', ['usuario' => $tarea->user->id]));
        } else {
            return redirect(route('home'));
        }
    }

}
