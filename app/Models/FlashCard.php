<?php

namespace App\Models;

use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperFlashCard
 */
class FlashCard extends Model
{
    use HasFactory;
    use Cloneable;
    use SoftDeletes;

    protected $fillable = [
        'titulo', 'descripcion', 'anverso', 'anverso_visible', 'reverso', 'reverso_visible', 'orden',
        'flash_deck_id',
        '__import_id',
    ];

    public function flash_deck()
    {
        return $this->belongsTo(FlashDeck::class);
    }
}
