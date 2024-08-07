<?php

namespace App\Models;

use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperRule
 */
class Rule extends Model
{
    use HasFactory;
    use Cloneable;

    protected $fillable = [
        'propiedad', 'operador', 'valor', 'rule_group_id',
        '__import_id',
    ];

    public function rule_group()
    {
        return $this->belongsTo(RuleGroup::class);
    }
}
