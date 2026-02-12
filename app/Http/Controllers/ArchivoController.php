<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\FiltroUsuario;
use App\Traits\PaginarUltima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArchivoController extends Controller
{
    use PaginarUltima;
    use FiltroUsuario;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $user = $this->filtrar_por_usuario($request, $user);

        $actividades = $this->paginate_ultima($user->actividades_archivadas(), config('ikasgela.pagination_medium'));

        $users = $this->usuarios_curso_actual($user);

        return view('archivo.index', compact(['actividades', 'user', 'users']));
    }

    public function show($id)
    {
        $user = Auth::user();
        $actividad = $user->actividades()->findOrFail($id);

        return view('archivo.show', compact(['actividad', 'user']));
    }

    public function outline(Request $request)
    {
        $user = Auth::user();

        $user = $this->filtrar_por_usuario($request, $user);

        if ($user->baja_ansiedad) {
            abort(404);
        }

        $curso = $user->curso_actual();

        if (!is_null($curso) && ($curso->progreso_visible || Auth::user()->hasRole('tutor'))) {
            $unidades = $curso->unidades()->whereVisible(true)->tag('examen', false)->orderBy('orden')->get();
        } else {
            abort(404);
        }

        $users = $this->usuarios_curso_actual($user);

        return view('archivo.outline', compact(['unidades', 'user', 'curso', 'users']));
    }

    public function usuarios_curso_actual(User $user)
    {
        $curso = $user->curso_actual();

        if (!is_null($curso))
            $users = $curso->users()->rolAlumno()->noBloqueado()->orderBy('surname')->orderBy('name')->get();
        else
            $users = [];

        return $users;
    }

    public function diario(Request $request)
    {
        $user = Auth::user();

        $user = $this->filtrar_por_usuario($request, $user);

        $actividades = $this->paginate_ultima($user->actividades(), config('ikasgela.pagination_medium'));

        $users = $this->usuarios_curso_actual($user);

        return view('archivo.diario', compact(['actividades', 'user', 'users']));
    }
}
