<?php

namespace App\Http\View\Composers;

use Auth;
use Illuminate\View\View;

class ActividadesAsignadasComposer
{
    public function compose(View $view)
    {
        $user = Auth::user();

        $num_actividades = $user->actividades_en_curso_autoavance()->count();

        $view->with('alumno_actividades_asignadas', $num_actividades);
    }
}
