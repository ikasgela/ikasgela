<?php

namespace App\Http\Controllers;

use App\Actividad;
use App\Organization;
use App\Tarea;
use App\Unidad;
use App\User;
use Illuminate\Http\Request;

class TutorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        memorizar_ruta();

        $this->recuento_enviadas();

        $organization = Organization::find(setting_usuario('_organization_id'));

        if ($request->has('filtro_alumnos')) {
            session(['profesor_filtro_alumnos' => $request->input('filtro_alumnos')]);
        }

        switch (session('profesor_filtro_alumnos')) {
            case 'R':
                $usuarios = User::organizacionActual()->rolAlumno()
                    ->whereHas('actividades', function ($query) {
                        $query->where('auto_avance', false)->where('estado', 30);
                    })
                    ->orderBy('last_active')->get();
                break;
            case 'P':
                $usuarios = User::organizacionActual()->rolAlumno()->orderBy('name')->get()->sortBy('actividades_completadas');
                break;
            default:
                $usuarios = User::organizacionActual()->rolAlumno()->orderBy('name')->get();
                break;
        }

        $unidades = Unidad::organizacionActual()->cursoActual()->orderBy('codigo')->orderBy('nombre')->get();

        if ($request->has('unidad_id')) {
            session(['profesor_unidad_actual' => $request->input('unidad_id')]);
        }

        $disponibles = $this->actividadesDisponibles();

        $total_actividades_grupo = 0;
        foreach ($usuarios as $usuario) {
            $total_actividades_grupo += $usuario->actividades_completadas()->count();
        }

        return view('tutor.index', compact(['usuarios', 'unidades', 'disponibles', 'organization', 'total_actividades_grupo']));
    }

    private function recuento_enviadas(): void
    {
        $tareas = Tarea::cursoActual()->noAutoAvance()->where('estado', 30)->get();

        $num_enviadas = count($tareas);
        if ($num_enviadas > 0)
            session(['num_enviadas' => $num_enviadas]);
        else
            session()->forget('num_enviadas');
    }

    private function actividadesDisponibles()
    {
        $actividades_curso = Actividad::plantilla()->cursoActual()->orderBy('orden');

        if (session('profesor_unidad_actual')) {
            $disponibles = $actividades_curso->where('unidad_id', session('profesor_unidad_actual'));
        } else {
            $disponibles = $actividades_curso;
        }

        return $disponibles->paginate(25);
    }

}
