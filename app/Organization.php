<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

class Organization extends Model
{
    use Rememberable;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->rememberCacheTag = 'organization';
        $this->rememberFor = config('ikasgela.eloquent_cache_time', 60);
    }

    protected $fillable = [
        'name', 'slug', 'current_period_id', 'registration_open', 'seats'
    ];

    public function periods()
    {
        return $this->hasMany(Period::class);
    }

    public function users()
    {
        return $this
            ->belongsToMany(User::class)
            ->withTimestamps();
    }

    public function qualifications()
    {
        return $this->hasMany(Qualification::class);
    }

    public function skills()
    {
        return $this->hasMany(Skill::class);
    }

    public function current_period()
    {
        return Period::find($this->current_period_id);
    }

    public function isRegistrationOpen()
    {
        return $this->registration_open && $this->seats > 0;
    }
}
