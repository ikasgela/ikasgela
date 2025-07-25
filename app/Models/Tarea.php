<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @mixin IdeHelperTarea
 */
class Tarea extends Pivot
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    protected $table = 'tareas';

    public $incrementing = true;

    // Modificar tambien el pivote en \App\Models\User::actividades
    protected $fillable = [
        'estado',
        'feedback',
        'puntuacion',
        'intentos',
    ];

    public function actividad()
    {
        return $this->belongsTo(Actividad::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function registros()
    {
        return $this->hasMany(Registro::class);
    }

    public function jplags()
    {
        return $this->hasMany(JPlag::class);
    }

    #[Scope]
    protected function usuarioNoBloqueado($query)
    {
        return $query->whereHas('user', function ($query) {
            $query->where('blocked_date', null);
        });
    }

    #[Scope]
    protected function cursoActual($query)
    {
        return $query->whereHas('actividad.unidad.curso', function ($query) {
            $query->where('cursos.id', setting_usuario('curso_actual'));
        });
    }

    #[Scope]
    protected function noAutoAvance($query)
    {
        return $query->whereHas('actividad', function ($query) {
            $query->where('actividades.auto_avance', false);
        });
    }

    public function tiempoDedicado()
    {
        $aceptada = Registro::where('tarea_id', $this->id)->where('estado', 20)->first();
        $enviada = Registro::where('tarea_id', $this->id)->whereIn('estado', [30, 64])->first();

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
        return in_array($this->estado, [40, 60, 62, 64]);
    }

    public function getIsCompletadaArchivadaAttribute()
    {
        return in_array($this->estado, [62]);
    }

    public function getIsEnviadaAttribute()
    {
        return in_array($this->estado, [30]);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}
