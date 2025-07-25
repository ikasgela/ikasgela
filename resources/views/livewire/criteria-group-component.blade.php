<div class="card-body row pb-0">
    <div class="col-2 mb-3">
        @if($rubric_is_editing && $is_editing)
            <input type="text" class="form-control mb-2" wire:model="titulo" wire:keydown.enter="save"
                   placeholder="{{ __('Title') }}"/>
        @elseif($rubric_is_editing && !$titulo)
            <a wire:click.prevent="toggle_edit">
                <h5 class="card-title border border-1 text-muted px-2">{{ __('Title') }}</h5>
            </a>
        @elseif($titulo)
            <a wire:click.prevent="toggle_edit">
                <h5 class="card-title">{{ $criteria_group->titulo }}</h5>
            </a>
        @endif
        @if($rubric_is_editing && $is_editing)
            <input type="text" class="form-control" wire:model="descripcion" wire:keydown.enter="save"
                   placeholder="{{ __('Description') }}"/>
        @elseif($rubric_is_editing && !$descripcion)
            <a wire:click.prevent="toggle_edit">
                <p class="small border border-1 text-muted px-2">{{ __('Description') }}</p>
            </a>
        @elseif($descripcion)
            <a wire:click.prevent="toggle_edit">
                <span class="small">{{ $criteria_group->descripcion }}</span>
            </a>
        @endif
    </div>
    <div class="col overflow-x-auto">
        <div class="row h-100 flex-nowrap">
            @foreach($criteria_group->criterias as $criteria)
                <livewire:criteria-component
                    :$criteria
                    :key="'criteria-'.$criteria->id"
                    :$rubric_is_editing
                    :$rubric_is_qualifying
                />
            @endforeach
            @if($rubric_is_editing)
                <div class="col-auto align-content-center mb-3">
                    <button class="btn btn-sm btn-success h-100"
                            wire:click="add_criteria({{ $criteria_group->id }})">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>
            @endif
        </div>
    </div>
    @if($rubric_is_editing)
        <div class="col-auto">
            <div class="btn-toolbar">
                <div class="btn-group-sm btn-group-vertical">
                    <button
                        class="btn {{ !$this->is_first_criteria_group($criteria_group->id) ? 'btn-primary' : 'btn-light disabled' }}"
                        wire:click="$parent.up_criteria_group({{ $criteria_group->id }})">
                        <i class="bi bi-arrow-up"></i>
                    </button>
                    <button
                        class="btn {{ !$this->is_last_criteria_group($criteria_group->id) ? 'btn-primary' : 'btn-light disabled' }}"
                        wire:click="$parent.down_criteria_group({{ $criteria_group->id }})">
                        <i class="bi bi-arrow-down"></i>
                    </button>
                </div>
                <div class="btn-group-sm btn-group-vertical ms-2">
                    <button class="btn btn-danger"
                            wire:click="$parent.delete_criteria_group({{ $criteria_group->id }})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif
    @if($criteria_group->criterias->count() > 0)
        <div class="col-auto mb-3">
            @if($rubric_is_editing || !$this->is_rubric_completed)
                <button
                    disabled
                    class="btn opacity-100 btn-secondary p-3" style="min-width: 6em;">
                    {{ $this->max_total }}
                </button>
            @else
                <button
                    disabled
                    class="btn opacity-100 btn-secondary p-3" style="min-width: 6em;">
                    {{ $this->total }}/{{ $this->max_total }}
                </button>
            @endif
        </div>
    @endif
</div>
