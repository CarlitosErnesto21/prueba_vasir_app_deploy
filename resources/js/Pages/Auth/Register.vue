<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    isRegister: Boolean
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <div>
        <Head title="Register" />

        <div class="flex justify-center mb-6">
            <Link href="/">
                <img src="../../../../imagenes/logo.png" alt="Logo" class="h-10 w-auto cursor-pointer" title="Ir a catálogo"/>
            </Link>
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="register-name" value="Nombre Completo:" class="font-bold"/>
                <TextInput
                    id="register-name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    :autofocus="isRegister"
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="register-email" value="Correo Electrónico:" class="font-bold"/>
                <TextInput
                    id="register-email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="register-password" value="Contraseña:" class="font-bold"/>
                <TextInput
                    id="register-password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="register-password_confirmation"
                    value="Confirmar Contraseña:"
                    class="font-bold"
                />
                <TextInput
                    id="register-password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton
                    class="ms-4 bg-red-500 hover:bg-red-600 focus:bg-red-700 text-white"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Registrarse
                </PrimaryButton>
            </div>
        </form>
    </div>
</template>
