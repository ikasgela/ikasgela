<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'texto', 'correcto', 'seleccionado', 'feedback', 'orden'
    ];

    public function pregunta()
    {
        return $this->belongsTo(Pregunta::class);
    }
}
