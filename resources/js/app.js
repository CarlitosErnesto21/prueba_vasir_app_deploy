import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import Swal from 'sweetalert2';
import PrimeVue from 'primevue/config';

import Aura from '@primeuix/themes/aura';
import { ToastService } from 'primevue';
import 'primeicons/primeicons.css';

// Importaciones de componentes de PrimeVue
import Toast from 'primevue/toast';
import Button from 'primevue/button';
import Toolbar from 'primevue/toolbar';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import FileUpload from 'primevue/fileupload';
import Textarea from 'primevue/textarea';
import InputNumber from 'primevue/inputnumber';
import Select from 'primevue/select';
import ConfirmationService from 'primevue/confirmationservice';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => title ? `${title} - ${appName}` : appName, // Se escribe así para que el título sea dinámico
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'), // Importa los componentes de las páginas para que Inertia los pueda resolver con el fin de que se puedan utilizar en la aplicación
        ),
    setup({ el, App, props, plugin }) {
        const app =  createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            //configuración global de SweetAlert2 
            app.config.globalProperties.$swal = Swal;
            app.use(PrimeVue, {
                theme: {
                    preset: Aura
                }
            });
            app.use(ToastService);
            app.use(ConfirmationService)
            // Definimos los componentes de PrimeVue
            app.component('Toast', Toast); // Componente de notificaciones
            app.component('Button', Button); // Componente de botones
            app.component('Toolbar', Toolbar); // Componente de barra de herramientas
            app.component('DataTable', DataTable); // Componente de tabla de datos
            app.component('Column', Column); // Componente de columna para tablas
            app.component('Dialog', Dialog); // Componente de diálogo modal
            app.component('InputText', InputText); // Componente de entrada de texto
            app.component('IconField', IconField); // Componente de campo de icono
            app.component('InputIcon', InputIcon);  // Componente de entrada con icono
            app.component('FileUpload', FileUpload); // Componente de carga de archivos
            app.component('Textarea', Textarea); // Componente de área de texto
            app.component('InputNumber', InputNumber); // Componente de entrada numérica
            app.component('Select', Select); // Componente de selección
            app.mount(el);
            return app;
    },
    progress: {
        color: '#4B5563',
    },
});

