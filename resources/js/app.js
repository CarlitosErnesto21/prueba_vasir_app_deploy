import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import PrimeVue from 'primevue/config';
import ToastService from 'primevue/toastservice';

createInertiaApp({
  resolve: name => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    const vueApp = createApp({ render: () => h(App, props) });
    vueApp.use(plugin);
    vueApp.use(PrimeVue);
    vueApp.use(ToastService); // <-- ¡Esto es lo importante!
    vueApp.mount(el);
  },
});