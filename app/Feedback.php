<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $fillable = [
        'curso_id', 'mensaje'
    ];

    public function curso()
    {
        return $this->morphTo();
    }
}
