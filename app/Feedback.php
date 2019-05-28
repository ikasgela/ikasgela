<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $fillable = [
        'curso_id', 'mensaje'
    ];

    public function feedback()
    {
        return $this->belongsTo(Curso::class);
    }
}
