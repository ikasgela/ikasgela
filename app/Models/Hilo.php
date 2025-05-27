<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Cmgmyr\Messenger\Models\Thread;

/**
 * @mixin IdeHelperHilo
 */
class Hilo extends Thread
{
    protected $fillable = ['subject', 'owner_id', 'noreply', 'alert', 'curso_id'];

    #[Scope]
    protected function cursoActual($query)
    {
        return $query->where('curso_id', setting_usuario('curso_actual'));
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }
}
