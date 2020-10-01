<?php

namespace App;

use App\Traits\Etiquetas;
use Cmgmyr\Messenger\Traits\Messagable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use LogsActivity;
    use Messagable;
    use Etiquetas;
    use Impersonate;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'surname', 'email', 'password', 'username', 'tutorial', 'last_active',
        'blocked_date', 'max_simultaneas', 'tags'
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

    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'last_active'];

    public function avatar_url($width = 64)
    {
        $hash = md5(strtolower(trim($this->email)));
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
        // Modificar tambien los campos en \App\Tarea::$fillable
        return $this->belongsToMany('App\Actividad', 'tareas')
            ->cursoActual()
            ->using('App\Tarea')
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
            ->belongsToMany('App\Role')
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
        if ($this->roles()->where('name', $role)->first()) {
            return true;
        }
        return false;
    }

    public function actividades_nuevas()
    {
        return $this->actividades()
            ->wherePivot('estado', 10);
    }

    public function actividades_ocultas()
    {
        return $this->actividades()
            ->wherePivot('estado', 11);
    }

    public function actividades_aceptadas()
    {
        return $this->actividades()
            ->wherePivotIn('estado', [20, 21]);
    }

    public function actividades_caducadas()
    {
        return $this->actividades()
            ->caducada()
            ->wherePivotNotIn('estado', [30, 40, 60, 62]);
    }

    public function actividades_en_curso()
    {
        return $this->actividades()
            ->where(function ($query) {
                $query
                    ->estados([10, 20, 21]);
            })
            ->orWhere(function ($query) {
                $query
                    ->estados([40, 41, 42]);
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

    public function actividades_revisadas()
    {
        return $this->actividades()
            ->where('auto_avance', false)
            ->wherePivotIn('estado', [40, 41]);
    }

    public function actividades_archivadas()
    {
        return $this->actividades()
            ->wherePivotIn('estado', [60, 62]);
    }

    public function actividades_completadas()
    {
        return $this->actividades()
            ->wherePivotIn('estado', [40, 60, 62]);
    }

    public function actividades_sin_completar()
    {
        return $this->actividades()
            ->wherePivotNotIn('estado', [40, 60, 62])
            ->tag('base');
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

    public function scopeOrganizacionActual($query)
    {
        return $query->whereHas('organizations', function ($query) {
            $query->where('organizations.id', setting_usuario('_organization_id'));
        });
    }

    public function scopeCursoActual($query)
    {
        return $query->whereHas('cursos', function ($query) {
            $query->where('cursos.id', setting_usuario('curso_actual'));
        });
    }

    public function scopeRolAlumno($query)
    {
        return $query->whereHas('roles', function ($query) {
            $query->where('name', 'alumno');
        });
    }

    public function scopeRolProfesor($query)
    {
        return $query->whereHas('roles', function ($query) {
            $query->where('name', 'profesor');
        });
    }

    public function scopeNoBloqueado($query)
    {
        return $query->where('blocked_date', null);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function getLastActiveTimeAttribute()
    {
        return $this->last_active ? $this->last_active->diffForHumans() : __('Never');
    }

    public function curso_actual()
    {
        setting()->setExtraColumns(['user_id' => $this->id]);
        return Curso::find(setting('curso_actual'));
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

    public function num_completadas($etiqueta, $unidad = null)
    {
        $total = 0;

        $query = $this->actividades_completadas();

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

    public function newThreadsCount()
    {
        return Hilo::forUserWithNewMessages($this->id)->cursoActual()->count();
    }
}
