<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pregunta extends Model
{
    protected $fillable = [
        'titulo', 'texto', 'multiple', 'imagen'
    ];

    public function cuestionario()
    {
        return $this->belongsTo(Cuestionario::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
