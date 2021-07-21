<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CacheClear extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'fecha',
    ];

    protected $dates = [
        'fecha'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
