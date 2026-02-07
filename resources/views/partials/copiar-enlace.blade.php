<script>
    function copyToClipboard(el, text) {
        const message = "{{ __('Link copied') }}.";

        // Intentar escribir en el portapapeles
        navigator.clipboard.writeText(text).then(function () {
            try {
                // Si ya hay un popover creado en este elemento, lo eliminamos antes
                if (el && el._bs_popover) {
                    try {
                        el._bs_popover.dispose();
                    } catch (e) {
                    }
                    el._bs_popover = null;
                }

                if (!el) return;

                // Guardar el título original
                var originalTitle = el.getAttribute('title') || '';
                var originalBsTitle = el.getAttribute('data-bs-title') || '';
                var originalBsOriginalTitle = el.getAttribute('data-bs-original-title') || '';

                // Eliminar cualquier atributo de título que pueda existir
                el.removeAttribute('title');
                el.removeAttribute('data-bs-title');
                el.removeAttribute('data-bs-original-title');
                el.removeAttribute('data-original-title');

                // Asignar el contenido del popover
                el.setAttribute('data-bs-toggle', 'popover');
                el.setAttribute('data-bs-content', message);

                // Crear instancia de Bootstrap Popover (trigger manual)
                var pop = new bootstrap.Popover(el, {
                    trigger: 'manual',
                    placement: 'top',
                    title: '',
                    content: message
                });

                // Guardar la referencia para poder limpiarla
                el._bs_popover = pop;

                // Mostrar el popover
                pop.show();

                // Ocultarlo y destruirlo después de 2 segundos
                setTimeout(function () {
                    try {
                        pop.hide();
                        pop.dispose();
                        el._bs_popover = null;

                        // Restaurar el título original
                        if (originalTitle) {
                            el.setAttribute('title', originalTitle);
                        }
                        if (originalBsTitle) {
                            el.setAttribute('data-bs-title', originalBsTitle);
                        }
                        if (originalBsOriginalTitle) {
                            el.setAttribute('data-bs-original-title', originalBsOriginalTitle);
                        }

                        // Limpiar los atributos del popover
                        el.removeAttribute('data-bs-toggle');
                        el.removeAttribute('data-bs-content');
                    } catch (e) {
                        // Silenciar errores de limpieza
                    }
                }, 2000);
            } catch (e) {
                console.error('Popover error:', e);
            }
        }).catch(function (err) {
            console.error('Clipboard write failed', err);
        });
    }
</script>
