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

        $usuarios = User::organizacionActual()->rolAlumno()->orderBy('name')->get();

        $unidades = Unidad::cursoActual()->orderBy('orden')->get();

        $total_actividades_grupo = 0;
        foreach ($usuarios as $usuario) {
            $total_actividades_grupo += $usuario->actividades_completadas()->count();
        }

        // Resultados por usuario y unidades

        $resultados_usuario_unidades = [];

        foreach ($usuarios as $usuario) {

            foreach ($unidades as $unidad) {
                $resultados_usuario_unidades[$usuario->id][$unidad->id] = new Resultado();

                foreach ($usuario->actividades->where('unidad_id', $unidad->id) as $actividad) {

                    $puntuacion_actividad = $actividad->puntuacion * ($actividad->multiplicador ?: 1);
                    $puntuacion_tarea = $actividad->tarea->puntuacion * ($actividad->multiplicador ?: 1);

                    if ($puntuacion_actividad > 0) {
                        $resultados_usuario_unidades[$usuario->id][$unidad->id]->actividad += $puntuacion_actividad;
                        $resultados_usuario_unidades[$usuario->id][$unidad->id]->tarea += $puntuacion_tarea;
                    }
                }
            }
        }

        return view('tutor.index', compact(['usuarios', 'unidades', 'organization', 'total_actividades_grupo', 'resultados_usuario_unidades']));
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

}
