<?php

namespace App\Http\Controllers;

use App\Models\Rule;
use App\Models\RuleGroup;
use Illuminate\Http\Request;

class RuleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:profesor|admin');
    }

    public function index()
    {
        abort(404);
    }

    public function create(RuleGroup $rule_group)
    {
        return view('rules.create', compact('rule_group'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'propiedad' => 'required|in:puntuacion,intentos',
            'operador' => 'required|in:>,<,>=,<=,==,!=',
            'valor' => 'required',
        ]);

        Rule::create($request->all());

        return retornar();
    }

    public function show(Rule $rule)
    {
        abort(404);
    }

    public function edit(Rule $rule)
    {
        return view('rules.edit', compact('rule'));
    }

    public function update(Request $request, Rule $rule)
    {
        $this->validate($request, [
            'propiedad' => 'required|in:puntuacion,intentos',
            'operador' => 'required|in:>,<,>=,<=,==,!=',
            'valor' => 'required',
        ]);

        $rule->update($request->all());

        return retornar();
    }

    public function destroy(Rule $rule)
    {
        $rule->delete();

        return back();
    }

    public function duplicar(Rule $rule)
    {
        $clon = $rule->duplicate();
        $clon->save();

        return back();
    }
}
