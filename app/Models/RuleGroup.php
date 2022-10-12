<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuleGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'operador', 'accion', 'resultado', 'selector_id',
    ];

    public function selector()
    {
        return $this->belongsTo(Selector::class);
    }

    public function rules()
    {
        return $this->hasMany(Rule::class);
    }
}
