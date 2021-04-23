<?php

namespace App\Http\Controllers;

use App\Actividad;
use App\Curso;
use App\Mail\ActividadAsignada;
use App\Models\CacheClear;
use App\Organization;
use App\Registro;
use App\Tarea;
use App\Team;
use App\Traits\PaginarUltima;
use App\Unidad;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use NumberFormatter;

class ProfesorController extends Controller
{
    use PaginarUltima;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->recuento_enviadas();

        $organization = Organization::find(setting_usuario('_organization_id'));

        if ($request->has('filtro_alumnos')) {
            session(['profesor_filtro_alumnos' => $request->input('filtro_alumnos')]);
        }

        if ($request->has('filtro_alumnos_bloqueados')) {
            if (session('profesor_filtro_alumnos_bloqueados') == 'B') {
                session(['profesor_filtro_alumnos_bloqueados' => '']);
            } else {
                session(['profesor_filtro_alumnos_bloqueados' => $request->input('filtro_alumnos_bloqueados')]);
            }
        }

        if ($request->has('filtro_etiquetas')) {
            if (request('filtro_etiquetas') == 'N') {
                session(['profesor_filtro_etiquetas' => '']);
                session(['tags' => []]);
            }
        }

        $curso_actual = Curso::find(setting_usuario('curso_actual'));

        if ($curso_actual != null) {

            $alumnos = $curso_actual->users()->rolAlumno()->orderBy('surname')->orderBy('name');

            if (!session('profesor_filtro_alumnos_bloqueados') == 'B') {
                $alumnos = $alumnos->noBloqueado();
            }

            if ($request->has('tag')) {
                session(['profesor_filtro_etiquetas' => 'S']);
                session()->push('tags', request('tag'));
            }

            if (!is_null(session('tags')))
                $alumnos = $alumnos->tags(session('tags'));

            switch (session('profesor_filtro_alumnos')) {
                case 'R':
                    $usuarios = $alumnos->whereHas('actividades', function ($query) {
                        $query->where('auto_avance', false)->where('estado', 30);
                    })
                        ->orderBy('last_active')->get();
                    break;
                case 'P':
                    $usuarios = $alumnos->orderBy('name')->get()->sortBy('num_completadas_base');
                    break;
                default:
                    $usuarios = $alumnos->orderBy('name')->get();
                    break;
            }
        } else {
            $usuarios = User::cursoActual()->rolAlumno()->orderBy('name')->get();
        }

        $unidades = Unidad::organizacionActual()->cursoActual()->orderBy('codigo')->orderBy('nombre')->get();

        if ($request->has('unidad_id_disponibles')) {
            session(['profesor_unidad_id_disponibles' => $request->input('unidad_id_disponibles')]);
        }

        $disponibles = $this->actividadesDisponibles();

        $media_grupo = 0;
        $total_actividades_grupo = 0;

        if ($curso_actual != null) {

            $usuarios_activos = $curso_actual->users()->rolAlumno()->noBloqueado()->get();

            $total_actividades_grupo = 0;
            foreach ($usuarios_activos as $usuario) {
                $total_actividades_grupo += $usuario->num_completadas('base');
            }

            $media_grupo = isset($usuarios_activos) && $usuarios_activos->count() > 0 ? $total_actividades_grupo / $usuarios_activos->count() : 0;
        }

