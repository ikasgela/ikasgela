<?php

namespace App\Http\Controllers;

use App\Curso;
use App\Qualification;
use App\Skill;
use BadMethodCallException;
use Illuminate\Http\Request;

class QualificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $qualifications = Qualification::all();

        return view('qualifications.index', compact('qualifications'));
    }

    public function create()
    {
        return view('qualifications.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        Qualification::create($request->all());

        return redirect(route('qualifications.index'));
    }

    public function show(Qualification $qualification)
    {
        throw new BadMethodCallException(__('Not implemented.'));
    }

    public function edit(Qualification $qualification)
    {
        $skills_seleccionados = $qualification->skills()->orderBy('name')->get();

        $filtro = $qualification->skills()->pluck('skill_id')->unique()->flatten()->toArray();
        $skills_disponibles = Skill::whereNotIn('id', $filtro)->orderBy('name')->get();

        return view('qualifications.edit', compact(['qualification', 'skills_seleccionados', 'skills_disponibles']));
    }

    public function update(Request $request, Qualification $qualification)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $qualification->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'template' => $request->has('template'),
        ]);

        $qualification->skills()->sync($request->input('skills_seleccionados'));

        return redirect(route('qualifications.index'));
    }

    public function destroy(Qualification $qualification)
    {
        $qualification->delete();

        return redirect(route('qualifications.index'));
    }
}
