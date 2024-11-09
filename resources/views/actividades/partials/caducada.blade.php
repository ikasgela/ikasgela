@if($actividad->is_finished || $actividad->is_expired)
    @if($actividad->plantilla)
        <i class="fas fa-exclamation-triangle text-secondary ms-2" title="{{ trans_choice('tasks.expired', 1) }}"></i>
    @else
        <i class="fas fa-exclamation-triangle text-warning ms-2" title="{{ trans_choice('tasks.expired', 1) }}"></i>
    @endif
@endif
