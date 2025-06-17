<div>
    <form wire:submit="save">
        <div class="modal-header">
            <h1 class="modal-title fs-5">{{ __('Edit criteria') }}</h1>
            <button type="button" class="btn-close"
                    wire:click="$dispatch('hideModal')"
                    aria-label="{{ __('Close') }}"></button>
        </div>
        <div class="modal-body">
            <h2 class="fs-6">{{ __('Text') }}</h2>
            <textarea wire:model="texto" class="form-control" rows="5" cols="20"></textarea>
            <h2 class="fs-6 mt-3">{{ __('Score') }}</h2>
            <input wire:model="puntuacion" type="text" class="form-control"/>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary"
                    wire:click="$dispatch('hideModal')">{{ __('Close') }}</button>
            <button type="submit" class="btn btn-primary">{{ __('Save & Close') }}</button>
        </div>
    </form>
</div>