        // Formateador con 2 decimales y en el idioma del usuario
        $locale = app()->getLocale();
        $formatStyle = NumberFormatter::DECIMAL;
        $formatter = new NumberFormatter($locale, $formatStyle);
        $formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 2);
        $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 2);

        $media_grupo_formato = $formatter->format($media_grupo);

        return view('profesor.index', compact(['usuarios', 'unidades', 'disponibles', 'organization',
            'total_actividades_grupo', 'media_grupo', 'media_grupo_formato']));
    }

    public function tareas(User $user, Request $request)
    {
        $this->recuento_enviadas();

        if ($request->has('filtro_alumnos')) {
            session(['profesor_filtro_alumnos' => $request->input('filtro_alumnos')]);
        }

        if ($request->has('unidad_id_disponibles')) {
            session(['profesor_unidad_id_disponibles' => $request->input('unidad_id_disponibles')]);
        }

        if ($request->has('unidad_id_asignadas')) {
            session(['profesor_unidad_id_asignadas' => $request->input('unidad_id_asignadas')]);
        }

        if ($request->has('filtro_actividades_examen')) {
            if (session('profesor_filtro_actividades_examen') == 'E') {
                session(['profesor_filtro_actividades_examen' => '']);
            } else {
                session(['profesor_filtro_actividades_examen' => 'E']);
            }
        }

        switch (session('profesor_filtro_alumnos')) {
            case 'R':
                $actividades = $user->actividades_enviadas_noautoavance();
                break;
            default:
                $actividades = $user->actividades();
                break;
        }

        if (!session('profesor_filtro_actividades_examen') == 'E') {
            $actividades = $actividades->tag('examen', false);
        }

        if (session('profesor_unidad_id_asignadas')) {
            $actividades = $actividades->where('unidad_id', session('profesor_unidad_id_asignadas'));
        }

        $actividades = $this->paginate_ultima($actividades, config('ikasgela.pagination_assigned_activities'), 'asignadas');

        $unidades = Unidad::organizacionActual()->cursoActual()->orderBy('codigo')->orderBy('nombre')->get();

        // Obtener el id del anterior y el siguiente

        $usuarios = User::cursoActual()->rolAlumno()->noBloqueado()->orderBy('surname')->orderBy('name')->pluck('id')->toArray();

        $pos = array_search($user->id, $usuarios);

        $user_anterior = null;
        if (isset($usuarios[$pos - 1])) {
            $user_anterior = $usuarios[$pos - 1];
        }

        $user_siguiente = null;
        if (isset($usuarios[$pos + 1])) {
            $user_siguiente = $usuarios[$pos + 1];
        }

        $disponibles = $this->actividadesDisponibles();

        return view('profesor.tareas', compact(['actividades', 'disponibles', 'user', 'unidades', 'user_anterior', 'user_siguiente']));
    }

    public function asignarTarea(User $user, Request $request)
    {
        $this->validate($request, [
            'seleccionadas' => 'required',
        ]);

        $this->asignarTareasUsuario($user);

        return redirect(route('profesor.tareas', ['user' => $user->id]));
    }

    public function asignarTareasGrupo(Request $request)
    {
        $this->validate($request, [
            'usuarios_seleccionados' => 'required',
            'seleccionadas' => 'required',
        ]);

        foreach (request('usuarios_seleccionados') as $user_id) {

            $user = User::find($user_id);

            $this->asignarTareasUsuario($user);
        }

        return redirect(route('profesor.index'));
    }

    public function asignarTareasEquipo(Request $request)
    {
        $this->validate($request, [
            'equipos_seleccionados' => 'required',
            'seleccionadas' => 'required',
        ]);

        foreach (request('equipos_seleccionados') as $team_id) {
            $team = Team::findOrFail($team_id);
            $this->asignarTareasUsuarioEquipo($team);
        }

        return redirect(route('teams.index'));
    }

    public function revisar(User $user, Tarea $tarea)
    {
        $actividad = $tarea->actividad;
        $feedbacks_curso = $actividad->unidad->curso->feedbacks()->get();
        $feedbacks_actividad = isset($actividad->original) ? $actividad->original->feedbacks()->get() : [];

        return view('profesor.revisar', compact(['user', 'tarea', 'actividad', 'feedbacks_curso', 'feedbacks_actividad']));
    }

    private function recuento_enviadas(): void
    {
        $tareas = Tarea::cursoActual()->usuarioNoBloqueado()->noAutoAvance()->where('estado', 30)->get();

        $num_enviadas = count($tareas);
        if ($num_enviadas > 0)
            session(['num_enviadas' => $num_enviadas]);
        else
            session()->forget('num_enviadas');
    }

    private function asignarTareasUsuario($user): void
    {
        $asignadas = '';

        foreach (request('seleccionadas') as $actividad_id) {
            $actividad = Actividad::find($actividad_id);

            // Sacar un duplicado de la actividad y poner el campo plantilla a false
            // REF: https://github.com/BKWLD/cloner

            $clon = $actividad->duplicate();
            $clon->plantilla_id = $actividad->id;
            $clon->orden = $clon->id;

            if (!isset($clon->fecha_disponibilidad)) {
                $ahora = now();
                $clon->fecha_disponibilidad = $ahora;
                $plazo = $ahora->addDays($actividad->unidad->curso->plazo_actividad);
                $clon->fecha_entrega = $plazo;
                $clon->fecha_limite = $plazo;
            }

            $clon->save();

            // Dejar pendiente el borrado de caché para cuando llegue la fecha
            CacheClear::create(['fecha' => $clon->fecha_disponibilidad, 'user_id' => $user->id]);
            CacheClear::create(['fecha' => $clon->fecha_entrega, 'user_id' => $user->id]);
            CacheClear::create(['fecha' => $clon->fecha_limite, 'user_id' => $user->id]);

            $asignadas .= "- " . $clon->unidad->nombre . " - " . $clon->nombre . ".\n";
            $user->actividades()->attach($clon);
            $tarea = Tarea::where('user_id', $user->id)->where('actividad_id', $clon->id)->first();

            Registro::create([
                'user_id' => $user->id,
                'tarea_id' => $tarea->id,
                'estado' => 10,
                'timestamp' => now(),
                'curso_id' => Auth::user()->curso_actual()->id,
            ]);
        }

        if (request('notificar') && setting_usuario('notificacion_actividad_asignada', $user))
            Mail::to($user->email)->queue(new ActividadAsignada($user->name, $asignadas));
    }

    private function asignarTareasUsuarioEquipo($team): void
    {
        $asignadas = '';

        foreach (request('seleccionadas') as $actividad_id) {
            $actividad = Actividad::findOrFail($actividad_id);

            // Sacar un duplicado de la actividad y poner el campo plantilla a false
            // REF: https://github.com/BKWLD/cloner

            $clon = $actividad->duplicate();
            $clon->plantilla_id = $actividad->id;
            $clon->orden = $clon->id;

            if (!isset($clon->fecha_disponibilidad)) {
                $ahora = now();
                $clon->fecha_disponibilidad = $ahora;
                $plazo = $ahora->addDays($actividad->unidad->curso->plazo_actividad);
                $clon->fecha_entrega = $plazo;
                $clon->fecha_limite = $plazo;
            }

            $clon->shared = true;

            $clon->save();

            $asignadas .= "- " . $clon->unidad->nombre . " - " . $clon->nombre . ".\n";

            // Asociar la misma copia a todos los miembros del equipo
            foreach ($team->users as $user) {

                // Dejar pendiente el borrado de caché para cuando llegue la fecha
                CacheClear::create(['fecha' => $clon->fecha_disponibilidad, 'user_id' => $user->id]);
                CacheClear::create(['fecha' => $clon->fecha_entrega, 'user_id' => $user->id]);
                CacheClear::create(['fecha' => $clon->fecha_limite, 'user_id' => $user->id]);

                $user->actividades()->attach($clon);
                $tarea = Tarea::where('user_id', $user->id)->where('actividad_id', $clon->id)->first();

                Registro::create([
                    'user_id' => $user->id,
                    'tarea_id' => $tarea->id,
                    'estado' => 10,
                    'timestamp' => now(),
                    'curso_id' => Auth::user()->curso_actual()->id,
                ]);
            }
        }

        foreach ($team->users as $user) {
            if (request('notificar') && setting_usuario('notificacion_actividad_asignada', $user))
                Mail::to($user->email)->queue(new ActividadAsignada($user->name, $asignadas));
        }
    }

    private function actividadesDisponibles()
    {
        $actividades_curso = Actividad::plantilla()->cursoActual()->orderBy('orden');

        if (session('profesor_unidad_id_disponibles')) {
            $disponibles = $actividades_curso->where('unidad_id', session('profesor_unidad_id_disponibles'));
        } else {
            $disponibles = $actividades_curso;
        }

        return $this->paginate_ultima($disponibles, config('ikasgela.pagination_available_activities'), 'disponibles');
    }

    public function editNotaManual(User $user, Curso $curso)
    {
        $nota = $user->cursos()->wherePivot('curso_id', $curso->id)->first()->pivot->nota;

        return view('profesor.nota_manual', compact(['curso', 'user', 'nota']));
    }

    public function updateNotaManual(User $user, Curso $curso, Request $request)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'profesor']))
            abort('403');

        $user->cursos()->sync([$curso->id => ['nota' => request('nota')]], false);
        $user->save();  // Provocar que el observer limpie la caché

        return retornar();
    }
}
