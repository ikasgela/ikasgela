<div>
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between">
            <div><i class="bi bi-ui-checks-grid me-2"></i>{{ __('Rubric') }}</div>
            <div>
                @if(!$rubric_is_qualifying)
                    @if(Auth::user()->hasAnyRole(['admin','profesor']))
                        @isset($actividad)
                            <a title="{{ __('Edit resources') }}"
                               href="{{ route('rubrics.actividad', [$actividad->id]) }}"
                               class="text-link-light me-2">
                                <i class="fas fa-list"></i>
                            </a>
                        @endisset
                        <a title="{{ __('Edit') }}"
                           href="#"
                           wire:click.prevent="toggle_edit"
                           class='text-link-light'><i class="fas fa-edit"></i></a>
                    @endif
                @endif
            </div>
        </div>
        <div class="card-body">
            @if($rubric_is_editing && $is_editing_cabecera)
                <input type="text" class="form-control" wire:model="titulo" wire:keydown.enter="save"
                       placeholder="{{ __('Title') }}"/>
            @elseif($rubric_is_editing && !$titulo)
                <a wire:click.prevent="toggle_edit_cabecera">
                    <h5 class="card-title border border-1 text-muted px-2">{{ __('Title') }}</h5>
                </a>
            @elseif($titulo)
                <a wire:click.prevent="toggle_edit_cabecera">
                    <h5 class="card-title">{{ $rubric->titulo }}</h5>
                </a>
            @endif
            @if($rubric_is_editing && $is_editing_cabecera)
                <input type="text" class="form-control" wire:model="descripcion" wire:keydown.enter="save"
                       placeholder="{{ __('Description') }}"/>
            @elseif($rubric_is_editing && !$descripcion)
                <a wire:click.prevent="toggle_edit_cabecera">
                    <p class="small border border-1 text-muted px-2">{{ __('Description') }}</p>
                </a>
            @elseif($descripcion)
                <a wire:click.prevent="toggle_edit_cabecera">
                    <p class="small">{{ $rubric->descripcion }}</p>
                </a>
            @endif
        </div>
        <hr class="my-0">
        @foreach($rubric->criteria_groups as $criteria_group)
            <livewire:criteria-group-component
                :$criteria_group
                :key="'criteria-group-'.$criteria_group->id"
                :$rubric_is_editing
                :$rubric_is_qualifying
            />
            @if(!$loop->last)
                <hr class="my-0">
            @endif
        @endforeach
        @if($rubric_is_editing)
            <hr class="my-0">
            <div class="p-3 text-center">
                <button class="btn btn-sm btn-success w-100"
                        wire:click="add_criteria_group">
                    <i class="bi bi-plus-lg"></i>
                </button>
            </div>
        @endif
        <hr class="my-0">
        <div class="card-body">
            <div class="row">
                <div class="col-2">
                    <h5 class="card-title">{{ __('Total') }}</h5>
                </div>
                <div class="col text-end">
                    <button
                        disabled
                        class="btn opacity-100 btn-warning p-3" style="min-width: 4em;">
                        {{ $rubric->total }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.errors')
</div>
