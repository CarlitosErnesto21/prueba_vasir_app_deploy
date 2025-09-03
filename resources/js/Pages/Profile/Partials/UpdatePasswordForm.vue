<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section class="bg-white">
        <form @submit.prevent="updatePassword" class="space-y-8">
            <!-- Contraseña Actual -->
            <div class="space-y-3">
                <InputLabel for="current_password" value="Contraseña Actual" class="text-red-900 font-bold text-sm" />

                <div class="relative">
                    <TextInput
                        id="current_password"
                        ref="currentPasswordInput"
                        v-model="form.current_password"
                        type="password"
                        class="w-full border-2 border-red-200 rounded-xl px-4 py-4 text-red-900 font-medium
                               focus:border-red-500 focus:ring-4 focus:ring-red-100 
                               hover:border-red-300 transition-all duration-300
                               bg-red-50 focus:bg-white shadow-sm"
                        autocomplete="current-password"
                        placeholder="Ingresa tu contraseña actual"
                    />
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                </div>

                <InputError
                    :message="form.errors.current_password"
                    class="text-red-600 font-semibold"
                />
            </div>

            <!-- Nueva Contraseña -->
            <div class="space-y-3">
                <InputLabel for="password" value="Nueva Contraseña" class="text-red-900 font-bold text-sm" />

                <div class="relative">
                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="w-full border-2 border-red-200 rounded-xl px-4 py-4 text-red-900 font-medium
                               focus:border-red-500 focus:ring-4 focus:ring-red-100 
                               hover:border-red-300 transition-all duration-300
                               bg-red-50 focus:bg-white shadow-sm"
                        autocomplete="new-password"
                        placeholder="Crea una contraseña segura"
                    />
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                </div>

                <InputError :message="form.errors.password" class="text-red-600 font-semibold" />
                
                <!-- Indicador de fortaleza de contraseña -->
                <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                    <h4 class="text-sm font-bold text-red-900 mb-2">Requisitos de contraseña:</h4>
                    <ul class="text-xs text-red-700 space-y-1">
                        <li class="flex items-center">
                            <span class="w-1.5 h-1.5 bg-red-600 rounded-full mr-2"></span>
                            Mínimo 8 caracteres
                        </li>
                        <li class="flex items-center">
                            <span class="w-1.5 h-1.5 bg-red-600 rounded-full mr-2"></span>
                            Al menos una mayúscula
                        </li>
                        <li class="flex items-center">
                            <span class="w-1.5 h-1.5 bg-red-600 rounded-full mr-2"></span>
                            Al menos un número
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Confirmar Contraseña -->
            <div class="space-y-3">
                <InputLabel
                    for="password_confirmation"
                    value="Confirmar Nueva Contraseña"
                    class="text-red-900 font-bold text-sm"
                />

                <div class="relative">
                    <TextInput
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        class="w-full border-2 border-red-200 rounded-xl px-4 py-4 text-red-900 font-medium
                               focus:border-red-500 focus:ring-4 focus:ring-red-100 
                               hover:border-red-300 transition-all duration-300
                               bg-red-50 focus:bg-white shadow-sm"
                        autocomplete="new-password"
                        placeholder="Confirma tu nueva contraseña"
                    />
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>

                <InputError
                    :message="form.errors.password_confirmation"
                    class="text-red-600 font-semibold"
                />
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    {{ form.processing ? 'Actualizando...' : 'Actualizar Contraseña' }}
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
                        ¡Contraseña actualizada exitosamente!
                    </div>
                </Transition>
            </div>
        </form>
    </section>
</template>
