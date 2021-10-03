<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JPlag extends Model
{
    use HasFactory;

    protected $fillable = [
        'tarea_id', 'repository', 'match'
    ];

    public function tarea()
    {
        return $this->belongsTo(Tarea::class)->withTrashed();
    }
}
