<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;


/**
 * @mixin IdeHelperRole
 */
class Role extends Model
{
    use HasFactory;
    use Cachable;

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
