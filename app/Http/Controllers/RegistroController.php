<?php

namespace App\Http\Controllers;

use App\Registro;
use App\Traits\PaginarUltima;
use Illuminate\Http\Request;

class RegistroController extends Controller
{
    use PaginarUltima;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:alumno|profesor|admin');
    }

    public function index()
    {
        $registros = $this->paginate_ultima(Registro::query(), 100);

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

        return retornar();
    }

    public function destroy(Registro $registro)
    {
        $registro->delete();

        return back();
    }
}
