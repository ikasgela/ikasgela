<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable = [
        'name', 'slug'
    ];

    public function periods()
    {
        return $this->hasMany(Period::class);
    }
}
