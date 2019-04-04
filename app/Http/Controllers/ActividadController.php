<?php

namespace App\Http\Controllers;

use App\Actividad;
use App\Tarea;
use App\Unidad;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ActividadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin')->except('actualizarEstado', 'archivo');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $actividades = Actividad::all();

        return view('actividades.index', compact('actividades'));
    }

    public function plantillas()
    {
        $actividades = Actividad::where('plantilla', true)->get();

        return view('actividades.index', compact('actividades'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $unidades = Unidad::all();
        $actividades = Actividad::whereNull('siguiente_id')->get();

        return view('actividades.create', compact(['unidades', 'actividades']));
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

            'slug' => Str::slug(request('nombre')),
        ]);

        if (!is_null($request->input('siguiente_id'))) {
            $siguiente = Actividad::find($request->input('siguiente_id'));
            $actividad->siguiente()->save($siguiente);
        }

        return redirect(route('actividades.index'));
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Actividad $actividad
     * @return \Illuminate\Http\Response
     */
    public function edit(Actividad $actividad)
    {
        $unidades = Unidad::all();
        $siguiente = !is_null($actividad->siguiente) ? $actividad->siguiente->id : null;
        $actividades = Actividad::where('id', '!=', $actividad->id)->whereNull('siguiente_id')->orWhere('id', $siguiente)->get();

        return view('actividades.edit', compact(['actividad', 'unidades', 'actividades']));
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
            'siguiente_id' => $request->input('siguiente_id'),

            'slug' => strlen($request->input('slug')) > 0
                ? Str::slug($request->input('slug'))
                : Str::slug($request->input('nombre'))
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

        return redirect(route('actividades.index'));
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
        $ahora = Carbon::now();

        $tarea->estado = $nuevoestado;

        switch ($nuevoestado) {
            case 10:
                break;
            case 20:
                $tarea->aceptada = $ahora;
                break;
            case 30:
                $tarea->enviada = $ahora;
                break;
            case 40:
                if (config('app.debug')) {
                    $tarea->feedback = 'Muy bien, buen trabajo.';
                }
                $tarea->revisada = $ahora;
                break;
            case 41:
                if (config('app.debug')) {
                    $tarea->feedback = 'Ummm, a casita a volverlo a intentar.';
                }
                $tarea->revisada = $ahora;
                break;
            case 50:
                $tarea->terminada = $ahora;
                break;
            case 60:
                // Archivar
                $tarea->archivada = $ahora;

                // Buscar la actividad y el usuario (deberían estar enlazados, pero no lo están)
                $actividad = Actividad::find($tarea->actividad_id);
                $user = Auth::user();

                // Pasar a la siguiente si no es final
                if (!is_null($actividad->siguiente)) {
                    if (!$actividad->final) {
                        $user->actividades()->attach($actividad->siguiente);
                    } else {
                        $user->actividades()->attach($actividad->siguiente, ['estado' => 11]);
                    }
                }
                break;
            default:
        }

        $tarea->save();

        return redirect(route('users.home'));
    }

    public function archivo()
    {
        $user = Auth::user();
        $actividades = $user->actividades()->wherePivot('estado', 60)->get();

        return view('actividades.archivo', compact('actividades'));
    }

}
