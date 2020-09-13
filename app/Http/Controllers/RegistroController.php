<?php

namespace App\Http\Controllers;

use App\Curso;
use App\Registro;
use App\Traits\PaginarUltima;
use App\User;
use Illuminate\Http\Request;

class RegistroController extends Controller
{
    use PaginarUltima;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:alumno|profesor|admin');
    }

    public function index(Request $request)
    {
        // Lista de usuarios
        $users = User::orderBy('name')->get();

        $user = null;

        if (!empty($request->input('user_id'))) {
            $user_id = $request->input('user_id');
            if ($user_id == -1) {
                session()->forget('filtrar_user_actual');
            } else {
                $user = User::find($user_id);
                session(['filtrar_user_actual' => $user_id]);
            }
        } else if (!empty(session('filtrar_user_actual'))) {
            $user = User::find(session('filtrar_user_actual'));
        }

        if (!is_null($user)) {
            $registros = $this->paginate_ultima(Registro::where('user_id', $user->id), 100);
        } else {
            $registros = $this->paginate_ultima(Registro::query(), 100);
        }

        return view('registros.index', compact(['registros', 'users']));
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
