<?php

namespace App\Models;

use App\Observers\SharedKeys;
use App\Traits\Etiquetas;
use Cmgmyr\Messenger\Traits\Messagable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Lab404\Impersonate\Models\Impersonate;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable implements MustVerifyEmail, HasLocalePreference
{
    use HasFactory;
    use Notifiable;
    use LogsActivity;
    use Messagable;
    use Etiquetas;
    use Impersonate;
    use SharedKeys;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'surname', 'email', 'password', 'username', 'tutorial', 'last_active',
        'blocked_date', 'max_simultaneas', 'tags', 'baja_ansiedad', 'identifier',
        'gravatar_email',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public $appends = ['last_active_time', 'num_completadas_base'];

    public function getFullNameAttribute()
    {
        return $this->name . ' ' . $this->surname;
    }

    public function avatar_url($width = 64)
    {
        $email = $this->email;

        if (isset($this->gravatar_email) && $this->gravatar_email !== '') {
            $email = $this->gravatar_email;
        }

        $hash = md5(strtolower(trim($email)));
        return "https://www.gravatar.com/avatar/$hash?s=$width&d=identicon";
    }

    public static function generar_username($email)
    {
        if (strlen($email) > 40) {
            $usuario_dominio = array_map('trim', explode('@', $email));

            $dominio = $usuario_dominio[1];
            $longitud_dominio = strlen($dominio);
            $usuario = $usuario_dominio[0];

            $email = substr($usuario, 0, 39 - $longitud_dominio) . '.' . $dominio;
        }

        return strtolower(str_replace('@', '.', $email));
    }

    public function actividades()
    {
        // Modificar tambien los campos en \App\Models\Tarea::$fillable
        return $this->belongsToMany('App\Models\Actividad', 'tareas')
            ->cursoActual()
            ->using('App\Models\Tarea')
            ->as('tarea')
            ->withPivot([
                'id',
                'estado',
                'feedback',
                'puntuacion',
                'intentos'
            ]);
    }

    public function roles()
    {
        return $this
            ->belongsToMany('App\Models\Role')
            ->withTimestamps();
    }

    public function authorizeRoles($roles)
    {
        if ($this->hasAnyRole($roles)) {
            return true;
        }
        abort(401, 'Esta acción no está autorizada.');
    }

    public function hasAnyRole($roles)
    {
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }
        } else {
            if ($this->hasRole($roles)) {
                return true;
            }
        }
        return false;
    }

    public function hasRole($role)
    {
        $key = 'roles_' . $this->id;

        $cached_roles = Cache::remember($key, config('ikasgela.eloquent_cache_time'), function () {
            return $this->roles()->get();
        });

        return $cached_roles->contains('name', $role);
    }

    public function actividades_nuevas()
    {
        return $this->actividades()
            ->wherePivot('estado', 10);
    }

    public function num_actividades_nuevas()
    {
        $key = 'num_actividades_nuevas_' . $this->id;

        return Cache::remember($key, config('ikasgela.eloquent_cache_time'), function () {
            return $this->actividades_nuevas()->count();
        });
    }

    public function actividades_ocultas()
    {
        return $this->actividades()
            ->wherePivot('estado', 11);
    }

    public function num_actividades_ocultas()
    {
        $key = 'num_actividades_ocultas_' . $this->id;

        return Cache::remember($key, config('ikasgela.eloquent_cache_time'), function () {
            return $this->actividades_ocultas()->count();
        });
    }

    public function actividades_aceptadas()
    {
        return $this->actividades()
            ->wherePivotIn('estado', [20, 21]);
    }

    public function num_actividades_aceptadas()
    {
        $key = 'num_actividades_aceptadas_' . $this->id;

        return Cache::remember($key, config('ikasgela.eloquent_cache_time'), function () {
            return $this->actividades_aceptadas()->count();
        });
    }

    public function actividades_caducadas()
    {
        return $this->actividades()
            ->caducada()
            ->where('auto_avance', false)
            ->wherePivotNotIn('estado', [30, 40, 60, 61, 64]);
    }

    public function num_actividades_caducadas()
    {
        $key = 'num_actividades_caducadas_' . $this->id;

        return Cache::remember($key, config('ikasgela.eloquent_cache_time'), function () {
            return $this->actividades_caducadas()->count();
        });
    }

    public function actividades_en_curso()
    {
        return $this->actividades()
            ->where(function ($query) {
                $query
                    ->estados([10, 20, 21, 40, 41, 42]);
            });
    }

    public function num_actividades_en_curso()
    {
        $key = 'num_actividades_en_curso_' . $this->id;

        return Cache::remember($key, config('ikasgela.eloquent_cache_time'), function () {
            return $this->actividades_en_curso()->count();
        });
    }

    public function actividades_en_curso_autoavance()
    {
        return $this->actividades_en_curso()
            ->orWhere(function ($query) {
                $query
                    ->estados([30])
                    ->autoAvance();
            });
    }

    public function num_actividades_en_curso_autoavance()
    {
        $key = 'num_actividades_en_curso_autoavance_' . $this->id;

        return Cache::remember($key, config('ikasgela.eloquent_cache_time'), function () {
            return $this->actividades_en_curso_autoavance()->count();
        });
    }

    public function actividades_enviadas()
    {
        return $this->actividades()
            ->wherePivotIn('estado', [30]);
    }

    public function actividades_enviadas_noautoavance()
    {
        return $this->actividades()
            ->wherePivotIn('estado', [30])
            ->where('auto_avance', false);
    }

    public function num_actividades_enviadas_noautoavance()
    {
        $key = 'num_actividades_enviadas_noautoavance_' . $this->id;

        return Cache::remember($key, config('ikasgela.eloquent_cache_time'), function () {
            return $this->actividades_enviadas_noautoavance()->count();
        });
    }

    public function actividades_enviadas_noautoavance_noexamen()
    {
        return $this->actividades()
            ->wherePivotIn('estado', [30])
            ->where('auto_avance', false)
            ->tag('examen', false);
    }

    public function num_actividades_enviadas_noautoavance_noexamen()
    {
        $key = 'num_actividades_enviadas_noautoavance_noexamen_' . $this->id;

        return Cache::remember($key, config('ikasgela.eloquent_cache_time'), function () {
            return $this->actividades_enviadas_noautoavance_noexamen()->count();
        });
    }

    public function actividades_revisadas()
    {
        return $this->actividades()
            ->where('auto_avance', false)
            ->wherePivotIn('estado', [40, 41]);
    }

    public function num_actividades_revisadas()
    {
        $key = 'num_actividades_revisadas_' . $this->id;

        return Cache::remember($key, config('ikasgela.eloquent_cache_time'), function () {
            return $this->actividades_revisadas()->count();
        });
    }

    public function actividades_archivadas()
    {
        return $this->actividades()
            ->wherePivotIn('estado', [60, 64]);
    }

    public function num_actividades_archivadas()
    {
        $key = 'num_actividades_archivadas_' . $this->id;

        return Cache::remember($key, config('ikasgela.eloquent_cache_time'), function () {
            return $this->actividades_archivadas()->count();
        });
    }

    public function actividades_completadas(Milestone $milestone = null)
    {
        $completadas = $this->actividades()
            ->wherePivotIn('estado', [40, 60, 64]);

        return $milestone == null ? $completadas : $completadas->whereBetween('actividades.fecha_finalizacion', [$milestone?->curso->fecha_inicio, $milestone?->date]);
    }

    public function num_actividades_completadas(Milestone $milestone = null)
    {
        $key = 'num_actividades_completadas_' . $this->id . $milestone->cache_key;

        return Cache::remember($key, config('ikasgela.eloquent_cache_time'), function () use ($milestone) {
            return $this->actividades_completadas($milestone)->count();
        });
    }

    public function actividades_sin_completar()
    {
        return $this->actividades()
            ->wherePivotNotIn('estado', [40, 60, 61, 64])
            ->tag('base');
    }

    public function num_actividades_sin_completar()
    {
        $key = 'num_actividades_sin_completar_' . $this->id;

        return Cache::remember($key, config('ikasgela.eloquent_cache_time'), function () {
            return $this->actividades_sin_completar()->count();
        });
    }

    public function actividades_asignadas()
    {
        return $this->actividades()
            ->enPlazo()
            ->wherePivotIn('estado', [10, 20, 21, 30, 40, 41, 42]);
    }

    public function actividades_examen()
    {
        return $this->actividades()
            ->tag('examen');
    }

    public function teams()
    {
        return $this
            ->belongsToMany(Team::class)
            ->withTimestamps();
    }

    public function cursos()
    {
        return $this
            ->belongsToMany(Curso::class)
            ->withPivot('nota')
            ->withTimestamps();
    }

    public function registros()
    {
        return $this->hasMany(Registro::class);
    }

    public function organizations()
    {
        return $this
            ->belongsToMany(Organization::class)
            ->withTimestamps();
    }

    public function isVerified()
    {
        return $this->email_verified_at != null;
    }

    public function isBlocked()
    {
        return $this->blocked_date != null;
    }

    #[Scope]
    protected function organizacionActual($query)
    {
        return $query->whereHas('organizations', function ($query) {
            $query->where('organizations.id', setting_usuario('_organization_id'));
        });
    }

    #[Scope]
    protected function cursoActual($query)
    {
        return $query->whereHas('cursos', function ($query) {
            $query->where('cursos.id', setting_usuario('curso_actual'));
        });
    }

    #[Scope]
    protected function rolAdmin($query)
    {
        return $query->whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        });
    }

    #[Scope]
    protected function rolAlumno($query)
    {
        return $query->whereHas('roles', function ($query) {
            $query->where('name', 'alumno');
        });
    }

    #[Scope]
    protected function rolProfesor($query)
    {
        return $query->whereHas('roles', function ($query) {
            $query->where('name', 'profesor');
        });
    }

    #[Scope]
    protected function noBloqueado($query)
    {
        return $query->where('blocked_date', null);
    }

    public function files()
    {
        return $this->morphMany('App\Models\File', 'uploadable');
    }

    public function getLastActiveTimeAttribute()
    {
        return $this->last_active ? $this->last_active->diffForHumans() : __('Never');
    }

    public function curso_actual()
    {
        return Curso::find(setting_usuario('curso_actual'));
    }

    public function organizacion_actual()
    {
        return Organization::find(setting_usuario('_organization_id'));
    }

    public function num_archivadas($etiqueta, $unidad)
    {
        $total = 0;

        foreach ($this->actividades_archivadas()->where('unidad_id', $unidad)->get() as $actividad) {
            if ($actividad->hasEtiqueta($etiqueta))
                $total += 1;
        }

        return $total;
    }

    public function num_completadas($etiqueta, $unidad = null, Milestone $milestone = null)
    {
        $total = 0;

        $query = $this->actividades_completadas($milestone);

        if (!is_null($unidad))
            $query = $query->where('unidad_id', $unidad);

        foreach ($query->get() as $actividad) {
            if ($actividad->hasEtiqueta($etiqueta))
                $total += 1;
        }

        return $total;
    }

    public function getNumCompletadasBaseAttribute()
    {
        return $this->num_completadas('base');
    }

    public function canImpersonate()
    {
        return $this->hasRole('admin');
    }

    public function canBeImpersonated()
    {
        return !$this->hasRole('admin') && !$this->isBlocked() && $this->hasVerifiedEmail();
    }

    public function newThreadsCount($curso = null)
    {
        if (is_null($curso))
            return Hilo::forUserWithNewMessages($this->id)->cursoActual()->count();
        else
            return Hilo::forUserWithNewMessages($this->id)->where('curso_id', $curso->id)->count();
    }

    public function num_actividades_asignadas_total()
    {
        $key = 'num_actividades_asignadas_total_' . $this->id;

        return Cache::remember($key, config('ikasgela.eloquent_cache_time'), function () {
            return $this->actividades_en_curso_autoavance()->enPlazoOrCorregida()->tag('extra', false)->count() ?: 0;
        });
    }

    public function siguiente_actividad()
    {
        $key = 'siguiente_actividad_' . $this->id;

        return Cache::remember($key, config('ikasgela.eloquent_cache_time'), function () {
            return $this->actividades_asignadas()->orderBy('id', 'desc')->first();
        });
    }

    public function actividades_en_curso_examen()
    {
        return $this->actividades_en_curso_autoavance()->enPlazoOrCorregida()->tag('examen');
    }

    public function num_actividades_en_curso_examen()
    {
        $key = 'num_actividades_en_curso_examen_' . $this->id;

        return Cache::remember($key, config('ikasgela.eloquent_cache_time'), function () {
            return $this->actividades_en_curso_examen()->count();
        });
    }

    public function actividades_en_curso_seb()
    {
        return $this->actividades_en_curso_autoavance()->enPlazoOrCorregida()->tag('seb');
    }

    public function num_actividades_en_curso_seb()
    {
        $key = 'num_actividades_en_curso_seb_' . $this->id;

        return Cache::remember($key, config('ikasgela.eloquent_cache_time'), function () {
            return $this->actividades_en_curso_seb()->count();
        });
    }

    public function actividades_enviadas_seb()
    {
        return $this->actividades_enviadas()->enPlazoOrCorregida()->tag('seb');
    }

    public function num_actividades_enviadas_seb()
    {
        $key = 'num_actividades_enviadas_seb_' . $this->id;

        return Cache::remember($key, config('ikasgela.eloquent_cache_time'), function () {
            return $this->actividades_enviadas_seb()->count();
        });
    }

    public function actividades_en_curso_no_extra_examen()
    {
        return $this->actividades_en_curso_autoavance()->tag('extra', false)->tag('examen', false);
    }

    public function num_actividades_en_curso_no_extra_examen()
    {
        $key = 'num_actividades_en_curso_no_extra_examen_' . $this->id;

        return Cache::remember($key, config('ikasgela.eloquent_cache_time'), function () {
            return $this->actividades_en_curso_no_extra_examen()->count();
        });
    }

    public function actividades_en_curso_extra()
    {
        return $this->actividades_en_curso_autoavance()->tag('extra');
    }

    public function num_actividades_en_curso_extra()
    {
        $key = 'num_actividades_en_curso_extra_' . $this->id;

        return Cache::remember($key, config('ikasgela.eloquent_cache_time'), function () {
            return $this->actividades_en_curso_extra()->count();
        });
    }

    public function actividades_en_curso_enviadas()
    {
        return $this->actividades_enviadas_noautoavance();
    }

    public function num_actividades_en_curso_enviadas()
    {
        $key = 'num_actividades_en_curso_enviadas_' . $this->id;

        return Cache::remember($key, config('ikasgela.eloquent_cache_time'), function () {
            return $this->actividades_en_curso_enviadas()->count();
        });
    }

    public function calcular_calificaciones($media_actividades_grupo, Milestone $milestone = null): ResultadoCalificaciones
    {
        $key = 'calificaciones_' . $this->id . $milestone?->cache_key;

        return Cache::remember($key, config('ikasgela.eloquent_cache_time'), function () use ($media_actividades_grupo, $milestone) {

            $user = $this;

            $curso = $this->curso_actual();

            $r = new ResultadoCalificaciones();

            // Resultados por competencias

            $r->skills_curso = [];
            $r->resultados = [];

            $r->hayExamenes = false;

            if (!is_null($curso) && !is_null($curso->qualification)) {
                $r->skills_curso = $curso->qualification->skills->sortBy('pivot.orden');

                foreach ($r->skills_curso as $skill) {
                    $r->resultados[$skill->id] = new Resultado();
                    $r->resultados[$skill->id]->porcentaje = $skill->pivot->percentage;

                    if ($skill->peso_examen > 0)
                        $r->hayExamenes = true;
                }

                foreach ($user->actividades_completadas($milestone)->get() as $actividad) {

                    // Total de puntos de la actividad
                    $puntuacion_actividad = $actividad->puntuacion * ($actividad->multiplicador ?: 1);

                    // Puntos obtenidos
                    $puntuacion_tarea = $actividad->tarea->puntuacion * ($actividad->multiplicador ?: 1);

                    if ($puntuacion_actividad > 0) {

                        // Obtener las competencias: Curso->Unidad->Actividad
                        if (!is_null($actividad->qualification_id)) {
                            $skills = $actividad->qualification->skills;
                        } else if (!is_null($actividad->unidad->qualification_id)) {
                            $skills = $actividad->unidad->qualification->skills;
                        } else {
                            $skills = $r->skills_curso;
                        }

                        foreach ($skills as $skill) {

                            // Aportación de la competencia a la cualificación
                            $porcentaje = $skill->pivot->percentage;

                            // Peso relativo de las actividades de examen
                            $peso_examen = $skill->peso_examen;
                            $peso_tarea = 100 - $skill->peso_examen;

                            $r->resultados[$skill->id]->peso_examen = $skill->peso_examen;

                            if ($actividad->hasEtiqueta('base')) {
                                $r->resultados[$skill->id]->puntos_tarea += $puntuacion_tarea * ($porcentaje / 100);
                                $r->resultados[$skill->id]->puntos_totales_tarea += $puntuacion_actividad * ($porcentaje / 100);
                                $r->resultados[$skill->id]->num_tareas += 1;
                            } else if ($actividad->hasEtiqueta('examen')) {
                                $r->resultados[$skill->id]->puntos_examen += $puntuacion_tarea * ($porcentaje / 100);
                                $r->resultados[$skill->id]->puntos_totales_examen += $puntuacion_actividad * ($porcentaje / 100);
                                $r->resultados[$skill->id]->num_examenes += 1;
                            } else if ($actividad->hasEtiqueta('extra') || $actividad->hasEtiqueta('repaso')) {
                                $r->resultados[$skill->id]->puntos_tarea += $puntuacion_tarea * ($porcentaje / 100);
                                $r->resultados[$skill->id]->num_tareas += 1;
                            }

                            $r->resultados[$skill->id]->tarea += $puntuacion_tarea * ($porcentaje / 100);
                            $r->resultados[$skill->id]->actividad += $puntuacion_actividad * ($porcentaje / 100);
                        }
                    }
                }
            }

            // Aplicar el criterio del mínimo de competencias
            $r->competencias_50_porciento = true;
            $r->minimo_competencias = $curso?->minimo_competencias;
            foreach ($r->resultados as $resultado) {
                if ($resultado->porcentaje_competencia() < $r->minimo_competencias)
                    $r->competencias_50_porciento = false;
            }

            // Unidades
            $unidades = $curso?->unidades()->whereVisible(true)->orderBy('orden')->get() ?? new Collection();

            // Actividades obligatorias

            $minimo_entregadas = $curso?->minimo_entregadas;

            $r->actividades_obligatorias_superadas = true;
            $r->num_actividades_obligatorias = 0;
            foreach ($unidades as $unidad) {
                if ($unidad->num_actividades('base') > 0) {
                    $r->num_actividades_obligatorias += $unidad->num_actividades('base');

                    if ($user->num_completadas('base', $unidad->id, $milestone) < $unidad->num_actividades('base') * $minimo_entregadas / 100) {
                        $r->actividades_obligatorias_superadas = false;
                    }
                }
            }

            // Nota final

            $nota = 0;
            $porcentaje_total = 0;
            foreach ($r->resultados as $resultado) {
                if ($resultado->actividad > 0) {
                    $nota += ($resultado->porcentaje_competencia() / 100) * ($resultado->porcentaje / 100);
                    $porcentaje_total += $resultado->porcentaje;
                }
            }

            // Nota sobre 10
            $nota = $nota * 10;

            // Ajustar la nota si el porcentaje de las competencias suma más del 100%
            if ($porcentaje_total == 0)
                $porcentaje_total = 100;

            $nota = ($nota / $porcentaje_total) * 100;

            // Ajustar la nota en función de las completadas 100% completadas -> 100% de nota
            // Si estamos en una evaluación parcial, ajustar en función de la media de actividades del grupo
            $r->numero_actividades_completadas = $user->num_completadas('base', null, $milestone);
            if ($r->num_actividades_obligatorias > 0) {
                $ajuste_proporcional_nota = $milestone?->ajuste_proporcional_nota ?: $curso?->ajuste_proporcional_nota;
                switch ($ajuste_proporcional_nota) {
                    case 'media':
                    case 'mediana':
                        $nota = $media_actividades_grupo > 0 ? $nota * ($r->numero_actividades_completadas / $media_actividades_grupo) : -1;
                        break;
                    default:
                        $nota = $nota * ($r->numero_actividades_completadas / $r->num_actividades_obligatorias);
                }
            }

            // Resultados por unidades

            $r->resultados_unidades = [];

            foreach ($unidades as $unidad) {
                $r->resultados_unidades[$unidad->id] = new Resultado();

                foreach ($user->actividades->where('unidad_id', $unidad->id) as $actividad) {

                    $puntuacion_actividad = $actividad->puntuacion * ($actividad->multiplicador ?: 1);
                    $puntuacion_tarea = $actividad->tarea->puntuacion * ($actividad->multiplicador ?: 1);
                    $completada = in_array($actividad->tarea->estado, [40, 60, 64]);

                    if ($puntuacion_actividad > 0 && $completada) {
                        $r->resultados_unidades[$unidad->id]->actividad += $puntuacion_actividad;
                        $r->resultados_unidades[$unidad->id]->tarea += $puntuacion_tarea;
                    }
                }
            }

            // Pruebas de evaluación
            $r->minimo_examenes = $curso?->minimo_examenes;
            $r->pruebas_evaluacion = true;
            $r->num_pruebas_evaluacion = 0;

            foreach ($unidades as $unidad) {
                if ($unidad->hasEtiqueta('examen')
                    && $user->num_completadas('examen', $unidad->id, $milestone) > 0
                    && $r->resultados_unidades[$unidad->id]->actividad > 0) {

                    $r->num_pruebas_evaluacion += 1;
                    $nota_examen = $r->resultados_unidades[$unidad->id]->tarea / $r->resultados_unidades[$unidad->id]->actividad;
                    $minimo_examenes_superado = $nota_examen >= $r->minimo_examenes / 100;

                    if (!$minimo_examenes_superado) {
                        $r->pruebas_evaluacion = false;
                    }
                }
            }

            // Evaluación continua
            $r->evaluacion_continua_superada = ($r->actividades_obligatorias_superadas || $r->num_actividades_obligatorias == 0 || $curso->minimo_entregadas == 0)
                && (!$curso?->examenes_obligatorios || $r->pruebas_evaluacion || $r->num_pruebas_evaluacion == 0)
                && $r->competencias_50_porciento && $nota >= 5;

            // Exámenes finales
            $r->minimo_examenes_finales = $curso?->minimo_examenes_finales;
            $r->examen_final = false;
            $r->examen_final_superado = false;

            if (!$r->evaluacion_continua_superada) {
                foreach ($unidades as $unidad) {
                    if ($unidad->hasEtiquetas(['examen', 'final'])
                        && $user->num_completadas('examen', $unidad->id, $milestone) > 0
                        && $r->resultados_unidades[$unidad->id]->actividad > 0) {

                        $nota_examen = $r->resultados_unidades[$unidad->id]->tarea / $r->resultados_unidades[$unidad->id]->actividad;
                        $minimo_examenes_finales_superado = $nota_examen >= $r->minimo_examenes_finales / 100;

                        if (!$r->examen_final) {
                            $r->examen_final = true;
                            $nota = 0;
                        }
                        if ($nota_examen * 10 > $nota) {
                            $nota = $nota_examen * 10;
                        }
                        if ($minimo_examenes_finales_superado) {
                            $r->examen_final_superado = true;
                        }
                    }
                }
            }

            // Si la nota es por examen final, aplicar el porcentaje tope
            if ($r->examen_final && isset($curso->maximo_recuperable_examenes_finales))
                $nota = min($nota, $curso->maximo_recuperable_examenes_finales / 10);

            // Nota manual
            $temp = $user->cursos()->wherePivot('curso_id', $curso?->id)->first();
            if ($temp != null && isset($temp->pivot->nota)) {
                $r->hay_nota_manual = true;
                $nota = $temp->pivot->nota;
                if ($nota >= 5) {
                    $r->nota_manual_superada = true;
                }
            }

            // Nota final
            $r->nota_numerica = $nota;

            return $r;
        });
    }

    public function cache_clears()
    {
        return $this->hasMany(CacheClear::class);
    }

    public function clearCache(): void
    {
        $user = $this;

        foreach ($this->keys as $key) {
            Cache::forget($key . $user->id);
        }

        foreach ($user->cursos as $curso) {
            foreach ($curso->milestones as $milestone) {
                Cache::forget('calificaciones_' . $user->id . $milestone->cache_key);
            }
        }

        Cache::forget("user.{$user->id}");
        Cache::forget("user.{$user->id}.{$user->getRememberToken()}");
        Cache::forget("roles_{$user->id}");
    }

    public function clearSession(): void
    {
        session()->forget([
            'filtrar_organization_actual',
            'filtrar_user_actual',
            'filtrar_curso_actual',
            'filtrar_milestone_actual',
            'tags_usuario',
            'tags_actividades',
            'profesor_filtro_etiquetas',
            'profesor_filtro_alumnos',
            'profesor_unidad_id_disponibles',
            'profesor_filtro_alumnos_bloqueados',
            'profesor_filtro_actividades_examen',
            'profesor_unidad_id_disponibles',
            'profesor_filtro_actividades_etiquetas',
            'num_enviadas',
        ]);
    }

    public function preferredLocale()
    {
        return setting_usuario('user_locale', $this) ?? App::getLocale();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    protected function casts(): array
    {
        return [
            'last_active' => 'datetime',
        ];
    }
}
