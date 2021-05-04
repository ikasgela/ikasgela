<?php

namespace App\Http\Controllers;

use App\Actividad;
use App\Curso;
use App\Group;
use App\Team;
use App\Traits\PaginarUltima;
use App\Unidad;
use App\User;
use Illuminate\Http\Request;
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
        $teams = Team::all();

        $unidades = Unidad::organizacionActual()->cursoActual()->orderBy('codigo')->orderBy('nombre')->get();

        if ($request->has('unidad_id_disponibles')) {
            session(['profesor_unidad_id_disponibles' => $request->input('unidad_id_disponibles')]);
        }

        $disponibles = $this->actividadesDisponibles();

        return view('teams.index', compact(['teams', 'unidades', 'disponibles']));
    }

    public function create()
    {
        $groups = Group::orderBy('name')->get();

        return view('teams.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'group_id' => 'required',
            'name' => 'required',
        ]);

        Team::create([
            'group_id' => request('group_id'),
            'name' => request('name'),
            'slug' => Str::slug(request('name'))
        ]);

        return retornar();
    }

    public function show(Team $team)
    {
        return view('teams.show', compact('team'));
    }

    public function edit(Team $team)
    {
        $groups = Group::with('teams')->orderBy('name')->get();

        $curso_actual = Curso::find(setting_usuario('curso_actual'));
        $alumnos = $curso_actual?->users()->rolAlumno()->noBloqueado()->orderBy('surname')->orderBy('name');

        $users_seleccionados = $team->users()->dontRemember()->orderBy('surname')->orderBy('name')->get();
        $filtro = $team->users()->dontRemember()->pluck('user_id')->unique()->flatten()->toArray();
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
            'slug' => strlen(request('slug')) > 0
                ? Str::slug(request('slug'))
                : Str::slug(request('name'))
        ]);

        $team->users()->sync($request->input('users_seleccionados'));

        User::flushCache();

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

        return $this->paginate_ultima($disponibles, config('ikasgela.pagination_available_activities'), 'disponibles');
    }
}
