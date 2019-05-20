<?php

namespace App;

use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Actividad extends Model
{
    use Cloneable;
    use LogsActivity;

    protected $cloneable_relations = ['intellij_projects', 'youtube_videos', 'siguiente', 'qualification'];
    protected $clone_exempt_attributes = ['plantilla', 'template'];

    protected $table = 'actividades';

    protected $fillable = [
        'unidad_id', 'nombre', 'descripcion', 'puntuacion', 'plantilla', 'slug', 'final', 'siguiente', 'auto_avance', 'qualification_id'
    ];

    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'tareas')
            ->using('App\Tarea')
            ->as('tarea')
            ->withPivot([
                'id',

                'estado',
                'fecha_limite',
                'feedback',
                'puntuacion',
            ]);
    }

    public function youtube_videos()
    {
        return $this
            ->belongsToMany(YoutubeVideo::class)
            ->withTimestamps();
    }

    public function intellij_projects()
    {
        return $this
            ->belongsToMany(IntellijProject::class)
            ->withTimestamps()
            ->withPivot([
                'fork'
            ]);
    }

    public function siguiente()
    {
        return $this->hasOne(Actividad::class, 'siguiente_id');
    }

    public function anterior()
    {
        return $this->belongsTo(Actividad::class, 'siguiente_id');
    }

    public function qualification()
    {
        return $this->belongsTo(Qualification::class);
    }
}
