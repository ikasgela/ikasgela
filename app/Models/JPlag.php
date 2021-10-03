<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JPlag extends Model
{
    use HasFactory;

    protected $fillable = [
        'intellij_project_id', 'match_id', 'percent'
    ];

    public function intellij_project()
    {
        return $this->belongsTo(IntellijProject::class);
    }

    public function match()
    {
        return $this->belongsTo(IntellijProject::class);
    }
}
