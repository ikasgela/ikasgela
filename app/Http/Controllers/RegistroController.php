<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Registro;
use App\Traits\PaginarUltima;
use App\Models\User;
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
        $curso = Curso::find(setting_usuario('curso_actual'));
        $users = $curso?->users()->orderBy('name')->get() ?? [];

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
            $registros = $this->paginate_ultima(Registro::where('curso_id', $curso?->id)->where('user_id', $user->id), 100);
        } else {
            $registros = $this->paginate_ultima(Registro::where('curso_id', $curso?->id), 100);
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

        $user = User::find(request('user_id'));
        $curso = $user->curso_actual();

        Registro::create([
            'user_id' => request('user_id'),
            'tarea_id' => request('tarea_id'),
            'estado' => request('estado'),
            'detalles' => request('detalles'),
            'curso_id' => !is_null($user) && !is_null($curso) ? $curso->id : null,
        ]);

        return retornar();
    }

    public function destroy(Registro $registro)
    {
        $registro->delete();

        return back();
    }
}
