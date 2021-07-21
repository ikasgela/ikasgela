@if($actividad->is_finished || $actividad->is_expired)
    @if($actividad->plantilla)
        <i class="fas fa-exclamation-triangle text-secondary" title="{{ trans_choice('tasks.expired', 1) }}"></i>
    @else
        <i class="fas fa-exclamation-triangle text-warning" title="{{ trans_choice('tasks.expired', 1) }}"></i>
    @endif
@endif
