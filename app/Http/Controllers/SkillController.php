<?php

namespace App\Http\Controllers;

use App\Curso;
use App\Skill;
use App\Traits\FiltroCurso;
use Illuminate\Http\Request;

class SkillController extends Controller
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

        $skills = $this->filtrar_por_curso($request, Skill::class);

        return view('skills.index', compact('skills', 'cursos'));
    }

    public function create()
    {
        $cursos = Curso::orderBy('nombre')->get();

        return view('skills.create', compact(['cursos']));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'curso_id' => 'required',
            'name' => 'required',
        ]);

        Skill::create($request->all());

        return retornar();
    }

    public function show(Skill $skill)
    {
        return abort(501);
    }

    public function edit(Skill $skill)
    {
        $cursos = Curso::orderBy('nombre')->get();

        return view('skills.edit', compact(['skill', 'cursos']));
    }

    public function update(Request $request, Skill $skill)
    {
        $this->validate($request, [
            'curso_id' => 'required',
            'name' => 'required',
        ]);

        $skill->update($request->all());

        return retornar();
    }

    public function destroy(Skill $skill)
    {
        $skill->delete();

        return back();
    }
}
