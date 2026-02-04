@if($rubric_is_editing)
    <div class="col-auto mb-3">
        <div class="btn-toolbar">
            <div class="btn-group-sm btn-group-vertical">
                <button
                    title="{{ $cabecera_horizontal ? __('Show header on the left') : __('Show header above') }}"
                    class="btn btn-secondary"
                    wire:click="toggle_cabecera_horizontal">
                    <i class="bi {{ $cabecera_horizontal ? 'bi-arrow-bar-right' : 'bi-arrow-bar-down' }}"></i>
                </button>
            </div>
            <div class="btn-group-sm btn-group-vertical ms-2">
                <button
                    title="{{ __('Move up') }}"
                    class="btn {{ !$this->is_first_criteria_group($criteria_group->id) ? 'btn-primary' : 'btn-light disabled' }}"
                    wire:click="$parent.up_criteria_group({{ $criteria_group->id }})">
                    <i class="bi bi-arrow-up"></i>
                </button>
                <button
                    title="{{ __('Move down') }}"
                    class="btn {{ !$this->is_last_criteria_group($criteria_group->id) ? 'btn-primary' : 'btn-light disabled' }}"
                    wire:click="$parent.down_criteria_group({{ $criteria_group->id }})">
                    <i class="bi bi-arrow-down"></i>
                </button>
            </div>
            <div class="btn-group-sm btn-group-vertical ms-2">
                <button
                    title="{{ __('Delete') }}"
                    class="btn btn-danger"
                    wire:click="$parent.delete_criteria_group({{ $criteria_group->id }})">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <div class="btn-group-sm btn-group-vertical ms-2">
                <button
                    title="{{ __('Duplicate') }}"
                    class="btn btn-secondary"
                    wire:click="$parent.duplicate_criteria_group({{ $criteria_group->id }})">
                    <i class="bi bi-copy"></i>
                </button>
            </div>
        </div>
    </div>
@endif
