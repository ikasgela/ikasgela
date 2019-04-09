<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'username', 'tutorial'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function avatar_url()
    {
        $hash = md5(strtolower(trim($this->email)));
        return "https://www.gravatar.com/avatar/$hash?s=200&d=identicon";
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
                'aceptada',
                'fecha_limite',
                'enviada',
                'revisada',
                'feedback',
                'puntuacion',
                'terminada',
                'archivada'
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
        return $this->actividades()->wherePivot('estado', 10)->get();
    }

    public function actividades_ocultas()
    {
        return $this->actividades()->wherePivot('estado', 11)->get();
    }

    public function actividades_aceptadas()
    {
        return $this->actividades()->wherePivot('estado', 20)->get();
    }

    public function actividades_enviadas()
    {
        return $this->actividades()->wherePivot('estado', 30)->get();
    }

    public function actividades_revisadas()
    {
        return $this->actividades()->wherePivotIn('estado', [40, 41])->get();
    }

    public function actividades_archivadas()
    {
        return $this->actividades()->wherePivot('estado', 60)->get();
    }

    public function actividades_asignadas()
    {
        return $this->actividades()->wherePivotIn('estado', [60, 11], 'and', 'notin')->get();
    }
}
