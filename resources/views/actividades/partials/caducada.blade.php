@if($actividad->is_finished || $actividad->is_expired)
    @if($actividad->plantilla)
        <i class="bi bi-exclamation-triangle-fill text-secondary ms-2" title="{{ trans_choice('tasks.expired', 1) }}"></i>
    @else
        <i class="bi bi-exclamation-triangle-fill text-warning ms-2" title="{{ trans_choice('tasks.expired', 1) }}"></i>
    @endif
@endif
