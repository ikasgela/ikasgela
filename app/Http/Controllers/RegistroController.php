<?php

namespace App\Http\Controllers;

use App\Registro;
use Illuminate\Http\Request;

class RegistroController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:alumno|profesor|admin');
    }

    public function index()
    {
        $registros = Registro::paginate(100);

        return view('registros.index', compact('registros'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required',
            'tarea_id' => 'required',
            'estado' => 'required',
        ]);

        Registro::create($request->all());

        return redirect(route('registros.index'));
    }

    public function destroy(Registro $registro)
    {
        $registro->delete();

        return redirect(route('registros.index'));
    }
}
