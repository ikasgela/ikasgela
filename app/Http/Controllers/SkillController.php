<?php

namespace App\Http\Controllers;

use App\Skill;
use BadMethodCallException;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $skills = Skill::all();

        return view('skills.index', compact('skills'));
    }

    public function create()
    {
        return view('skills.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        Skill::create($request->all());

        return redirect(route('skills.index'));
    }

    public function show(Skill $skill)
    {
        throw new BadMethodCallException(__('Not implemented.'));
    }

    public function edit(Skill $skill)
    {
        return view('skills.edit', compact('skill'));
    }

    public function update(Request $request, Skill $skill)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $skill->update($request->all());

        return redirect(route('skills.index'));
    }

    public function destroy(Skill $skill)
    {
        $skill->delete();

        return redirect(route('skills.index'));
    }
}
