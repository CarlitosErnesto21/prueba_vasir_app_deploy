<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section class="bg-white rounded-xl shadow-sm border border-blue-100 p-8">
        <header class="border-b border-blue-100 pb-6">
            <h2 class="text-2xl font-bold text-blue-900 mb-2">
                Información del Perfil
            </h2>

            <p class="text-blue-600 leading-relaxed">
                Actualiza la información de tu cuenta y dirección de correo electrónico.
            </p>
        </header>

        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="mt-8 space-y-8"
        >
            <div class="space-y-2">
                <InputLabel for="name" value="Nombre" class="text-blue-900 font-semibold" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full border-2 border-blue-200 rounded-lg px-4 py-3 text-blue-900 
                           focus:border-red-400 focus:ring-2 focus:ring-red-100 
                           hover:border-yellow-400 transition-all duration-300
                           bg-blue-50 focus:bg-white"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2 text-red-600 font-medium" :message="form.errors.name" />
            </div>

            <div class="space-y-2">
                <InputLabel for="email" value="Correo Electrónico" class="text-blue-900 font-semibold" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full border-2 border-blue-200 rounded-lg px-4 py-3 text-blue-900 
                           focus:border-red-400 focus:ring-2 focus:ring-red-100 
                           hover:border-yellow-400 transition-all duration-300
                           bg-blue-50 focus:bg-white"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2 text-red-600 font-medium" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null" 
                 class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-r-lg">
                <p class="text-blue-900 mb-3 font-medium">
                    Tu dirección de correo electrónico no está verificada.
                </p>
                <Link
                    :href="route('verification.send')"
                    method="post"
                    as="button"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-yellow-400 to-yellow-500 
                           text-blue-900 font-semibold rounded-lg shadow-md 
                           hover:from-yellow-500 hover:to-yellow-600 hover:shadow-lg
                           focus:outline-none focus:ring-2 focus:ring-yellow-300 focus:ring-offset-2 
                           transition-all duration-300 transform hover:scale-105"
                >
                    Haz clic aquí para reenviar el correo de verificación
                </Link>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-4 p-3 bg-green-100 border border-green-300 text-green-800 rounded-lg font-medium"
                >
                    Se ha enviado un nuevo enlace de verificación a tu correo electrónico.
                </div>
            </div>

            <div class="flex items-center gap-6 pt-6 border-t border-blue-100">
                <PrimaryButton 
                    :disabled="form.processing"
                    class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 
                           hover:from-red-500 hover:to-red-600 
                           text-white font-bold rounded-lg shadow-lg 
                           hover:shadow-xl transform hover:scale-105 
                           focus:ring-4 focus:ring-blue-200 
                           disabled:opacity-50 disabled:cursor-not-allowed 
                           transition-all duration-300"
                >
                    Guardar Cambios
                </PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out duration-500"
                    enter-from-class="opacity-0 translate-y-2"
                    leave-active-class="transition ease-in-out duration-300"
                    leave-to-class="opacity-0 translate-y-2"
                >
                    <div
                        v-if="form.recentlySuccessful"
                        class="flex items-center gap-2 px-4 py-2 bg-green-100 text-green-800 
                               rounded-lg border border-green-300 font-semibold shadow-sm"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Guardado exitosamente
                    </div>
                </Transition>
            </div>
        </form>
    </section>
</template>
