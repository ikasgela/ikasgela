<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Group;
use App\Models\Period;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $groups = Group::all();

        return view('groups.index', compact('groups'));
    }

    public function create()
    {
        $periods = Period::orderBy('name')->get();

        return view('groups.create', compact('periods'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'period_id' => 'required',
            'name' => 'required',
        ]);

        Group::create([
            'period_id' => request('period_id'),
            'name' => request('name'),
            'slug' => Str::slug(request('name'))
        ]);

        return retornar();
    }

    public function show(Group $group)
    {
        abort(404);
    }

    public function edit(Group $group)
    {
        $periods = Period::organizacionActual()->orderBy('name')->get();

        $cursos_seleccionados = $group->cursos()->orderBy('nombre')->get();

        $filtro = $group->cursos()->pluck('curso_id')->unique()->flatten()->toArray();
        $cursos_disponibles = Curso::organizacionActual()->whereNotIn('id', $filtro)->orderBy('nombre')->get();

        return view('groups.edit', compact(['group', 'periods', 'cursos_seleccionados', 'cursos_disponibles']));
    }

    public function update(Request $request, Group $group)
    {
        $this->validate($request, [
            'period_id' => 'required',
            'name' => 'required',
        ]);

        $group->update([
            'period_id' => request('period_id'),
            'name' => request('name'),
            'slug' => strlen(request('slug')) > 0
                ? Str::slug(request('slug'))
                : Str::slug(request('name'))
        ]);

        $group->cursos()->sync($request->input('cursos_seleccionados'));

        return retornar();
    }

    public function destroy(Group $group)
    {
        $group->delete();

        return back();
    }
}
