<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unidad extends Model
{
    protected $table = 'unidades';

    protected $fillable = [
        'curso_id', 'nombre', 'descripcion', 'slug', 'qualification_id'
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function actividades()
    {
        return $this->hasMany(Actividad::class);
    }

    public function qualification()
    {
        return $this->belongsTo(Qualification::class);
    }
}
