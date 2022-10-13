<?php

namespace App\Models;

use Bkwld\Cloner\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuleGroup extends Model
{
    use HasFactory;
    use Cloneable;

    protected $cloneable_relations = ['rules'];

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

    public function actividad()
    {
        return Actividad::find($this->resultado);
    }
}
