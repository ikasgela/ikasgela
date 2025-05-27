<?php

namespace App\Http\View\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ActividadesAsignadasComposer
{
    public function compose(View $view)
    {
        $user = Auth::user();

        $num_actividades = !is_null($user) ? $user->num_actividades_asignadas_total() : 0;

        $view->with('alumno_actividades_asignadas', $num_actividades);
    }
}
