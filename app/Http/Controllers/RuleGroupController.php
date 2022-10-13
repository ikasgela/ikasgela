<?php

namespace App\Http\Controllers;

use App\Models\RuleGroup;
use App\Models\Selector;
use Illuminate\Http\Request;

class RuleGroupController extends Controller
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

    public function create(Selector $selector)
    {
        return view('rule_groups.create', compact('selector'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'operador' => 'required',
            'accion' => 'required',
            'resultado' => 'required',
        ]);

        RuleGroup::create($request->all());

        return retornar();
    }

    public function show(RuleGroup $rule_group)
    {
        abort(404);
    }

    public function edit(RuleGroup $rule_group)
    {
        $selector = $rule_group->selector;

        $rules = $rule_group->items;

        return view('rule_groups.edit', compact(['rule_group', 'selector', 'rules']));
    }

    public function update(Request $request, RuleGroup $rule_group)
    {
        $this->validate($request, [
            'operador' => 'required',
            'accion' => 'required',
            'resultado' => 'required',
        ]);

        $rule_group->update($request->all());

        return retornar();
    }

    public function destroy(RuleGroup $rule_group)
    {
        $rule_group->delete();

        return back();
    }
}
