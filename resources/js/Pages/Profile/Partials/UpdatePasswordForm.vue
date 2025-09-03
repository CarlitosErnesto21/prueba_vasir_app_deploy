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
    <section class="bg-white rounded-xl shadow-sm border border-blue-100 p-8">
        <header class="border-b border-blue-100 pb-6">
            <h2 class="text-2xl font-bold text-blue-900 mb-2">
                Actualizar Contraseña
            </h2>

            <p class="text-blue-600 leading-relaxed">
                Asegúrate de que tu cuenta esté usando una contraseña larga y aleatoria para mantenerte seguro.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-8 space-y-8">
            <div class="space-y-2">
                <InputLabel for="current_password" value="Contraseña Actual" class="text-blue-900 font-semibold" />

                <TextInput
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="mt-1 block w-full border-2 border-blue-200 rounded-lg px-4 py-3 text-blue-900 
                           focus:border-red-400 focus:ring-2 focus:ring-red-100 
                           hover:border-yellow-400 transition-all duration-300
                           bg-blue-50 focus:bg-white"
                    autocomplete="current-password"
                />

                <InputError
                    :message="form.errors.current_password"
                    class="mt-2 text-red-600 font-medium"
                />
            </div>

            <div class="space-y-2">
                <InputLabel for="password" value="Nueva Contraseña" class="text-blue-900 font-semibold" />

                <TextInput
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full border-2 border-blue-200 rounded-lg px-4 py-3 text-blue-900 
                           focus:border-red-400 focus:ring-2 focus:ring-red-100 
                           hover:border-yellow-400 transition-all duration-300
                           bg-blue-50 focus:bg-white"
                    autocomplete="new-password"
                />

                <InputError :message="form.errors.password" class="mt-2 text-red-600 font-medium" />
            </div>

            <div class="space-y-2">
                <InputLabel
                    for="password_confirmation"
                    value="Confirmar Contraseña"
                    class="text-blue-900 font-semibold"
                />

                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full border-2 border-blue-200 rounded-lg px-4 py-3 text-blue-900 
                           focus:border-red-400 focus:ring-2 focus:ring-red-100 
                           hover:border-yellow-400 transition-all duration-300
                           bg-blue-50 focus:bg-white"
                    autocomplete="new-password"
                />

                <InputError
                    :message="form.errors.password_confirmation"
                    class="mt-2 text-red-600 font-medium"
                />
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
                        Contraseña actualizada exitosamente
                    </div>
                </Transition>
            </div>
        </form>
    </section>
</template>
