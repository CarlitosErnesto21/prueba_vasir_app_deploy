<script setup>
import { ref, computed, watch } from 'vue' // <-- AGREGA computed aquí
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Register from './Register.vue';
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

// Estado para mostrar/ocultar contraseña
const showPassword = ref(false);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
        onSuccess: () => {
            // Verificar si hay una reserva pendiente ACTIVA de esta sesión
            const reservaPendiente = sessionStorage.getItem('tour_reserva_pendiente');
            const sessionActiva = sessionStorage.getItem('reserva_session_activa');
            
            if (reservaPendiente && sessionActiva === 'true') {
                const tourInfo = JSON.parse(reservaPendiente);
                // NO limpiar aquí - dejar que la vista de destino lo maneje
                // Redirigir a la vista original
                router.visit(tourInfo.returnUrl);
                return;
            }
            
            // Verificar si hay un producto pendiente ACTIVO de esta sesión
            const productoPendiente = sessionStorage.getItem('producto_compra_pendiente');
            const sessionActivaProducto = sessionStorage.getItem('compra_session_activa');
            
            if (productoPendiente && sessionActivaProducto === 'true') {
                const productoInfo = JSON.parse(productoPendiente);
                // NO limpiar aquí - dejar que la vista de destino lo maneje
                // Redirigir a la vista original
                router.visit(productoInfo.returnUrl);
                return;
            }
            
            // Si no hay reserva o compra pendiente activa, limpiar cualquier dato residual
            if (!sessionActiva || sessionActiva !== 'true') {
                sessionStorage.removeItem('tour_reserva_pendiente');
                sessionStorage.removeItem('reserva_session_activa');
            }
            
            if (!sessionActivaProducto || sessionActivaProducto !== 'true') {
                sessionStorage.removeItem('producto_compra_pendiente');
                sessionStorage.removeItem('compra_session_activa');
            }
        }
    });
};

const page = usePage();
const isRegister = ref(window.location.pathname.startsWith('/register'));

// Actualiza el título cuando cambias de formulario
watch(isRegister, (value) => {
    document.title = value ? 'Register - VASIR' : 'Log in - VASIR';
});

const toggleForm = () => {
    if (isRegister.value) {
        window.history.pushState({}, '', '/login');
        isRegister.value = false;
    } else {
        window.history.pushState({}, '', '/register');
        isRegister.value = true;
    }
};
</script>

<template>
    <GuestLayout :is-register="isRegister" :toggle-form="toggleForm">
        <template #login>
            <Head title="Log in" />

            <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
                {{ status }}
            </div>
            <div class="flex justify-center mb-6">
                <Link href="/">
                    <img src="../../../../imagenes/logo.png" alt="Logo" class="h-10 w-auto cursor-pointer" title="Ir a catálogo"/>
                </Link>
            </div>

            <form @submit.prevent="submit">
                <div>
                    <InputLabel for="login-email" value="Correo Electrónico:" />
                    <TextInput
                        id="login-email"
                        type="email"
                        class="mt-1 block w-full max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg xl:max-w-xl 2xl:max-w-2xl"
                        v-model="form.email"
                        required
                        :autofocus="!isRegister" 
                        autocomplete="username"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-4">
                    <InputLabel for="login-password" value="Contraseña" />
                    <div class="relative">
                        <TextInput
                            id="login-password"
                            :type="showPassword ? 'text' : 'password'"
                            class="mt-1 block w-full max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg xl:max-w-xl 2xl:max-w-2xl pr-10"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors"
                        >
                            <svg v-if="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                            </svg>
                        </button>
                    </div>
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="mt-4 block">
                    <label class="flex items-center">
                        <Checkbox name="remember" v-model:checked="form.remember" />
                        <span class="ms-2 text-sm text-red-600">Acuérdate de mí.</span>
                    </label>
                </div>

                <div class="mt-4 flex items-center justify-center">
                    <PrimaryButton
                        class="ms-4 bg-gradient-to-r from-red-500 to-red-400 hover:bg-red-600 focus:bg-red-700 text-white"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing">
                        Iniciar Sesión
                    </PrimaryButton>
                </div>
                <div class="mt-2 flex items-center justify-center">
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="rounded-md text-sm text-red-600 underline hover:text-red-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        ¿Olvidaste tu contraseña?
                    </Link>
                </div>
            </form>
        </template>
        <template #register>
            <Register :is-register="isRegister" />
        </template>
    </GuestLayout>
</template>
