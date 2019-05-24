<?php

namespace App\Http\Controllers;

use App\Cuestionario;
use BadMethodCallException;
use Illuminate\Http\Request;

class CuestionarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:profesor');
    }

    public function index()
    {
        $cuestionarios = Cuestionario::all();

        return view('cuestionarios.index', compact('cuestionarios'));
    }

    public function create()
    {
        return view('cuestionarios.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'titulo' => 'required',
        ]);

        Cuestionario::create([
            'titulo' => $request->input('titulo'),
            'descripcion' => $request->input('descripcion'),
            'plantilla' => $request->has('plantilla'),
        ]);

        return redirect(route('cuestionarios.index'));
    }

    public function show(Cuestionario $cuestionario)
    {
        throw new BadMethodCallException(__('Not implemented.'));
    }

    public function edit(Cuestionario $cuestionario)
    {
        return view('cuestionarios.edit', compact('cuestionario'));
    }

    public function update(Request $request, Cuestionario $cuestionario)
    {
        $this->validate($request, [
            'titulo' => 'required',
        ]);

        $cuestionario->update([
            'titulo' => $request->input('titulo'),
            'descripcion' => $request->input('descripcion'),
            'plantilla' => $request->has('plantilla'),
        ]);

        return redirect(route('cuestionarios.index'));
    }

    public function destroy(Cuestionario $cuestionario)
    {
        $cuestionario->delete();

        return redirect(route('cuestionarios.index'));
    }
}
