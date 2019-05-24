<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'texto', 'correcto', 'feedback_ok', 'feedback_error', 'orden'
    ];

    public function pregunta()
    {
        return $this->belongsTo(Pregunta::class);
    }
}
