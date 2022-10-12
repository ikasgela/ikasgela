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
}
