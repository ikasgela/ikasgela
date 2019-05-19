<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Registro extends Model
{
    protected $fillable = [
        'user_id', 'tarea_id', 'estado', 'timestamp', 'detalles'
    ];

    public $timestamps = false;

    public function tarea()
    {
        return $this->belongsTo(Tarea::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
