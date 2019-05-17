<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Registro extends Model
{
    protected $fillable = [
        'user_is', 'tarea_id', 'estado_inicial', 'estado_final', 'timestamp', 'detalles'
    ];

    public $timestamps = false;

    public function tarea()
    {
        return $this->belongsTo(Tarea::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
