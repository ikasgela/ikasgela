<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Tarea extends Pivot
{
    protected $table = 'tareas';

    public $incrementing = true;

    // Modificar tambien el pivote en \App\User::actividades
    protected $fillable = [
        'estado', 'aceptada', 'enviada', 'revisada', 'feedback_recibido', 'feedback', 'puntuacion', 'feedback_leido', 'archivada'
    ];

}
