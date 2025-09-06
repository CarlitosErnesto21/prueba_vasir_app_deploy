<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

// Estado para mostrar/ocultar contraseña
const showPassword = ref(false);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="bg-white">
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <div class="bg-red-100 rounded-full p-2">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-red-900 mb-2">⚠️ Zona de Peligro</h3>
                    <p class="text-red-800 text-sm leading-relaxed mb-3">
                        Una vez eliminada tu cuenta, todos los recursos y datos serán eliminados permanentemente. 
                        Descarga cualquier información importante antes de proceder.
                    </p>
                    <div class="bg-white bg-opacity-60 rounded-md p-3 border-l-2 border-red-500">
                        <p class="text-red-900 font-medium text-xs">
                            Esta acción no se puede deshacer. Todos tus datos se perderán permanentemente.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center">
            <DangerButton 
                @click="confirmUserDeletion"
                class="inline-flex items-center px-6 py-2.5 bg-red-600 text-white text-sm font-medium 
                       rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 
                       transition-colors"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Eliminar Mi Cuenta
            </DangerButton>
        </div>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="bg-white rounded-lg shadow-xl max-w-md mx-auto">
                <!-- Header del modal -->
                <div class="bg-red-600 rounded-t-lg px-6 py-4">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-white">Confirmar Eliminación</h2>
                            <p class="text-red-100 text-sm">Esta acción es irreversible</p>
                        </div>
                    </div>
                </div>

                <!-- Contenido del modal -->
                <div class="p-6">
                    <div class="bg-red-50 border-l-2 border-red-400 p-4 mb-6 rounded-r-md">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="w-4 h-4 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-red-800 text-sm leading-relaxed">
                                    Una vez eliminada tu cuenta, todos los recursos y datos serán eliminados permanentemente. 
                                    Ingresa tu contraseña para confirmar esta acción.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <InputLabel for="password" value="Confirma con tu contraseña:" class="text-gray-700 font-medium text-sm" />

                        <div class="relative">
                            <TextInput
                                id="password"
                                ref="passwordInput"
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                class="w-full border border-red-300 rounded-lg px-3 py-2.5 pr-10 text-gray-900 text-sm
                                       focus:border-red-500 focus:ring-2 focus:ring-red-100 
                                       hover:border-red-400 transition-colors
                                       bg-red-50 focus:bg-white placeholder-gray-500"
                                placeholder="Tu contraseña actual"
                                @keyup.enter="deleteUser"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-red-400 hover:text-red-600 transition-colors"
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

                        <InputError :message="form.errors.password" class="text-red-600 text-sm" />
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 mt-6">
                        <SecondaryButton 
                            @click="closeModal"
                            class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-md 
                                   hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 
                                   transition-colors border border-gray-300"
                        >
                            Cancelar
                        </SecondaryButton>

                        <DangerButton
                            :class="{ 'opacity-50': form.processing }"
                            :disabled="form.processing"
                            @click="deleteUser"
                            class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md 
                                   hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 
                                   disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                        >
                            {{ form.processing ? 'Eliminando...' : 'Sí, Eliminar' }}
                        </DangerButton>
                    </div>
                </div>
            </div>
        </Modal>
    </section>
</template>
