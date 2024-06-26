import Vue from "vue";
import VueRouter from "vue-router";

import ZohoForm from "./components/ZohoForm.vue";
import AuthForm from "./components/AuthForm.vue";


Vue.use(VueRouter);

export default new VueRouter({
    mode: 'history',

    routes: [
        {
            path: '/',
            component: ZohoForm,
        },
        {
            path: '/form',
            component: ZohoForm,
        },
        {
            path: '/auth-form',
            component: AuthForm,
        }
    ]
});
