<?php

namespace App\Models;

use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllowedApp extends Model
{
    use Cloneable, HasFactory;

    protected $fillable = [
        'title', 'os', 'executable', 'path', 'identifier', 'show_icon', 'force_close', 'disabled', 'safe_exam_id',
        '__import_id',
    ];

    public function safe_exam()
    {
        return $this->belongsTo(SafeExam::class);
    }
}
