require('./bootstrap');

require('jquery-countdown');

// https://stackoverflow.com/a/17147973
// https://codepen.io/NaokiIshimura/pen/aEvQPY
$(document).ready(function ($) {
    $(".table-row").click(function () {
        window.location = $(this).data("href");
    });

    $(".table-row-blank").click(function () {
        window.open($(this).data("href"));
    });

    $(".table-cell-click .clickable").click(function () {
        window.location = $(this).parent().data("href");
    });

    $('#seleccionar_usuarios').change(function () {
        $("input[name^='usuarios_seleccionados']").not(this).prop('checked', this.checked);
    });

    $('#seleccionar_actividades').change(function () {
        $("input[name^='seleccionadas']").not(this).prop('checked', this.checked);
    });

    $('#seleccionar_asignadas').change(function () {
        $("input[name^='asignadas']").not(this).prop('checked', this.checked);
    });

    $('#seleccionar_todos').change(function () {
        $("input[name='recipients[]']").not(this).prop('checked', this.checked);
    });

    $('#seleccionar_equipos').change(function () {
        $("input[name^='equipos_seleccionados']").not(this).prop('checked', this.checked);
    });

    $('.add').click(function () {
        var prefijo = $(this).data("selector");
        return !$('#' + prefijo + '-select2 option:selected').remove().appendTo('#' + prefijo + '-select1');
    });

    $('.remove').click(function () {
        var prefijo = $(this).data("selector");
        return !$('#' + prefijo + '-select1 option:selected').remove().appendTo('#' + prefijo + '-select2');
    });

    $('#boton_guardar').click(function () {
        $('.multi-select option').each(function () {
            $(this).attr('selected', true);
        });
    });

    $('#boton_feedback').click(function () {

        var extra = '';
        if (tinyMCE.activeEditor.getContent().length > 0) {
            extra = '\n';
        }

        var texto = tinyMCE.activeEditor.getContent();
        extra = extra + $('#feedback_id option:selected').attr('data-mensaje');

        tinyMCE.activeEditor.setContent(texto + extra);
    });

    $('#boton_feedback_actividad').click(function () {

        var extra = '';
        if (tinyMCE.activeEditor.getContent().length > 0) {
            extra = '\n';
        }

        var texto = tinyMCE.activeEditor.getContent();
        extra = extra + $('#feedback_actividad_id option:selected').attr('data-mensaje');

        tinyMCE.activeEditor.setContent(texto + extra);
    });

    $('#guardar_feedback').submit(function () {
        $('#mensaje').val(tinyMCE.activeEditor.getContent());
    })

    $('[data-countdown]').each(function () {
        var $this = $(this);
        var finalDate = $(this).data('countdown');
        var locale = $('html').attr('lang');
        $this.countdown(finalDate, function (event) {
            var dias = event.strftime('%-D');
            if (dias > 0) {
                if (locale === 'es') {
                    $(this).html(event.strftime('%-D día%!D:s;, %H:%M:%S'));
                } else if (locale === 'eu') {
                    if (dias > 1) {
                        $(this).html(event.strftime('%-D egun, %H:%M:%S'));
                    } else {
                        $(this).html(event.strftime('egun bat, %H:%M:%S'));
                    }
                } else {
                    $(this).html(event.strftime('%-D day%!D:s;, %H:%M:%S'));
                }
            } else {
                $(this).html(event.strftime('%H:%M:%S'));
            }

            if (event.elapsed) {
                setTimeout(function () {
                    location.reload();
                }, 1050);
            }
        });
    });

    $('.single_click').on('click', function (e) {
        $(this).children('i').show();
        if ($(this).hasClass("disabled")) {
            e.preventDefault();
        }
        $(this).addClass("disabled");
    });

    $('#nuevo_mensaje').submit(function (e) {
        tinyMCE.activeEditor.plugins.autosave.removeDraft();
    });

    $('.c-header-toggler').bind('click', function (e) {
        var is_sidebar_open = $('#sidebar').hasClass("c-sidebar-lg-show");
        axios.post('/settings/api', {
            sidebar_open: is_sidebar_open,
        });
    });

    // https://github.com/iTeeLion/jquery.checkbox-shift-selector.js
    var chkboxShiftLastChecked = [];

    $('[data-chkbox-shiftsel]').click(function (e) {
        var chkboxType = $(this).data('chkbox-shiftsel');
        if (chkboxType === '') {
            chkboxType = 'default';
        }
        var $chkboxes = $('[data-chkbox-shiftsel="' + chkboxType + '"]');

        if (!chkboxShiftLastChecked[chkboxType]) {
            chkboxShiftLastChecked[chkboxType] = this;
            return;
        }

        if (e.shiftKey) {
            var start = $chkboxes.index(this);
            var end = $chkboxes.index(chkboxShiftLastChecked[chkboxType]);

            $chkboxes.slice(Math.min(start, end), Math.max(start, end) + 1).prop('checked', chkboxShiftLastChecked[chkboxType].checked);
        }

        chkboxShiftLastChecked[chkboxType] = this;
    });
});
