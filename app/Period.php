<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

class Period extends Model
{
    use Rememberable;

    public $rememberCacheTag = 'query_period';
    public $rememberFor = 60;

    protected $fillable = [
        'organization_id', 'name', 'slug'
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }
}
