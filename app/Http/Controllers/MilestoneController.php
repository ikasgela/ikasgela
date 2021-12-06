<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Milestone;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $curso_actual = Curso::find(setting_usuario('curso_actual'));

        $milestones = $curso_actual?->milestones()->orderBy('date')->get();

        return view('milestones.index', compact(['milestones']));
    }

    public function create()
    {
        $curso_actual = Curso::find(setting_usuario('curso_actual'));

        return view('milestones.create', compact(['curso_actual']));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'curso_id' => 'required',
            'name' => 'required',
            'date' => 'required',
        ]);

        Milestone::create([
            'curso_id' => $request->input('curso_id'),
            'name' => $request->input('name'),
            'date' => $request->input('date'),
            'published' => $request->has('published'),
        ]);

        return retornar();
    }

    public function show(Milestone $milestone)
    {
        abort(404);
    }

    public function edit(Milestone $milestone)
    {
        $cursos = Curso::orderBy('nombre')->get();

        return view('milestones.edit', compact(['milestone', 'cursos']));
    }

    public function update(Request $request, Milestone $milestone)
    {
        $this->validate($request, [
            'curso_id' => 'required',
            'name' => 'required',
            'date' => 'required',
        ]);

        $milestone->update([
            'curso_id' => $request->input('curso_id'),
            'name' => $request->input('name'),
            'date' => $request->input('date'),
            'published' => $request->has('published'),
        ]);

        return retornar();
    }

    public function destroy(Milestone $milestone)
    {
        $milestone->delete();

        return back();
    }
}
