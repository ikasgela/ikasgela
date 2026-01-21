function single_click_confirmar(event, boton, titulo, subtitulo, texto_confirmar, texto_cancelar) {

    if (typeof titulo !== 'undefined') {
        event.preventDefault();

        // Obtener o crear modal
        let modalElement = document.getElementById('single-click-confirm-modal');
        if (!modalElement) {
            modalElement = document.createElement('div');
            modalElement.id = 'single-click-confirm-modal';
            modalElement.className = 'modal fade';
            modalElement.setAttribute('tabindex', '-1');
            modalElement.innerHTML = `
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"></button>
                            <button type="button" class="btn btn-primary"></button>
                        </div>
                    </div>
                </div>`;
            document.body.appendChild(modalElement);
        }

        // Actualizar contenido
        modalElement.querySelector('.modal-title').textContent = titulo;
        modalElement.querySelector('.modal-body').textContent = subtitulo;
        const cancelBtn = modalElement.querySelector('.btn-secondary');
        const confirmBtn = modalElement.querySelector('.btn-primary');

        cancelBtn.textContent = texto_cancelar || 'Cancel';
        confirmBtn.textContent = texto_confirmar || 'Confirm';

        // Manejar confirmación
        // Clonamos el botón para eliminar listeners anteriores
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

        let modal = bootstrap.Modal.getInstance(modalElement);
        if (!modal) {
            modal = new bootstrap.Modal(modalElement);
        }

        newConfirmBtn.addEventListener('click', function () {
            modal.hide();

            activar_spinner(boton);

            if (boton.tagName === 'A') {
                window.location.href = boton.href;
            } else if (boton.type === 'submit' && boton.form) {
                if (boton.name && boton.value) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = boton.name;
                    input.value = boton.value;
                    boton.form.appendChild(input);
                }
                boton.form.submit();
            }
        });

        modal.show();

    } else {
        activar_spinner(boton);
    }
}

function activar_spinner(boton) {
    let spinner = boton.querySelector('.spinner-border');
    if (!spinner && boton.childNodes.length > 1) {
        spinner = boton.childNodes[1];
    }

    if (spinner) {
        spinner.style.display = "inline-block";
    }
    boton.classList.add('disabled');
}
