import { Modal } from 'bootstrap';

document.addEventListener('livewire:init', () => {
    let modalsElement = document.getElementById('livewire-bootstrap-modal');

    if (modalsElement) {
        modalsElement.addEventListener('hidden.bs.modal', () => {
            Livewire.dispatch('resetModal');
        });

        Livewire.on('showBootstrapModal', () => {
            let modal = Modal.getOrCreateInstance(modalsElement);
            modal.show();
        });

        Livewire.on('hideModal', () => {
            let modal = Modal.getInstance(modalsElement);
            modal.hide();
            Livewire.dispatch('resetModal');
        });
    }
});
