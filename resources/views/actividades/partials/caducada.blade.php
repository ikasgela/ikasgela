{!! !$actividad->hasEtiqueta('examen')
        && ($actividad->is_finished || $actividad->is_expired)
        ? '<i class="fas fa-exclamation-triangle text-warning" title="'.trans_choice('tasks.expired', 1).'"></i>' : '' !!}
