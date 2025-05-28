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

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
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

            <form @submit.prevent="submit">
                <div>
                    <InputLabel for="login-email" value="Correo" />
                    <TextInput
                        id="login-email"
                        type="email"
                        class="mt-1 block w-full"
                        v-model="form.email"
                        required
                        :autofocus="!isRegister" 
                        autocomplete="username"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-4">
                    <InputLabel for="login-password" value="Contraseña" />
                    <TextInput
                        id="login-password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="mt-4 block">
                    <label class="flex items-center">
                        <Checkbox name="remember" v-model:checked="form.remember" />
                        <span class="ms-2 text-sm text-gray-600">Acuérdate de mí.</span>
                    </label>
                </div>

                <div class="mt-4 flex items-center justify-end">
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="rounded-md text-sm text-red-600 underline hover:text-red-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        ¿Olvidaste tu contraseña?
                    </Link>

                    <PrimaryButton
                        class="ms-4"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing">
                        Log in
                    </PrimaryButton>
                </div>
            </form>
        </template>
        <template #register>
            <Register :is-register="isRegister" />
        </template>
    </GuestLayout>
</template>
