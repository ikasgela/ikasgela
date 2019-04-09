/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

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
});
