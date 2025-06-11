<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperUserExport
 */
class UserExport extends Model
{
    protected $fillable = [
        'fecha', 'url', 'user_id',
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
