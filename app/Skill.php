<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = [
        'name', 'description', 'organization_id', 'peso_examen', 'minimo_competencias'
    ];

    public function qualifications()
    {
        return $this
            ->belongsToMany('App\Qualification')
            ->withTimestamps()
            ->withPivot([
                'percentage'
            ]);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
