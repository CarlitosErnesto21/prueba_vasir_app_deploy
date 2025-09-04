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
            class="space-y-6"
        >
            <!-- Campos en grid responsivo -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Campo Nombre -->
                <div class="space-y-2">
                    <InputLabel for="name" value="Nombre Completo" class="text-gray-700 font-medium text-sm" />
                    <div class="relative">
                        <TextInput
                            id="name"
                            type="text"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-gray-900 text-sm
                                   focus:border-red-500 focus:ring-2 focus:ring-red-100 
                                   hover:border-gray-400 transition-colors
                                   bg-white placeholder-gray-500"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="Tu nombre completo"
                        />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                    </div>
                    <InputError class="text-red-600 text-sm" :message="form.errors.name" />
                </div>

                <!-- Campo Email -->
                <div class="space-y-2">
                    <InputLabel for="email" value="Correo Electrónico" class="text-gray-700 font-medium text-sm" />
                    <div class="relative">
                        <TextInput
                            id="email"
                            type="email"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-gray-900 text-sm
                                   focus:border-red-500 focus:ring-2 focus:ring-red-100 
                                   hover:border-gray-400 transition-colors
                                   bg-white placeholder-gray-500"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            placeholder="tu@email.com"
                        />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </div>
                    </div>
                    <InputError class="text-red-600 text-sm" :message="form.errors.email" />
                </div>
            </div>

            <!-- Alerta de verificación de email -->
            <div v-if="mustVerifyEmail && user.email_verified_at === null" 
                 class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-yellow-800 text-sm font-medium mb-3">
                            Tu dirección de correo electrónico no está verificada.
                        </p>
                        <Link
                            :href="route('verification.send')"
                            method="post"
                            as="button"
                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium 
                                   rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 
                                   transition-colors"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 7.89a2 2 0 002.82 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Reenviar verificación
                        </Link>

                        <div
                            v-show="status === 'verification-link-sent'"
                            class="mt-3 p-3 bg-green-50 border border-green-200 text-green-800 rounded-md text-sm"
                        >
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Enlace de verificación enviado a tu correo.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 pt-6 border-t border-gray-200">
                <PrimaryButton 
                    :disabled="form.processing"
                    class="inline-flex items-center px-6 py-2.5 bg-red-600 text-white text-sm font-medium 
                           rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 
                           disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ form.processing ? 'Guardando...' : 'Guardar Cambios' }}
                </PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out duration-300"
                    enter-from-class="opacity-0 translate-y-1"
                    leave-active-class="transition ease-in-out duration-200"
                    leave-to-class="opacity-0 translate-y-1"
                >
                    <div
                        v-if="form.recentlySuccessful"
                        class="flex items-center gap-2 px-4 py-2 bg-green-50 text-green-800 
                               rounded-md border border-green-200 text-sm"
                    >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Información actualizada correctamente
                    </div>
                </Transition>
            </div>
        </form>
    </section>
</template>
