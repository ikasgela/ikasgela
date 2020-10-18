<?php

namespace App\Http\Controllers;

use App\Group;
use App\Team;
use BadMethodCallException;
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

        return view('teams.edit', compact(['team', 'groups']));
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

        return retornar();
    }

    public function destroy(Team $team)
    {
        $team->delete();

        return back();
    }
}
