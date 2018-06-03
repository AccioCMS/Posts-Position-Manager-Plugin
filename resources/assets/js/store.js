import Vuex from 'vuex'
import Vue from 'vue'
import Bootstrap from '../../../../../../vendor/acciocms/core/src/resources/assets/js/bootstrap-vuex';

Vue.use(Vuex);

export const store = new Vuex.Store({
    modules: {
        Bootstrap
    },
    state: {},
    getters: {},
    mutations: {}
});