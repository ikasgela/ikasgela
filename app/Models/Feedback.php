<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use YMigVal\LaravelModelCache\HasCachedQueries;

/**
 * @mixin IdeHelperFeedback
 */
class Feedback extends Model
{
    use HasFactory;
    use HasCachedQueries;

    protected $fillable = [
        'comentable_id', 'comentable_type',
        'mensaje', 'titulo',
        '__import_id',
        'orden',
    ];

    public function comentable()
    {
        return $this->morphTo();
    }
}
