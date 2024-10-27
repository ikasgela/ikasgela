<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperOrganization
 */
class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'current_period_id', 'registration_open', 'seats'
    ];

    public function getFullNameAttribute()
    {
        return $this->name;
    }

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

    public function current_period()
    {
        return Period::find($this->current_period_id);
    }

    public function isRegistrationOpen()
    {
        return $this->registration_open && $this->seats > 0;
    }
}
