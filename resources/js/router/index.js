import { createRouter, createWebHistory } from 'vue-router';
import Home from '../components/HomeComponent.vue';
import Producto from '../components/ProductoComponent.vue';

const routes = [
    { path: '/home', component: Home },
    { path: '/producto', component: Producto }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;