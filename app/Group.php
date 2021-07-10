<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'period_id', 'name', 'slug'
    ];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function getFullNameAttribute()
    {
        return $this->period->organization->name . ' - '
            . $this->period->name . ' - '
            . $this->name;
    }
}
