<?php

namespace App\Models;

use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllowedUrl extends Model
{
    use Cloneable, HasFactory;

    protected $fillable = [
        'url', 'safe_exam_id',
    ];

    public function safe_exam()
    {
        return $this->belongsTo(SafeExam::class);
    }
}
