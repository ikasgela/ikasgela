@use(GrahamCampbell\Markdown\Facades\Markdown)
<div>
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between">
            <div><i class="bi bi-ui-checks-grid me-2"></i>{{ __('Rubric') }}</div>
            @if(Auth::user()->hasAnyRole(['admin','profesor']))
                <div>
                    @if(!$rubric_is_qualifying)
                        @isset($actividad)
                            <a title="{{ __('Edit resources') }}"
                               href="{{ route('rubrics.actividad', [$actividad->id]) }}"
                               class="text-link-light me-2">
                                <i class="bi bi-puzzle"></i>
                            </a>
                        @endisset
                    @endif
                    @if($rubric_is_qualifying)
                        <a title="{{ __('Show') }}"
                           href="{{ route('rubrics.show', [$rubric->id]) }}"
                           class='text-link-light'><i class="bi bi-eye"></i></a>
                    @else
                        @if($rubric_is_editing)
                            <a title="{{ __('Save') }}"
                               href="#"
                               wire:click.prevent="toggle_edit"
                               class='text-warning'><i class="bi bi-floppy-fill"></i></a>
                        @else
                            <a title="{{ __('Edit') }}"
                               href="#"
                               wire:click.prevent="toggle_edit"
                               class='text-link-light'><i class="bi bi-pencil-square"></i></a>
                        @endif
                    @endif
                </div>
            @endif
        </div>
        @if($rubric_is_editing)
            <div class="card-body pb-0">
                <div class="row">
                    <div class="col">
                        <div class="mb-2">
                            @if($is_editing_cabecera)
                                <input type="text" class="form-control" wire:model="titulo" wire:keydown.enter="save"
                                       placeholder="{{ __('Title') }}"/>
                            @elseif(!$titulo)
                                <a wire:click.prevent="toggle_edit_cabecera">
                                    <h5 class="card-title border border-1 text-muted px-2">{{ __('Title') }}</h5>
                                </a>
                            @else
                                <a wire:click.prevent="toggle_edit_cabecera">
                                    <h5 class="card-title">{{ $titulo }}</h5>
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="btn-toolbar">
                            <div class="btn-group-sm btn-group-vertical">
                                <button class="btn {{ $titulo_visible ? 'btn-primary' : 'btn-secondary' }}"
                                        title="{{ $titulo_visible ? __('Visible') : __('Hidden') }}"
                                        wire:click="toggle_titulo_visible">
                                    <i class="bi {{ $titulo_visible ? 'bi-eye' : 'bi-eye-slash' }}"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="mb-3">
                            @if($is_editing_cabecera)
                                @include('livewire.partials.descripcion-textarea')
                            @elseif(!$descripcion)
                                <a wire:click.prevent="toggle_edit_cabecera">
                                    <p class="card-text border border-1 text-muted px-2">{{ __('Description') }}</p>
                                </a>
                            @else
                                <a wire:click.prevent="toggle_edit_cabecera">
                                    <div class="contenedor_descripcion">
                                        {!! Markdown::convert($descripcion) !!}
                                    </div>
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="col-auto mb-3">
                        <div class="btn-toolbar">
                            <div class="btn-group-sm btn-group-vertical">
                                <button class="btn {{ $descripcion_visible ? 'btn-primary' : 'btn-secondary' }}"
                                        title="{{ $descripcion_visible ? __('Visible') : trans_choice('tasks.hidden', 1) }}"
                                        wire:click="toggle_descripcion_visible">
                                    <i class="bi {{ $descripcion_visible ? 'bi-eye' : 'bi-eye-slash' }}"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="my-0">
        @elseif($titulo && $titulo_visible || $descripcion && $descripcion_visible)
            <div class="card-body">
                @if($titulo && $titulo_visible)
                    <a wire:click.prevent="toggle_edit_cabecera">
                        <h5 class="card-title">{{ $titulo }}</h5>
                    </a>
                @endif
                @if($descripcion && $descripcion_visible)
                    <a wire:click.prevent="toggle_edit_cabecera">
                        <div class="contenedor_descripcion">
                            {!! Markdown::convert($descripcion) !!}
                        </div>
                    </a>
                @endif
            </div>
            <hr class="my-0">
        @endif
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
                        title="{{ __('Add criteria group') }}"
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
                @if($rubric_is_editing)
                    <div class="col mb-3">
                        <div class="btn-toolbar">
                            <div class="btn-group-sm btn-group-vertical">
                                <button
                                    title="{{ __('Exclude unrated criteria groups when calculating totals') }}"
                                    class="btn {{ $excluir_no_seleccionadas ? 'btn-primary' : 'btn-secondary' }}"
                                    wire:click="toggle_excluir_no_seleccionadas">
                                    <i class="bi bi-ui-checks-grid"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col text-end">
                    @if($rubric->completada)
                        <button
                            disabled
                            class="btn opacity-100 btn-warning p-3 me-2" style="min-width: 6em;">
                            {{ $this->total > 0 ? formato_decimales($this->total / $this->max_total * 100) : 0 }}
                            /100
                        </button>
                    @endif
                    <button
                        disabled
                        class="btn opacity-100 btn-secondary p-3" style="min-width: 6em;">
                        {{ $rubric->completada ? $this->total . '/' : '' }}{{ $this->max_total }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.errors')
</div>
