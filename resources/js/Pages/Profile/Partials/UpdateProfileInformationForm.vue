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
    <section class="bg-white">
        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="space-y-8"
        >
            <!-- Campo Nombre -->
            <div class="space-y-3">
                <InputLabel for="name" value="Nombre Completo" class="text-red-900 font-bold text-sm" />

                <div class="relative">
                    <TextInput
                        id="name"
                        type="text"
                        class="w-full border-2 border-red-200 rounded-xl px-4 py-4 text-red-900 font-medium
                               focus:border-red-500 focus:ring-4 focus:ring-red-100 
                               hover:border-red-300 transition-all duration-300
                               bg-red-50 focus:bg-white shadow-sm"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Ingresa tu nombre completo"
                    />
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                </div>

                <InputError class="text-red-600 font-semibold" :message="form.errors.name" />
            </div>

            <!-- Campo Email -->
            <div class="space-y-3">
                <InputLabel for="email" value="Correo Electrónico" class="text-red-900 font-bold text-sm" />

                <div class="relative">
                    <TextInput
                        id="email"
                        type="email"
                        class="w-full border-2 border-red-200 rounded-xl px-4 py-4 text-red-900 font-medium
                               focus:border-red-500 focus:ring-4 focus:ring-red-100 
                               hover:border-red-300 transition-all duration-300
                               bg-red-50 focus:bg-white shadow-sm"
                        v-model="form.email"
                        required
                        autocomplete="username"
                        placeholder="tu-email@ejemplo.com"
                    />
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                        </svg>
                    </div>
                </div>

                <InputError class="text-red-600 font-semibold" :message="form.errors.email" />
            </div>

            <!-- Alerta de verificación de email -->
            <div v-if="mustVerifyEmail && user.email_verified_at === null" 
                 class="bg-gradient-to-r from-amber-50 to-yellow-50 border-l-4 border-amber-400 p-6 rounded-r-xl shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-amber-800 mb-4 font-semibold">
                            Tu dirección de correo electrónico no está verificada.
                        </p>
                        <Link
                            :href="route('verification.send')"
                            method="post"
                            as="button"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-amber-500 to-yellow-500 
                                   text-white font-bold rounded-lg shadow-lg 
                                   hover:from-amber-600 hover:to-yellow-600 hover:shadow-xl
                                   focus:outline-none focus:ring-4 focus:ring-amber-200 
                                   transition-all duration-300 transform hover:scale-105"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 7.89a2 2 0 002.82 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Reenviar verificación por email
                        </Link>

                        <div
                            v-show="status === 'verification-link-sent'"
                            class="mt-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg font-semibold shadow-sm"
                        >
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Se ha enviado un nuevo enlace de verificación a tu correo electrónico.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="flex items-center gap-6 pt-8 border-t border-red-100">
                <PrimaryButton 
                    :disabled="form.processing"
                    class="px-8 py-4 bg-gradient-to-r from-red-600 to-red-700 
                           hover:from-red-700 hover:to-red-800 
                           text-white font-bold rounded-xl shadow-lg 
                           hover:shadow-xl transform hover:scale-105 
                           focus:ring-4 focus:ring-red-200 
                           disabled:opacity-50 disabled:cursor-not-allowed 
                           transition-all duration-300"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ form.processing ? 'Guardando...' : 'Guardar Cambios' }}
                </PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out duration-500"
                    enter-from-class="opacity-0 translate-y-2"
                    leave-active-class="transition ease-in-out duration-300"
                    leave-to-class="opacity-0 translate-y-2"
                >
                    <div
                        v-if="form.recentlySuccessful"
                        class="flex items-center gap-3 px-6 py-3 bg-green-100 text-green-800 
                               rounded-xl border border-green-300 font-bold shadow-sm"
                    >
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        ¡Información actualizada exitosamente!
                    </div>
                </Transition>
            </div>
        </form>
    </section>
</template>
