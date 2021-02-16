<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class CacheClear extends Model
{
    protected $fillable = [
        'user_id', 'fecha',
    ];

    protected $dates = [
        'created_at', 'updated_at',
        'fecha',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
