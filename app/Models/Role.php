<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use GeneaLabs\LaravelModelCaching\Traits\CachedPivotOperations;


/**
 * @mixin IdeHelperRole
 */
class Role extends Model
{
    use HasFactory;
    use Cachable;
    use CachedPivotOperations;

    protected $fillable = [
        'name', 'description'
    ];

    public function users()
    {
        return $this
            ->belongsToMany(User::class)
            ->withTimestamps();
    }
}
