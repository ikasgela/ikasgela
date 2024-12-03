<?php

namespace App\Http\View\Composers;

use Auth;
use Illuminate\View\View;

class ActividadesPendientesComposer
{
    public function compose(View $view)
    {
        $user = Auth::user();

        $num_actividades = !is_null($user) && $user->hasRole('profesor') ? $user->curso_actual()?->recuento_enviadas() : 0;

        $view->with('profesor_actividades_pendientes', $num_actividades);
    }
}
