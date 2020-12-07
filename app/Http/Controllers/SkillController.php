<?php

namespace App\Http\Controllers;

use App\Organization;
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
        $organizations = Organization::orderBy('name')->get();

        return view('skills.create', compact(['organizations']));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'organization_id' => 'required',
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
        $organizations = Organization::orderBy('name')->get();

        return view('skills.edit', compact(['skill', 'organizations']));
    }

    public function update(Request $request, Skill $skill)
    {
        $this->validate($request, [
            'organization_id' => 'required',
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
