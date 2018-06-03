require('../../../../../../resources/assets/js/bootstrap');
require('../../../../../../vendor/acciocms/core/src/resources/assets/js/base-components');
require('vue-multiselect/dist/vue-multiselect.min.css');

import Vue from 'vue'
import VueRouter from 'vue-router';
import { store } from './store';
import Base from '../../views/backend/Base.vue';
Vue.use(VueRouter);

const routes = [
    { path: globalProjectDirectory+'/:adminPrefix/:lang/plugins/accio/post-position-manager', component: Base },
];

const router = new VueRouter({
    mode: 'history',
    routes: routes
});

const app = new Vue({
    el: '#app',
    router,
    store,
});
