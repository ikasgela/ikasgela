<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    protected $fillable = [
        'name', 'slug'
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
