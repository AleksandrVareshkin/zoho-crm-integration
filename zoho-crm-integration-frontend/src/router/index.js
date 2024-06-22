import { createRouter, createWebHistory } from 'vue-router';
import DealForm from '../components/DealForm.vue';

const routes = [
    {
        path: '/',
        name: 'DealForm',
        component: DealForm,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
