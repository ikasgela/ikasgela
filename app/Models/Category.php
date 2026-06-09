<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;


/**
 * @mixin IdeHelperCategory
 */
class Category extends Model
{
    use HasFactory;
    use Cachable;

    protected $fillable = [
        'period_id', 'name', 'slug'
    ];

    public function getFullNameAttribute()
    {
        return $this->period->organization->name . ' - '
            . $this->period->name . ' - '
            . $this->name;
    }

    public function getPrettyNameAttribute()
    {
        return $this->period->organization->name . ' » '
            . $this->period->name . ' » '
            . $this->name;
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function cursos()
    {
        return $this->hasMany(Curso::class);
    }
}
