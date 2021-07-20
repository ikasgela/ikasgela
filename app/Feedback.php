<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'curso_id', 'mensaje', 'titulo', 'curso_type',
        '__import_id',
        'orden',
    ];

    public function curso()
    {
        return $this->morphTo();
    }
}
