<?php

namespace App\Http\Controllers;

use App\Curso;
use App\Qualification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QualificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index(Request $request)
    {
        $request->validate([
            'curso_id' => 'numeric|integer',
        ]);

        $cursos = Curso::orderBy('nombre')->get();

        if (request('curso_id') >= -1) {
            session(['filtrar_curso_actual' => request('curso_id')]);
        } else if (empty(session('filtrar_curso_actual'))) {
            session(['filtrar_curso_actual' => Auth::user()->curso_actual()?->id]);
        }

        if (session('filtrar_curso_actual') == -1) {
            $qualifications = Qualification::all();
        } else {
            $qualifications = Qualification::where('curso_id', session('filtrar_curso_actual'))->get();
        }

        return view('qualifications.index', compact(['qualifications', 'cursos']));
    }

    public function create()
    {
        $cursos = Curso::orderBy('nombre')->get();

        $curso_actual = Curso::find(setting_usuario('curso_actual'));

        $skills_disponibles = $curso_actual?->skills ?: [];

        return view('qualifications.create', compact(['skills_disponibles', 'cursos', 'curso_actual']));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'curso_id' => 'required',
            'name' => 'required',
        ]);

        $qualification = Qualification::create([
            'curso_id' => $request->input('curso_id'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'template' => $request->has('template'),
        ]);

        if ($request->input('skills_seleccionados')) {
            foreach ($request->input('skills_seleccionados') as $skill) {
                $qualification->skills()->attach($skill, ['percentage' => $request->input('percentage_' . $skill)]);
            }
        }

        return retornar();
    }

    public function show(Qualification $qualification)
    {
        return abort(501);
    }

    public function edit(Qualification $qualification)
    {
        $cursos = Curso::orderBy('nombre')->get();

        $skills_disponibles = $qualification->curso->skills;

        return view('qualifications.edit', compact(['qualification', 'skills_disponibles', 'cursos']));
    }

    public function update(Request $request, Qualification $qualification)
    {
        $this->validate($request, [
            'curso_id' => 'required',
            'name' => 'required',
        ]);

        $qualification->update([
            'curso_id' => $request->input('curso_id'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'template' => $request->has('template'),
        ]);

        $qualification->skills()->detach();

        if ($request->input('skills_seleccionados')) {
            foreach ($request->input('skills_seleccionados') as $skill) {
                $qualification->skills()->attach($skill, ['percentage' => $request->input('percentage_' . $skill)]);
            }
        }

        return retornar();
    }

    public function destroy(Qualification $qualification)
    {
        $qualification->delete();

        return back();
    }
}
