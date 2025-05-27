<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperCacheClear
 */
class CacheClear extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'fecha',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected function casts(): array
    {
        return [
            'fecha' => 'datetime',
        ];
    }
}
