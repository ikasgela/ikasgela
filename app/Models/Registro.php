<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperRegistro
 */
class Registro extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'tarea_id', 'estado', 'timestamp', 'detalles', 'curso_id'
    ];

    public $timestamps = false;

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
        'timestamp'
    ];

    public function tarea()
    {
        return $this->belongsTo(Tarea::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
