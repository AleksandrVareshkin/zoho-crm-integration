import { createRouter, createWebHistory } from 'vue-router';
import ZohoForm from './components/ZohoForm.vue';
import AuthForm from './components/AuthForm.vue';

const routes = [
    {
        path: '/',
        component: ZohoForm,
    },
    {
        name: 'ZohoForm',
        path: '/form',
        component: ZohoForm,
    },
    {
        name: 'AuthForm',
        path: '/auth-form',
        component: AuthForm,
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
