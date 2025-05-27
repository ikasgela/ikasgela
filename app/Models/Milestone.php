<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @mixin IdeHelperMilestone
 */
class Milestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'date', 'published', 'curso_id',
        '__import_id',
        'decimals', 'truncate',
        'normalizar_nota', 'ajuste_proporcional_nota',
    ];

    public function getFullNameAttribute()
    {
        return $this->name . " - " . $this->date->format('d/m/Y');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function getCacheKeyAttribute()
    {
        return "_" . Str::slug($this->name) . "_" . $this->date->timestamp;
    }

    #[Scope]
    protected function published($query)
    {
        return $query->where('published', true);
    }

    protected function casts(): array
    {
        return [
            'date' => 'datetime',
        ];
    }
}
