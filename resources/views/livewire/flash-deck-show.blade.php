<div>
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between">
            <div><i class="bi bi-ui-checks-grid me-2"></i>{{ __('Deck') }}</div>
            @if(Auth::user()->hasAnyRole(['admin','profesor']))
                <div>
                    @if(!$flash_deck_is_qualifying)
                        @isset($actividad)
                            <a title="{{ __('Edit resources') }}"
                               href="{{ route('flash_decks.actividad', [$actividad->id]) }}"
                               class="text-link-light me-2">
                                <i class="bi bi-puzzle"></i>
                            </a>
                        @endisset
                    @endif
                    @if($flash_deck_is_qualifying)
                        <a title="{{ __('Show') }}"
                           href="{{ route('flash_decks.show', [$flash_deck->id]) }}"
                           class='text-link-light'><i class="bi bi-eye"></i></a>
                    @else
                        @if($flash_deck_is_editing)
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
        @if($flash_deck_is_editing)
            <div class="card-body pb-0">
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
                <div class="mb-3">
                    @if($is_editing_cabecera)
                        <input type="text" class="form-control" wire:model="descripcion" wire:keydown.enter="save"
                               placeholder="{{ __('Description') }}"/>
                    @elseif(!$descripcion)
                        <a wire:click.prevent="toggle_edit_cabecera">
                            <p class="card-text border border-1 text-muted px-2">{{ __('Description') }}</p>
                        </a>
                    @else
                        <a wire:click.prevent="toggle_edit_cabecera">
                            <p class="card-text">{{ $descripcion }}</p>
                        </a>
                    @endif
                </div>
            </div>
            <hr class="my-0">
        @elseif($titulo || $descripcion)
            <div class="card-body">
                @if($titulo)
                    <a wire:click.prevent="toggle_edit_cabecera">
                        <h5 class="card-title">{{ $titulo }}</h5>
                    </a>
                @endif
                @if($descripcion)
                    <a wire:click.prevent="toggle_edit_cabecera">
                        <p class="card-text">{{ $descripcion }}</p>
                    </a>
                @endif
            </div>
            <hr class="my-0">
        @endif
        @foreach($flash_deck->flash_cards as $flash_card)
            <livewire:flash-card-component
                :$flash_card
                :key="'flash-card-'.$flash_card->id"
                :$flash_deck_is_editing
                :$flash_deck_is_qualifying
            />
            @if(!$loop->last)
                <hr class="my-0">
            @endif
        @endforeach
        @if($flash_deck_is_editing)
            <hr class="my-0">
            <div class="p-3 text-center">
                <button class="btn btn-sm btn-success w-100"
                        wire:click="add_flash_card">
                    <i class="bi bi-plus-lg"></i>
                </button>
            </div>
        @endif
        @if($flash_deck->completada || $flash_deck_is_editing)
            <hr class="my-0">
            <div class="card-body">
                <div class="row">
                    <div class="col-2">
                        <h5 class="card-title">{{ __('Total') }}</h5>
                    </div>
                    <div class="col text-end">
                        @if($flash_deck->completada)
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
                            {{ $flash_deck->completada ? $this->total . '/' : '' }}{{ $this->max_total }}
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
    @include('layouts.errors')
</div>
