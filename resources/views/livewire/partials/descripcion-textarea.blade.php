<textarea class="form-control" wire:model="descripcion"
          @keydown.enter="$event.shiftKey && ($event.preventDefault(), $wire.save())"
          placeholder="{{ __('Description') }}"></textarea>
