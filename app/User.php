<?php

namespace App;

use Carbon\Carbon;
use Cmgmyr\Messenger\Traits\Messagable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use LogsActivity;
    use Messagable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'username', 'tutorial', 'last_active', 'blocked_date'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public $appends = ['last_active_time'];

    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'last_active'];

    public function avatar_url($width = 64)
    {
        $hash = md5(strtolower(trim($this->email)));
        return "https://www.gravatar.com/avatar/$hash?s=$width&d=identicon";
    }

    public static function generar_username($email)
    {
        return strtolower(str_replace('@', '.', $email));
    }

    public function actividades()
    {
        // Modificar tambien los campos en \App\Tarea::$fillable
        return $this->belongsToMany('App\Actividad', 'tareas')
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
        return $this->actividades()->wherePivot('estado', 10);
    }

    public function actividades_ocultas()
    {
        return $this->actividades()->wherePivot('estado', 11);
    }

    public function actividades_aceptadas()
    {
        return $this->actividades()->wherePivotIn('estado', [20, 21]);
    }

    public function actividades_enviadas()
    {
        return $this->actividades()->where('auto_avance', false)->wherePivot('estado', 30);
    }

    public function actividades_revisadas()
    {
        return $this->actividades()->where('auto_avance', false)->wherePivotIn('estado', [40, 41]);
    }

    public function actividades_archivadas()
    {
        return $this->actividades()->wherePivot('estado', 60);
    }

    public function actividades_asignadas()
    {
        return $this->actividades()
            ->wherePivotIn('estado', [60, 11], 'and', 'notin')
            ->where(function ($query) {
                $query->where('fecha_disponibilidad', '<=', Carbon::now())
                    ->orWhereNull('fecha_disponibilidad');
            })
            ->where(function ($query) {
                $query->where('fecha_limite', '>=', Carbon::now())
                    ->orWhereNull('fecha_limite');
            });
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

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function getLastActiveTimeAttribute()
    {
        return $this->last_active ? $this->last_active->diffForHumans() : __('Never');
    }
}
