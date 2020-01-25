<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

class Role extends Model
{
    use Rememberable;

    public $rememberCacheTag = 'query_role';
    public $rememberFor = 60;

    protected $fillable = [
        'name', 'description'
    ];

    public function users()
    {
        return $this
            ->belongsToMany('App\User')
            ->withTimestamps();
    }
}
