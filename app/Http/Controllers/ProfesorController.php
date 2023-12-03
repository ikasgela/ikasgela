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
                }
            }

            if (!is_null(session('tags_usuario')))
                $alumnos = $alumnos->tags(session('tags_usuario'));

            switch (session('profesor_filtro_alumnos')) {
                case 'R':
                    $usuarios = $alumnos->orderBy('last_active')->get();
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

        $unidades = Unidad::organizacionActual()->cursoActual()->orderBy('orden')->get();

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

        $etiquetas = [];
        foreach ($usuarios as $usuario) {
            $etiquetas = array_merge($etiquetas, $usuario->etiquetas());
        }
        $etiquetas = array_unique($etiquetas);
        sort($etiquetas);

        return view('profesor.index', compact(['usuarios', 'unidades', 'disponibles', 'organization',
            'total_actividades_grupo', 'media_grupo', 'media_grupo_formato',
            'etiquetas']));
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
            case 'C':
                $actividades = $user->actividades_caducadas();
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

        if ($request->has('filtro_etiquetas')) {
            if (request('filtro_etiquetas') == 'N') {
                session(['profesor_filtro_actividades_etiquetas' => '']);
                session(['tags_actividades' => []]);
            }
        }

        if ($request->has('tag')) {
            session(['profesor_filtro_actividades_etiquetas' => 'S']);
            session()->push('tags_actividades', request('tag'));
        }

        if (session('profesor_filtro_actividades_etiquetas')) {
            $actividades = $actividades->tags(session('tags_actividades'));
        }

        $actividades = $this->paginate_ultima($actividades, config('ikasgela.pagination_assigned_activities'), 'asignadas');

        $unidades = Unidad::organizacionActual()->cursoActual()->orderBy('orden')->get();

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
        $feedbacks_curso = $actividad->unidad->curso->feedbacks()->orderBy('orden')->get();
        $feedbacks_actividad = isset($actividad->original) ? $actividad->original->feedbacks()->orderBy('orden')->get() : [];

        $jplags = JPlag::where('tarea_id', $tarea->id)->orderBy('percent', 'desc')->get();

        return view('profesor.revisar', compact(['user', 'tarea', 'actividad', 'feedbacks_curso', 'feedbacks_actividad', 'jplags']));
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
                    array_push($ficheros_ruta_completa, Storage::disk('temp')->path($fichero));
                }

                // Almacenar el directorio para borrarlo al terminar con un evento
                //session(['_delete_me' => $directorio]);

                dispatch(function () use ($directorio) {
                    Log::debug('Borrando...', [
                        'directorio' => $directorio,
                    ]);
                    Storage::disk('temp')->deleteDirectory($directorio);
                })->afterResponse();

                return Zip::create("jplag-{$nombre}-{$fecha}.zip", $ficheros_ruta_completa);

            } catch (\Exception $e) {
                Log::error('Error al ejecutar JPlag.', [
                    'exception' => $e->getMessage(),
                    'tarea' => $tarea,
                ]);
            }
        }

        return back();
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
            $clon->plantilla_id = $actividad->id;
            $clon->orden = $clon->id;

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
