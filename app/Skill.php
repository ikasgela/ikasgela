<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = [
        'name', 'description'
    ];

    public function qualifications()
    {
        return $this
            ->belongsToMany('App\Qualification')
            ->withTimestamps()
            ->withPivot([
                'percentage'
            ]);
    }
}
