/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
// Include Vuetify
import Vuetify from 'vuetify';
// Include Vuex
import Vuex from 'vuex';
// Include VueRouter
import VueRouter from 'vue-router';
import swal from 'sweetalert2';

window.Vue = require('vue').default;
window.Swal = swal;

Vue.use(Vuetify);
Vue.use(Vuex);
Vue.use(VueRouter);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);

Vue.component('landing-page', require('./components/ClientFacing/LandingPage.vue').default);
Vue.component('product-details', require('./components/ClientFacing/ProductDetails.vue').default);
Vue.component('check-out-component', require('./components/ClientFacing/CheckOutComponent.vue').default);

Vue.component('menu-bar-component', require('./components/Shared/MenuBarComponent.vue').default);
Vue.component('footer-component', require('./components/Shared/FooterBarComponent.vue').default);

Vue.component('user-component', require('./components/AdminFacing/UserComponent.vue').default);
Vue.component('product-component', require('./components/AdminFacing/ProductComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    vuetify: new Vuetify(),
});
