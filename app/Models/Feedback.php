<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'comentable_id', 'comentable_type',
        'mensaje', 'titulo',
        '__import_id',
        'orden',
    ];

    public function comentable()
    {
        return $this->morphTo();
    }
}
