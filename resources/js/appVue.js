import { createApp } from 'vue';
import App from './components/AppComponent.vue';
import router from './router';
import Swal from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import Button from "primevue/button"
import Toast from 'primevue/toast';
import ToastService from 'primevue/toastservice';
import 'primeicons/primeicons.css';
import DatePicker from 'primevue/datepicker';

const app = createApp(App);
app.config.globalProperties.axios = axios;
app.use(router);
app.use(Swal);
app.use(PrimeVue, {
    theme: {
        preset: Aura
    }
});
app.use(ToastService);

app.component('Button', Button);
app.component('Toast', Toast);
app.component('DatePicker', DatePicker);

app.mount('#app');