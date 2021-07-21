<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

class Period extends Model
{
    use HasFactory;
    use Rememberable;

    protected $rememberFor;
    protected $rememberCacheTag;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->rememberCacheTag = 'period';
        $this->rememberFor = config('ikasgela.eloquent_cache_time', 60);
    }

    protected $fillable = [
        'organization_id', 'name', 'slug'
    ];

    public function getFullNameAttribute()
    {
        return $this->organization->name . ' - '
            . $this->name;
    }

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

    public function scopeOrganizacionActual($query)
    {
        return $query->where('organization_id', setting_usuario('_organization_id'));
    }
}
