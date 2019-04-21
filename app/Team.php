<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'group_id', 'name', 'slug'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
