<?php

namespace App;

use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    use Cloneable;

    protected $cloneable_relations = ['intellij_projects', 'youtube_videos', 'siguiente'];
    protected $clone_exempt_attributes = ['plantilla'];

    protected $table = 'actividades';

    protected $fillable = [
        'unidad_id', 'nombre', 'descripcion', 'puntuacion', 'plantilla', 'slug', 'final', 'siguiente'
    ];

    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }

    public function usuarios()
    {
        return $this->belongsToMany('App\User', 'tareas')->using('App\Tarea');
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
}
