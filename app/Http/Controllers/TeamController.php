<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\CacheClear;
use App\Models\Curso;
use App\Models\Registro;
use App\Models\Tarea;
use App\Models\Team;
use App\Models\Unidad;
use App\Models\User;
use App\Traits\PaginarUltima;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    use PaginarUltima;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin')->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        $curso_actual = Auth::user()->curso_actual();

        $teams = $curso_actual?->teams()->get() ?? [];

        $unidades = Unidad::organizacionActual()->cursoActual()->orderBy('orden')->get();

        if ($request->has('unidad_id_disponibles')) {
            session(['profesor_unidad_id_disponibles' => $request->input('unidad_id_disponibles')]);
        }

        $disponibles = $this->actividadesDisponibles();

        return view('teams.index', compact(['teams', 'unidades', 'disponibles']));
    }

    public function create()
    {
        $curso_actual = Auth::user()->curso_actual();

        $groups = $curso_actual?->groups()->orderBy('name')->get() ?? [];

        return view('teams.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'group_id' => 'required',
            'name' => [
                'required',
                function ($attribute, $value, $fail) {
                    $exists = Team::whereGroupId(request('group_id'))->whereSlug(Str::slug(request('name')))->exists();
                    if ($exists) {
                        $fail(__('The team name is not unique.'));
                    }
                },
            ],
        ]);

        Team::create([
            'group_id' => request('group_id'),
            'name' => request('name'),
            'slug' => Str::slug(request('name'))
        ]);

        return retornar();
    }

    public function show(Team $team, Request $request)
    {
        $unidades = Unidad::organizacionActual()->cursoActual()->orderBy('orden')->get();

        if ($request->has('unidad_id_disponibles')) {
            session(['profesor_unidad_id_disponibles' => $request->input('unidad_id_disponibles')]);
        }

        if ($request->has('unidad_id_asignadas')) {
            session(['profesor_unidad_id_asignadas' => $request->input('unidad_id_asignadas')]);
        }

        $disponibles = $this->actividadesDisponibles();

        $asignadas = $this->actividadesAsignadas($team);

        return view('teams.show', compact(['team', 'unidades', 'disponibles', 'asignadas']));
    }

    public function edit(Team $team)
    {
        $curso_actual = Auth::user()->curso_actual();

        $groups = $curso_actual?->groups()->with('teams')->orderBy('name')->get() ?? [];

        $curso_actual = Curso::find(setting_usuario('curso_actual'));
        $alumnos = $curso_actual?->users()->rolAlumno()->noBloqueado()->orderBy('surname')->orderBy('name');

        $users_seleccionados = $team->users()->orderBy('surname')->orderBy('name')->get();
        $filtro = $team->users()->pluck('user_id')->unique()->flatten()->toArray();
        $users_disponibles = $alumnos?->whereNotIn('user_id', $filtro)->orderBy('name')->get() ?? [];

        return view('teams.edit', compact(['team', 'groups', 'users_seleccionados', 'users_disponibles']));
    }

    public function update(Request $request, Team $team)
    {
        $this->validate($request, [
            'group_id' => 'required',
            'name' => 'required',
        ]);

        $team->update([
            'group_id' => request('group_id'),
            'name' => request('name'),
            'slug' => strlen((string)request('slug')) > 0
                ? Str::slug(request('slug'))
                : Str::slug(request('name'))
        ]);

        $result = $team->users()->sync($request->input('users_seleccionados') ?? []);

        $curso_id = Auth::user()->curso_actual()?->id;
        $actividades_equipo = $team->actividades()->get();

        // Asignar las actividades del equipo a los nuevos miembros
        foreach ($result['attached'] as $user_id) {
            $user = User::find($user_id);
            if (!$user) continue;

            foreach ($actividades_equipo as $actividad) {
                $ya_asignada = Tarea::where('user_id', $user->id)
                    ->where('actividad_id', $actividad->id)
                    ->exists();

                if (!$ya_asignada) {
                    CacheClear::create(['fecha' => $actividad->fecha_disponibilidad, 'user_id' => $user->id]);
                    CacheClear::create(['fecha' => $actividad->fecha_entrega, 'user_id' => $user->id]);
                    CacheClear::create(['fecha' => $actividad->fecha_limite, 'user_id' => $user->id]);

                    $user->actividades()->attach($actividad);
                    $user->clearCache();

                    $tarea = Tarea::where('user_id', $user->id)->where('actividad_id', $actividad->id)->first();
                    if ($tarea && $curso_id) {
                        Registro::create([
                            'user_id' => $user->id,
                            'tarea_id' => $tarea->id,
                            'estado' => 10,
                            'timestamp' => Carbon::now(),
                            'curso_id' => $curso_id,
                        ]);
                    }
                }
            }
        }

        // Borrar las tareas de los miembros eliminados sin borrar la actividad compartida
        foreach ($result['detached'] as $user_id) {
            $user = User::find($user_id);
            if (!$user) continue;

            foreach ($actividades_equipo as $actividad) {
                $tarea = Tarea::where('user_id', $user->id)
                    ->where('actividad_id', $actividad->id)
                    ->whereNull('deleted_at')
                    ->first();

                if ($tarea) {
                    if ($curso_id) {
                        Registro::create([
                            'user_id' => $user->id,
                            'tarea_id' => $tarea->id,
                            'estado' => 61,
                            'timestamp' => Carbon::now(),
                            'curso_id' => $curso_id,
                        ]);
                    }
                    $tarea->delete();
                    $user->clearCache();
                }
            }
        }

        return retornar();
    }

    public function destroy(Team $team)
    {
        $team->delete();

        return back();
    }

    private function actividadesDisponibles()
    {
        $actividades_curso = Actividad::plantilla()->cursoActual()->orderBy('orden');

        if (session('profesor_unidad_id_disponibles')) {
            $disponibles = $actividades_curso->where('unidad_id', session('profesor_unidad_id_disponibles'));
        } else {
            $disponibles = $actividades_curso;
        }

        return $this->paginate_ultima($disponibles, config('ikasgela.pagination_short'), 'disponibles');
    }

    private function actividadesAsignadas(Team $team)
    {
        $actividades_equipo = $team->actividades()->cursoActual();

        if (session('profesor_unidad_id_asignadas')) {
            $asignadas = $actividades_equipo->where('unidad_id', session('profesor_unidad_id_asignadas'));
        } else {
            $asignadas = $actividades_equipo;
        }

        return $this->paginate_ultima($asignadas, config('ikasgela.pagination_short'), 'asignadas');
    }
}
