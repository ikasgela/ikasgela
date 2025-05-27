<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperJPlag
 */
class JPlag extends Model
{
    use HasFactory;

    protected $fillable = [
        'tarea_id', 'match_id', 'percent'
    ];

    public function tarea()
    {
        return $this->belongsTo(Tarea::class, 'tarea_id', 'id');
    }

    public function match()
    {
        return $this->belongsTo(Tarea::class, 'match_id', 'id');
    }
}
