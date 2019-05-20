<?php

namespace App\Http\Controllers;

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
        $skills_disponibles = Skill::all();

        return view('qualifications.create', compact('skills_disponibles'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $qualification = Qualification::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'template' => $request->has('template'),
        ]);

        if ($request->input('skills_seleccionados')) {
            foreach ($request->input('skills_seleccionados') as $skill) {
                $qualification->skills()->attach($skill, ['percentage' => $request->input('percentage_' . $skill)]);
            }
        }

        return redirect(route('qualifications.index'));
    }

    public function show(Qualification $qualification)
    {
        throw new BadMethodCallException(__('Not implemented.'));
    }

    public function edit(Qualification $qualification)
    {
        $skills_disponibles = Skill::all();

        return view('qualifications.edit', compact(['qualification', 'skills_disponibles']));
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

        $qualification->skills()->detach();

        if ($request->input('skills_seleccionados')) {
            foreach ($request->input('skills_seleccionados') as $skill) {
                $qualification->skills()->attach($skill, ['percentage' => $request->input('percentage_' . $skill)]);
            }
        }

        return redirect(route('qualifications.index'));
    }

    public function destroy(Qualification $qualification)
    {
        $qualification->delete();

        return redirect(route('qualifications.index'));
    }
}
