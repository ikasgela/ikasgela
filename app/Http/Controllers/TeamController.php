<?php

namespace App\Http\Controllers;

use App\Curso;
use App\Group;
use App\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $teams = Team::all();

        return view('teams.index', compact('teams'));
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
        return abort(501);
    }

    public function edit(Team $team)
    {
        $groups = Group::orderBy('name')->get();

        $curso_actual = Curso::find(setting_usuario('curso_actual'));
        $alumnos = $curso_actual->users()->rolAlumno()->orderBy('surname')->orderBy('name');

        $users_seleccionados = $team->users()->orderBy('surname')->orderBy('name')->get();
        $filtro = $team->users()->pluck('user_id')->unique()->flatten()->toArray();
        $users_disponibles = $alumnos->whereNotIn('user_id', $filtro)->orderBy('name')->get();

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

//        dd($request->input('users_seleccionados'));

        $team->users()->sync($request->input('users_seleccionados'));

//        foreach ($team->users()->get() as $user) {
//            $user->clearCache();
//        }

        return retornar();
    }

    public function destroy(Team $team)
    {
        $team->delete();

        return back();
    }
}
