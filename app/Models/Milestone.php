<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'date', 'published', 'curso_id',
        '__import_id',
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }
}
