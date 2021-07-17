<?php

namespace App;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Tarea extends Pivot
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    protected $table = 'tareas';

    public $incrementing = true;

    // Modificar tambien el pivote en \App\User::actividades
    protected $fillable = [
        'estado',
        'feedback',
        'puntuacion',
        'intentos',
    ];

    public function actividad()
    {
        return $this->belongsTo('App\Actividad')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function registros()
    {
        return $this->hasMany(Registro::class);
    }

    public function scopeUsuarioNoBloqueado($query)
    {
        return $query->whereHas('user', function ($query) {
            $query->where('blocked_date', null);
        });
    }

    public function scopeCursoActual($query)
    {
        return $query->whereHas('actividad.unidad.curso', function ($query) {
            $query->where('cursos.id', setting_usuario('curso_actual'));
        });
    }

    public function scopeNoAutoAvance($query)
    {
        return $query->whereHas('actividad', function ($query) {
            $query->where('actividades.auto_avance', false);
        });
    }

    public function tiempoDedicado()
    {
        $aceptada = Registro::where('tarea_id', $this->id)->where('estado', 20)->first();
        $enviada = Registro::where('tarea_id', $this->id)->where('estado', 30)->first();

        if (!is_null($aceptada)) {
            if (!is_null($enviada)) {
                return $aceptada->timestamp->diffForHumans($enviada->timestamp, CarbonInterface::DIFF_ABSOLUTE);
            } else {
                return $aceptada->timestamp->diffForHumans();
            }
        } else {
            return __('Unknown');
        }
    }

    public function puntos()
    {
        return $this->puntuacion * ($this->actividad->multiplicador ?: 1);
    }

    public function archiveFiles()
    {
        foreach ($this->actividad->file_uploads()->get() as $file_upload) {
            foreach ($file_upload->files()->get() as $file) {
                $file->archived = true;
                $file->save();
            }
        }
    }

    public function getIsCompletadaAttribute()
    {
        return in_array($this->estado, [40, 60, 62]);
    }

    public function getIsEnviadaAttribute()
    {
        return in_array($this->estado, [30]);
    }
}
