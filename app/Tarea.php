<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Tarea extends Pivot
{
    protected $table = 'tareas';

    public $incrementing = true;

    protected $fillable = [
        'aceptada', 'completada', 'revisada', 'feedback', 'puntuacion'
    ];

}
