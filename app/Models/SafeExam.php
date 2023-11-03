<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SafeExam extends Model
{
    use HasFactory;

    protected $fillable = [
        'token', 'quit_password',
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }
}
