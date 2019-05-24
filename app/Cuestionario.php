<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cuestionario extends Model
{
    protected $fillable = [
        'titulo', 'descripcion', 'plantilla'
    ];

    public function actividades()
    {
        return $this
            ->belongsToMany(Actividad::class)
            ->withTimestamps();
    }

    public function preguntas()
    {
        return $this->hasMany(Pregunta::class);
    }
}
