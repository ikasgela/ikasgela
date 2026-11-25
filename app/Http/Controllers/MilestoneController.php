<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Milestone;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $curso_actual = Curso::find(setting_usuario('curso_actual'));

        $milestones = $curso_actual?->milestones()->orderBy('date')->get();

        return view('milestones.index', compact(['milestones']));
    }

    public function create()
    {
        $curso_actual = Curso::find(setting_usuario('curso_actual'));

        return view('milestones.create', compact(['curso_actual']));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'curso_id' => 'required',
            'name' => 'required',
            'date' => 'nullable',
            'start_date' => $request->filled('date') ? 'nullable|before_or_equal:date' : 'nullable',
            'num_actividades_obligatorias' => 'required_if:ajuste_proporcional_nota,fijo|nullable|integer|min:1',
            'qualification_id' => 'nullable|integer|exists:qualifications,id',
        ]);

        Milestone::create([
            'curso_id' => $request->input('curso_id'),
            'name' => $request->input('name'),
            'date' => $request->input('date') ?: null,
            'start_date' => $request->input('start_date') ?: null,
            'published' => $request->has('published'),
            'decimals' => $request->input('decimals'),
            'truncate' => $request->has('truncate'),
            'normalizar_nota' => $request->has('normalizar_nota'),
            'ajuste_proporcional_nota' => $request->input('ajuste_proporcional_nota'),
            'etiquetas' => $request->input('etiquetas', 'base'),
            'num_actividades_obligatorias' => $request->input('ajuste_proporcional_nota') === 'fijo' ? $request->input('num_actividades_obligatorias') : null,
            'qualification_id' => $request->input('qualification_id') ?: null,
        ]);

        return retornar();
    }

    public function show(Milestone $milestone)
    {
        abort(404);
    }

    public function edit(Milestone $milestone)
    {
        return view('milestones.edit', compact(['milestone']));
    }

    public function update(Request $request, Milestone $milestone)
    {
        $this->validate($request, [
            'curso_id' => 'required',
            'name' => 'required',
            'date' => 'nullable',
            'start_date' => $request->filled('date') ? 'nullable|before_or_equal:date' : 'nullable',
            'num_actividades_obligatorias' => 'required_if:ajuste_proporcional_nota,fijo|nullable|integer|min:1',
            'qualification_id' => 'nullable|integer|exists:qualifications,id',
        ]);

        $milestone->update([
            'curso_id' => $request->input('curso_id'),
            'name' => $request->input('name'),
            'date' => $request->input('date') ?: null,
            'start_date' => $request->input('start_date') ?: null,
            'published' => $request->has('published'),
            'decimals' => $request->input('decimals'),
            'truncate' => $request->has('truncate'),
            'normalizar_nota' => $request->has('normalizar_nota'),
            'ajuste_proporcional_nota' => $request->input('ajuste_proporcional_nota'),
            'etiquetas' => $request->input('etiquetas', 'base'),
            'num_actividades_obligatorias' => $request->input('ajuste_proporcional_nota') === 'fijo' ? $request->input('num_actividades_obligatorias') : null,
            'qualification_id' => $request->input('qualification_id') ?: null,
        ]);

        return retornar();
    }

    public function destroy(Milestone $milestone)
    {
        $milestone->delete();

        return back();
    }

    public function duplicar(Milestone $milestone)
    {
        $milestone->duplicar(null);

        return redirect(route('milestones.index'));
    }
}
