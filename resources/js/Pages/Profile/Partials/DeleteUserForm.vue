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
        <div class="bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 rounded-xl p-6 mb-8">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <div class="bg-red-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-red-900 mb-2">⚠️ Zona de Peligro</h3>
                    <p class="text-red-800 leading-relaxed mb-4">
                        Una vez que tu cuenta sea eliminada, todos sus recursos y datos serán eliminados permanentemente. 
                        Antes de eliminar tu cuenta, por favor descarga cualquier dato o información que desees conservar.
                    </p>
                    <div class="bg-white bg-opacity-50 rounded-lg p-4 border-l-4 border-red-500">
                        <p class="text-red-900 font-semibold text-sm">
                            Esta acción no se puede deshacer. Todos tus datos se perderán para siempre.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center">
            <DangerButton 
                @click="confirmUserDeletion"
                class="px-8 py-4 bg-gradient-to-r from-red-600 to-red-700 
                       hover:from-red-700 hover:to-red-800 
                       text-white font-bold rounded-xl shadow-lg 
                       hover:shadow-xl transform hover:scale-105 
                       focus:ring-4 focus:ring-red-200 
                       transition-all duration-300"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Eliminar Mi Cuenta Permanentemente
            </DangerButton>
        </div>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md mx-auto">
                <!-- Header del modal -->
                <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-t-2xl px-8 py-6">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0 w-16 h-16 bg-white bg-opacity-20 backdrop-blur-sm rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">
                                Confirmar Eliminación
                            </h2>
                            <p class="text-red-100 text-sm">
                                Esta acción es irreversible
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Contenido del modal -->
                <div class="p-8">
                    <div class="bg-red-50 border-l-4 border-red-400 p-6 mb-6 rounded-r-lg">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-red-800 font-semibold leading-relaxed">
                                    Una vez que tu cuenta sea eliminada, todos sus recursos y datos serán eliminados permanentemente. 
                                    Por favor ingresa tu contraseña para confirmar que deseas eliminar permanentemente tu cuenta.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <InputLabel
                            for="password"
                            value="Confirma con tu contraseña:"
                            class="text-red-900 font-bold text-sm"
                        />

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
                                placeholder="Ingresa tu contraseña para confirmar"
                                @keyup.enter="deleteUser"
                            />
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                        </div>

                        <InputError :message="form.errors.password" class="text-red-600 font-semibold" />
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex justify-end gap-4 pt-8 border-t border-red-100 mt-8">
                        <SecondaryButton 
                            @click="closeModal"
                            class="px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl 
                                   hover:bg-gray-200 focus:ring-4 focus:ring-gray-100 
                                   transition-all duration-300 border border-gray-300"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancelar
                        </SecondaryButton>

                        <DangerButton
                            :class="{ 'opacity-50': form.processing }"
                            :disabled="form.processing"
                            @click="deleteUser"
                            class="px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 
                                   hover:from-red-700 hover:to-red-800 
                                   text-white font-bold rounded-xl shadow-lg 
                                   hover:shadow-xl transform hover:scale-105 
                                   focus:ring-4 focus:ring-red-200 
                                   disabled:opacity-50 disabled:cursor-not-allowed 
                                   transition-all duration-300"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            {{ form.processing ? 'Eliminando...' : 'Sí, Eliminar Cuenta' }}
                        </DangerButton>
                    </div>
                </div>
            </div>
        </Modal>
    </section>
</template>
