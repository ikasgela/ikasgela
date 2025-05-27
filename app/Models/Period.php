<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperPeriod
 */
class Period extends Model
{
    use HasFactory;

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

    #[Scope]
    protected function organizacionActual($query)
    {
        return $query->where('organization_id', setting_usuario('_organization_id'));
    }
}
