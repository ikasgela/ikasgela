<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
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
        return view('rule_groups.create', array_merge(
            compact('selector'),
            ['actividades' => $this->actividades()]
        ));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'operador' => 'required|in:and,or',
            'accion' => 'required|in:siguiente',
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

        $actividades = $this->actividades();

        // El resultado actual se conserva aunque no sea una plantilla del curso actual
        $resultado = Actividad::find($rule_group->resultado);

        if (!is_null($resultado) && !$actividades->contains('id', $resultado->id)) {
            $actividades->push($resultado);
        }

        return view('rule_groups.edit', array_merge(
            compact(['rule_group', 'selector']),
            ['actividades' => $actividades]
        ));
    }

    public function update(Request $request, RuleGroup $rule_group)
    {
        $this->validate($request, [
            'operador' => 'required|in:and,or',
            'accion' => 'required|in:siguiente',
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

    public function duplicar(RuleGroup $rule_group)
    {
        $clon = $rule_group->duplicate();
        $clon->save();

        return back();
    }

    /**
     * Plantillas del curso actual, que son las que se pueden mostrar como resultado.
     */
    private function actividades()
    {
        return Actividad::cursoActual()->plantilla()->orderBy('orden')->orderBy('id')->get();
    }
}
