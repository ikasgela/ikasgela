{!! !$actividad->hasEtiqueta('examen')
        && (!is_null($actividad->fecha_entrega) && $actividad->fecha_entrega < now()
        || !is_null($actividad->fecha_limite) && $actividad->fecha_limite < now())
        ? '<i class="fas fa-exclamation-triangle text-warning" title="'.trans_choice('tasks.expired', 1).'"></i>' : '' !!}
