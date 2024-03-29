<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Qualification;
use App\Traits\FiltroCurso;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QualificationController extends Controller
{
    use FiltroCurso;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index(Request $request)
    {
        $cursos = Curso::orderBy('nombre')->get();

        $qualifications = $this->filtrar_por_curso($request, Qualification::class)->get();

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
                $qualification->skills()->attach($skill, [
                    'percentage' => $request->input('percentage_' . $skill),
                    'orden' => Str::orderedUuid(),
                ]);
            }
        }

        return retornar();
    }

    public function show(Qualification $qualification)
    {
        abort(404);
    }

    public function edit(Qualification $qualification)
    {
        $cursos = Curso::orderBy('nombre')->get();

        $skills_asignados = $qualification->skills->sortBy('pivot.orden');

        $skills_disponibles = $qualification->curso->skills->diff($skills_asignados);

        $ids = $skills_asignados->pluck('pivot.orden')->toArray();

        return view('qualifications.edit', compact(['qualification', 'skills_disponibles', 'skills_asignados', 'cursos', 'ids']));
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
                $qualification->skills()->attach($skill, [
                    'percentage' => $request->input('percentage_' . $skill),
                    'orden' => Str::orderedUuid(),
                ]);
            }
        }

        return retornar();
    }

    public function destroy(Qualification $qualification)
    {
        $qualification->delete();

        return back();
    }

    public function reordenar_skills(Request $request, Qualification $qualification)
    {
        $recursos = $qualification->skills()->get()->keyBy('pivot.orden');

        $a1 = $recursos->get(request('a1'));
        $a2 = $recursos->get(request('a2'));

        $temp = $a1->pivot->orden;
        $a1->pivot->orden = $a2->pivot->orden;
        $a2->pivot->orden = $temp;

        $a1->pivot->save();
        $a2->pivot->save();

        $qualification->save();

        return back();
    }
}
