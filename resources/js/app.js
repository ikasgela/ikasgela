/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

require('jquery-countdown');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import Toasted from 'vue-toasted';

Vue.use(Toasted)
Vue.toasted.register('error', message => message, {
    position: 'bottom-center',
    duration: 1000
})

Vue.component('profile', require('./components/profile/Profile.vue').default);
Vue.component('profile-password', require('./components/profile/Password.vue').default);

const app = new Vue({
    el: '#app'
});

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
        $("input[name='usuarios_seleccionados[]']").not(this).prop('checked', this.checked);
    });

    $('#seleccionar_actividades').change(function () {
        $("input[name='seleccionadas[]']").not(this).prop('checked', this.checked);
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
        $('select option').each(function () {
            $(this).attr('selected', true);
        });
    });

    $('#boton_feedback').click(function () {

        var extra = '';
        if (tinyMCE.activeEditor.getContent().length > 0) {
            extra = '\n';
        }

        var texto = tinyMCE.activeEditor.getContent();
        extra = extra + $('#feedback_id option:selected').text();

        tinyMCE.activeEditor.setContent(texto + extra);
    });

    $('[data-countdown]').each(function () {
        var $this = $(this);
        var finalDate = $(this).data('countdown');
        $this.countdown(finalDate, function (event) {
            $(this).html(event.strftime('%D días %H:%M:%S'));
        });
    });
});
