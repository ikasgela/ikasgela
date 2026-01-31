import './bootstrap';

import '../../vendor/aliqasemzadeh/livewire-bootstrap-modal/resources/js/modals.js';

import './darkmode';

// https://stackoverflow.com/a/17147973
// https://codepen.io/NaokiIshimura/pen/aEvQPY
document.addEventListener('DOMContentLoaded', function () {
    // Table row click navigation
    document.querySelectorAll('.table-row').forEach(function (row) {
        row.addEventListener('click', function () {
            window.location = this.dataset.href;
        });
    });

    document.querySelectorAll('.table-row-blank').forEach(function (row) {
        row.addEventListener('click', function () {
            window.open(this.dataset.href);
        });
    });

    document.querySelectorAll('.table-cell-click .clickable').forEach(function (cell) {
        cell.addEventListener('click', function () {
            window.location = this.parentElement.dataset.href;
        });
    });

    // Checkbox select all handlers
    const seleccionarUsuarios = document.getElementById('seleccionar_usuarios');
    if (seleccionarUsuarios) {
        seleccionarUsuarios.addEventListener('change', function () {
            document.querySelectorAll("input[name^='usuarios_seleccionados']").forEach(function (checkbox) {
                if (checkbox !== seleccionarUsuarios) {
                    checkbox.checked = seleccionarUsuarios.checked;
                }
            });
        });
    }

    const seleccionarActividades = document.getElementById('seleccionar_actividades');
    if (seleccionarActividades) {
        seleccionarActividades.addEventListener('change', function () {
            document.querySelectorAll("input[name^='seleccionadas']").forEach(function (checkbox) {
                if (checkbox !== seleccionarActividades) {
                    checkbox.checked = seleccionarActividades.checked;
                }
            });
        });
    }

    const seleccionarAsignadas = document.getElementById('seleccionar_asignadas');
    if (seleccionarAsignadas) {
        seleccionarAsignadas.addEventListener('change', function () {
            document.querySelectorAll("input[name^='asignadas']").forEach(function (checkbox) {
                if (checkbox !== seleccionarAsignadas) {
                    checkbox.checked = seleccionarAsignadas.checked;
                }
            });
        });
    }

    const seleccionarTodos = document.getElementById('seleccionar_todos');
    if (seleccionarTodos) {
        seleccionarTodos.addEventListener('change', function () {
            document.querySelectorAll("input[name='recipients[]']").forEach(function (checkbox) {
                if (checkbox !== seleccionarTodos) {
                    checkbox.checked = seleccionarTodos.checked;
                }
            });
        });
    }

    const seleccionarEquipos = document.getElementById('seleccionar_equipos');
    if (seleccionarEquipos) {
        seleccionarEquipos.addEventListener('change', function () {
            document.querySelectorAll("input[name^='equipos_seleccionados']").forEach(function (checkbox) {
                if (checkbox !== seleccionarEquipos) {
                    checkbox.checked = seleccionarEquipos.checked;
                }
            });
        });
    }

    // Multi-select add/remove buttons
    document.querySelectorAll('.add').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const prefijo = this.dataset.selector;
            const select2 = document.getElementById(prefijo + '-select2');
            const select1 = document.getElementById(prefijo + '-select1');
            if (select2 && select1) {
                Array.from(select2.selectedOptions).forEach(function (option) {
                    select1.appendChild(option);
                });
            }
            return false;
        });
    });

    document.querySelectorAll('.remove').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const prefijo = this.dataset.selector;
            const select1 = document.getElementById(prefijo + '-select1');
            const select2 = document.getElementById(prefijo + '-select2');
            if (select1 && select2) {
                Array.from(select1.selectedOptions).forEach(function (option) {
                    select2.appendChild(option);
                });
            }
            return false;
        });
    });

    const botonGuardar = document.getElementById('boton_guardar');
    if (botonGuardar) {
        botonGuardar.addEventListener('click', function () {
            document.querySelectorAll('.multi-select option').forEach(function (option) {
                option.selected = true;
            });
        });
    }

    // TinyMCE feedback buttons
    const botonFeedback = document.getElementById('boton_feedback');
    if (botonFeedback) {
        botonFeedback.addEventListener('click', function () {
            let extra = '';
            if (tinyMCE.activeEditor.getContent().length > 0) {
                extra = '\n';
            }

            const texto = tinyMCE.activeEditor.getContent();
            const feedbackSelect = document.getElementById('feedback_id');
            extra = extra + feedbackSelect.selectedOptions[0].getAttribute('data-mensaje');

            tinyMCE.activeEditor.setContent(texto + extra);
        });
    }

    const botonFeedbackActividad = document.getElementById('boton_feedback_actividad');
    if (botonFeedbackActividad) {
        botonFeedbackActividad.addEventListener('click', function () {
            let extra = '';
            if (tinyMCE.activeEditor.getContent().length > 0) {
                extra = '\n';
            }

            const texto = tinyMCE.activeEditor.getContent();
            const feedbackActividadSelect = document.getElementById('feedback_actividad_id');
            extra = extra + feedbackActividadSelect.selectedOptions[0].getAttribute('data-mensaje');

            tinyMCE.activeEditor.setContent(texto + extra);
        });
    }

    const guardarFeedback = document.getElementById('guardar_feedback');
    if (guardarFeedback) {
        guardarFeedback.addEventListener('submit', function () {
            document.getElementById('mensaje').value = tinyMCE.activeEditor.getContent();
        });
    }

    // Countdown - vanilla JS implementation
    document.querySelectorAll('[data-countdown]').forEach(function (element) {
        const finalDate = new Date(element.dataset.countdown).getTime();
        const locale = document.documentElement.lang;

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = finalDate - now;

            if (distance < 0) {
                // Countdown finished
                setTimeout(function () {
                    location.reload();
                }, 1050);
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            const pad = (num) => String(num).padStart(2, '0');
            const timeStr = `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;

            if (days > 0) {
                if (locale === 'es') {
                    element.textContent = `${days} día${days !== 1 ? 's' : ''}, ${timeStr}`;
                } else if (locale === 'eu') {
                    element.textContent = days > 1 ? `${days} egun, ${timeStr}` : `egun bat, ${timeStr}`;
                } else {
                    element.textContent = `${days} day${days !== 1 ? 's' : ''}, ${timeStr}`;
                }
            } else {
                element.textContent = timeStr;
            }
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    });

    // Single click disable button
    document.querySelectorAll('.single_click').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            const span = this.querySelector('span');
            if (span) {
                span.style.display = '';
            }
            if (this.classList.contains('disabled')) {
                e.preventDefault();
            }
            this.classList.add('disabled');
        });
    });

    // Message form autosave clear
    const nuevoMensaje = document.getElementById('nuevo_mensaje');
    if (nuevoMensaje) {
        nuevoMensaje.addEventListener('submit', function () {
            tinyMCE.activeEditor.plugins.autosave.removeDraft();
        });
    }

    // Sidebar toggler
    document.querySelectorAll('.c-header-toggler').forEach(function (toggler) {
        toggler.addEventListener('click', function () {
            const sidebar = document.getElementById('sidebar');
            const isSidebarOpen = sidebar && sidebar.classList.contains('c-sidebar-lg-show');
            axios.post('/settings/api', {
                sidebar_open: isSidebarOpen,
            });
        });
    });

    // Checkbox shift-select functionality
    // https://github.com/iTeeLion/jquery.checkbox-shift-selector.js
    const chkboxShiftLastChecked = {};

    document.querySelectorAll('[data-chkbox-shiftsel]').forEach(function (checkbox) {
        checkbox.addEventListener('click', function (e) {
            let chkboxType = this.dataset.chkboxShiftsel;
            if (chkboxType === '') {
                chkboxType = 'default';
            }
            const chkboxes = Array.from(document.querySelectorAll('[data-chkbox-shiftsel="' + chkboxType + '"]'));

            if (!chkboxShiftLastChecked[chkboxType]) {
                chkboxShiftLastChecked[chkboxType] = this;
                return;
            }

            if (e.shiftKey) {
                const start = chkboxes.indexOf(this);
                const end = chkboxes.indexOf(chkboxShiftLastChecked[chkboxType]);

                const from = Math.min(start, end);
                const to = Math.max(start, end) + 1;

                chkboxes.slice(from, to).forEach(function (cb) {
                    cb.checked = chkboxShiftLastChecked[chkboxType].checked;
                });
            }

            chkboxShiftLastChecked[chkboxType] = this;
        });
    });
});
