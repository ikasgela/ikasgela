<?php

namespace App\Http\Controllers;

use App\Jobs\RunJPlag;
use App\Mail\ActividadAsignada;
use App\Models\Actividad;
use App\Models\CacheClear;
use App\Models\Curso;
use App\Models\JPlag;
use App\Models\Organization;
use App\Models\Registro;
use App\Models\Tarea;
use App\Models\Team;
use App\Models\Unidad;
use App\Models\User;
use App\Traits\JPlagRunner;
use App\Traits\PaginarUltima;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use NumberFormatter;
use Zip;

class ProfesorController extends Controller
{
    use PaginarUltima;
    use JPlagRunner;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
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
                session(['tags_usuario' => []]);
            }
        }

        if ($request->has('filtro_actividades_examen')) {
            if (session('profesor_filtro_actividades_examen') == 'E') {
                session(['profesor_filtro_actividades_examen' => '']);
            } else {
                session(['profesor_filtro_actividades_examen' => 'E']);
            }
        }

        if ($request->has('user_id')) {
            if (session('filtrar_user_actual') == $request->input('user_id')) {
                session()->forget('filtrar_user_actual');
            } else {
                session(['filtrar_user_actual' => $request->input('user_id')]);
            }
        }

        $curso_actual = Curso::find(setting_usuario('curso_actual'));

        if ($curso_actual != null) {

            $alumnos = $curso_actual->users()->rolAlumno()->orderBy('surname')->orderBy('name');

            if (!session('profesor_filtro_alumnos_bloqueados') == 'B') {
                $alumnos = $alumnos->noBloqueado();
            }

            if ($request->has('tag_usuario')) {
                session(['profesor_filtro_etiquetas' => 'S']);
                session()->push('tags_usuario', request('tag_usuario'));
            }

            if (session('profesor_filtro_actividades_examen') == 'E') {
                if (session('profesor_filtro_alumnos') == 'R') {
                    $alumnos = $alumnos->whereHas('actividades', function ($query) {
                        $query->where('auto_avance', false)->where('estado', 30);
                    });
                }
            } else {
                if (session('profesor_filtro_alumnos') == 'R') {
                    $alumnos = $alumnos->whereHas('actividades', function ($query) {
                        $query->where('auto_avance', false)->tag('examen', false)->where('estado', 30);
                    });
                } else if (session('profesor_filtro_alumnos') == 'ACT') {
                    $alumnos = $alumnos->whereHas('actividades', function ($query) {
                        $query->where('auto_avance', false)->tag('examen', false)->whereIn('estado', [10, 11, 20, 21]);
                    });
                }
            }

            if (!is_null(session('tags_usuario')))
                $alumnos = $alumnos->tags(session('tags_usuario'));

            $usuarios = match (session('profesor_filtro_alumnos')) {
                'R', 'ACT' => $alumnos->orderBy('last_active')->get(),
                'P' => $alumnos->orderBy('name')->get()->sortBy('num_completadas_base'),
                default => $alumnos->orderBy('name')->get(),
            };
        } else {
            $usuarios = User::cursoActual()->rolAlumno()->orderBy('name')->get();
        }

        $unidades = Unidad::organizacionActual()->cursoActual()->orderBy('orden')->get();

        if ($request->has('unidad_id_disponibles')) {
            session(['profesor_unidad_id_disponibles' => $request->input('unidad_id_disponibles')]);
        }

        if ($request->has('filtro_etiquetas')) {
            if (request('filtro_etiquetas') == 'N') {
                session(['profesor_filtro_actividades_etiquetas' => '']);
                session(['tags_actividades' => []]);
            }
        }

        if ($request->has('tag_actividad')) {
            session(['profesor_filtro_actividades_etiquetas' => 'S']);
            session()->push('tags_actividades', request('tag_actividad'));
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

        $etiquetas = [];
        foreach ($usuarios as $usuario) {
            $etiquetas = array_merge($etiquetas, $usuario->etiquetas());
        }
        $etiquetas = array_unique($etiquetas);
        sort($etiquetas);

        return view('profesor.index', compact(['usuarios', 'unidades', 'disponibles', 'organization',
            'total_actividades_grupo', 'media_grupo', 'media_grupo_formato',
            'etiquetas',
            'curso_actual',
        ]));
    }

    public function tareas(User $user, Request $request)
    {
        $actividades = $this->getActividadesFiltradas($request, $user);

        $actividades = $this->paginate_ultima($actividades, config('ikasgela.pagination_assigned_activities'), 'asignadas');

        $unidades = Unidad::organizacionActual()->cursoActual()->orderBy('orden')->get();

        // Obtener el id del anterior y el siguiente

        $usuarios = $this->getUsuariosFiltrados();

        $user_anterior = null;
        $user_siguiente = null;

        $pos = array_search($user->id, $usuarios);

        if ($pos !== false) {
            if (isset($usuarios[$pos - 1])) {
                $user_anterior = $usuarios[$pos - 1];
            }

            if (isset($usuarios[$pos + 1])) {
                $user_siguiente = $usuarios[$pos + 1];
            }
        } else if (count($usuarios) > 0) {
            $user_siguiente = $usuarios[0];
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

    public function asignarTareaEquipo(Team $team, Request $request)
    {
        $this->validate($request, [
            'seleccionadas' => 'required',
        ]);

        $this->asignarTareasUsuarioEquipo($team);

        return redirect(route('teams.show', ['team' => $team->id]));
    }

    public function revisar(User $user, Tarea $tarea, Request $request)
    {
        $actividades = $this->getActividadesFiltradas($request, $user);

        $actividad = $tarea->actividad;
        $feedbacks_curso = $actividad->unidad->curso->feedbacks()->orderBy('orden')->get();
        $feedbacks_actividad = isset($actividad->original) ? $actividad->original->feedbacks()->orderBy('orden')->get() : [];

        $jplags = JPlag::where('tarea_id', $tarea->id)->orderBy('percent', 'desc')->get();

        $ids_actividades = $actividades->get()->pluck('id')->toArray();
        $pos = array_search($actividad->id, $ids_actividades);

        $actividad_anterior = null;
        if (isset($ids_actividades[$pos - 1])) {
            $id_actividad = $ids_actividades[$pos - 1];
            $tarea_anterior = Tarea::where('user_id', $user->id)->where('actividad_id', $id_actividad)->first();
            $actividad_anterior = $tarea_anterior->id;
        }

        $actividad_siguiente = null;
        if (isset($ids_actividades[$pos + 1])) {
            $id_actividad = $ids_actividades[$pos + 1];
            $tarea_siguiente = Tarea::where('user_id', $user->id)->where('actividad_id', $id_actividad)->first();
            $actividad_siguiente = $tarea_siguiente->id;
        }

        return view('profesor.revisar', compact([
            'user', 'tarea', 'actividad', 'feedbacks_curso', 'feedbacks_actividad', 'jplags',
            'actividad_anterior', 'actividad_siguiente',
        ]));
    }

    public function jplag(Tarea $tarea)
    {
        if ($tarea->actividad->intellij_projects->where('open_with', '=', 'idea')->count() > 0) {
            RunJPlag::dispatch($tarea);
        }

        return back();
    }

    public function jplag_download(Tarea $tarea)
    {
        if ($tarea->actividad->intellij_projects->count() > 0) {

            $directorio = '/' . Str::uuid() . '/';

            try {
                // Crear el directorio temporal
                Storage::disk('temp')->makeDirectory($directorio);
                $ruta = Storage::disk('temp')->path($directorio);

                $this->run_jplag($tarea, $ruta, $directorio);

                // Crear el zip
                $fecha = now()->format('Ymd-His');
                $nombre = Str::slug($tarea->actividad->full_name);

                $ficheros = Storage::disk('temp')->files($directorio . '/__resultados');

                $ficheros_ruta_completa = [];
                foreach ($ficheros as $fichero) {
                    $ficheros_ruta_completa[] = Storage::disk('temp')->path($fichero);
                }

                $ficheros_ruta_completa[] = Storage::disk('temp')->path($directorio . '/__resultados.jplag');

                // Disparar un evento para borrar el directorio después de enviar el zip
                dispatch(function () use ($directorio) {
                    Log::debug('Borrando...', [
                        'directorio' => $directorio,
                    ]);
                    Storage::disk('temp')->deleteDirectory($directorio);
                })->afterResponse();

                return Zip::create("jplag-{$nombre}-{$fecha}.zip", $ficheros_ruta_completa);

            } catch (Exception $e) {
                Log::error('Error al ejecutar JPlag.', [
                    'exception' => $e->getMessage(),
                    'tarea' => $tarea,
                ]);
            }
        }

        return back();
    }

    private function asignarTareasUsuario($user): void
    {
        $asignadas = '';

        foreach (request('seleccionadas') as $actividad_id) {
            $actividad = Actividad::find($actividad_id);

            // Sacar un duplicado de la actividad y poner el campo plantilla a false
            // REF: https://github.com/BKWLD/cloner

            $clon = $actividad->duplicate();
            $clon->duplicar_recursos_consumibles();
            $clon->plantilla_id = $actividad->id;
            $clon->orden = $clon->id;

            if (request()->has('fecha_override_enable'))
                $clon->establecerFechaEntrega(request('fecha_override'));
            else
                $clon->establecerFechaEntrega();

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

        if (!$user->curso_actual()->silence_notifications && request('notificar') && setting_usuario('notificacion_actividad_asignada', $user)) {
            Mail::to($user)->queue(new ActividadAsignada($user->name, $asignadas));
        }
    }

    private function asignarTareasUsuarioEquipo($team): void
    {
        $asignadas = '';

        foreach (request('seleccionadas') as $actividad_id) {
            $actividad = Actividad::findOrFail($actividad_id);

            // Sacar un duplicado de la actividad y poner el campo plantilla a false
            // REF: https://github.com/BKWLD/cloner

            $clon = $actividad->duplicate();
            $clon->duplicar_recursos_consumibles();
            $clon->plantilla_id = $actividad->id;
            $clon->orden = $clon->id;

            if (request()->has('fecha_override_enable'))
                $clon->establecerFechaEntrega(request('fecha_override'));
            else
                $clon->establecerFechaEntrega();

            $clon->addEtiqueta('trabajo en equipo');

            $clon->save();

            $asignadas .= "- " . $clon->unidad->nombre . " - " . $clon->nombre . ".\n";

            // Asociar la tarea al equipo
            $team->actividades()->attach($clon);

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
            if (!$user->curso_actual()->silence_notifications && request('notificar') && setting_usuario('notificacion_actividad_asignada', $user)) {
                Mail::to($user)->queue(new ActividadAsignada($user->name, $asignadas));
            }
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

        if (session('profesor_filtro_actividades_etiquetas')) {
            $disponibles = $disponibles->tags(session('tags_actividades'));
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

    public function getActividadesFiltradas(Request $request, User $user)
    {
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

        $actividades = match (session('profesor_filtro_alumnos')) {
            'R' => $user->actividades_enviadas_noautoavance(),
            'C' => $user->actividades_caducadas(),
            default => $user->actividades(),
        };

        if (!session('profesor_filtro_actividades_examen') == 'E') {
            $actividades = $actividades->tag('examen', false);
        }

        if (session('profesor_unidad_id_asignadas')) {
            $actividades = $actividades->where('unidad_id', session('profesor_unidad_id_asignadas'));
        }

        if ($request->has('filtro_etiquetas')) {
            if (request('filtro_etiquetas') == 'N') {
                session(['profesor_filtro_actividades_etiquetas' => '']);
                session(['tags_actividades' => []]);
            }
        }

        if ($request->has('tag_actividad')) {
            session(['profesor_filtro_actividades_etiquetas' => 'S']);
            session()->push('tags_actividades', request('tag_actividad'));
        }

        if (session('profesor_filtro_actividades_etiquetas')) {
            $actividades = $actividades->tags(session('tags_actividades'));
        }
        return $actividades;
    }

    public function getUsuariosFiltrados()
    {
        $alumnos = User::cursoActual()->rolAlumno()->orderBy('surname')->orderBy('name');

        if (!session('profesor_filtro_alumnos_bloqueados') == 'B') {
            $alumnos = $alumnos->noBloqueado();
        }

        if (session('profesor_filtro_actividades_examen') == 'E') {
            if (session('profesor_filtro_alumnos') == 'R') {
                $alumnos = $alumnos->whereHas('actividades', function ($query) {
                    $query->where('auto_avance', false)->where('estado', 30);
                });
            } else if (session('profesor_filtro_alumnos') == 'C') {
                $alumnos = $alumnos->whereHas('actividades', function ($query) {
                    $query->where('auto_avance', false)
                        ->where('fecha_limite', '<', now())
                        ->whereNotIn('estado', [30, 40, 60, 61, 64]);
                });
            }
        } else {
            if (session('profesor_filtro_alumnos') == 'R') {
                $alumnos = $alumnos->whereHas('actividades', function ($query) {
                    $query->where('auto_avance', false)->tag('examen', false)->where('estado', 30);
                });
            } else if (session('profesor_filtro_alumnos') == 'ACT') {
                $alumnos = $alumnos->whereHas('actividades', function ($query) {
                    $query->where('auto_avance', false)->tag('examen', false)->whereIn('estado', [10, 11, 20, 21]);
                });
            } else if (session('profesor_filtro_alumnos') == 'C') {
                $alumnos = $alumnos->whereHas('actividades', function ($query) {
                    $query->where('auto_avance', false)
                        ->tag('examen', false)
                        ->where('fecha_limite', '<', now())
                        ->whereNotIn('estado', [30, 40, 60, 61, 64]);
                });
            }
        }

        if (!is_null(session('tags_usuario')))
            $alumnos = $alumnos->tags(session('tags_usuario'));

        return $alumnos->pluck('id')->toArray();
    }
}
