<div>
    <form wire:submit="save">
        <div class="modal-header">
            <h5 class="modal-title">Modal title</h5>
            <button type="button" class="btn-close" wire:click="$dispatch('hideModal')" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <textarea wire:model="texto" class="form-control" rows="5" cols="20"></textarea>
            <input wire:model="puntuacion" type="text" class="form-control"/>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" wire:click="$dispatch('hideModal')">Close</button>
            <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
    </form>
</div>
