<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Tarea extends Pivot
{
    protected $table = 'tareas';

    public $incrementing = true;

    // Modificar tambien el pivote en \App\User::actividades
    protected $fillable = [
        'estado',
        'aceptada',
        'fecha_limite',
        'enviada',
        'revisada',
        'feedback',
        'puntuacion',
        'terminada',
        'archivada'
    ];

    public function actividad()
    {
        return $this->belongsTo('App\Actividad');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
