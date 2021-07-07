<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FileResource extends Model
{
    protected $fillable = [
        'titulo', 'descripcion',
        '__import_id', 'curso_id',
    ];

    public function actividades()
    {
        return $this
            ->belongsToMany(Actividad::class)
            ->withTimestamps();
    }

    public function files()
    {
        return $this->morphMany('App\File', 'file_upload');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }
}
