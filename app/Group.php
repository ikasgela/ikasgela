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
}
