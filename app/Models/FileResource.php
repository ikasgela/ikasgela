<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileResource extends Model
{
    use HasFactory;

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
        return $this->morphMany('App\Models\File', 'uploadable');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }
}
