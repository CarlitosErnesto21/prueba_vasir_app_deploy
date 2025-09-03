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
    <section class="bg-white rounded-xl shadow-sm border border-red-100 p-8">
        <header class="border-b border-red-100 pb-6">
            <h2 class="text-2xl font-bold text-red-700 mb-2">
                Eliminar Cuenta
            </h2>

            <p class="text-red-600 leading-relaxed">
                Una vez que tu cuenta sea eliminada, todos sus recursos y datos serán eliminados permanentemente. 
                Antes de eliminar tu cuenta, por favor descarga cualquier dato o información que desees conservar.
            </p>
        </header>

        <div class="mt-8">
            <DangerButton 
                @click="confirmUserDeletion"
                class="px-8 py-3 bg-gradient-to-r from-red-600 to-red-700 
                       hover:from-red-700 hover:to-red-800 
                       text-white font-bold rounded-lg shadow-lg 
                       hover:shadow-xl transform hover:scale-105 
                       focus:ring-4 focus:ring-red-200 
                       transition-all duration-300"
            >
                Eliminar Cuenta
            </DangerButton>
        </div>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-8 bg-white rounded-lg">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-xl font-bold text-red-700">
                            ¿Estás seguro de que quieres eliminar tu cuenta?
                        </h2>
                    </div>
                </div>

                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-r-lg">
                    <p class="text-red-800 font-medium">
                        Una vez que tu cuenta sea eliminada, todos sus recursos y datos serán eliminados permanentemente. 
                        Por favor ingresa tu contraseña para confirmar que deseas eliminar permanentemente tu cuenta.
                    </p>
                </div>

                <div class="mb-6">
                    <InputLabel
                        for="password"
                        value="Contraseña"
                        class="text-blue-900 font-semibold mb-2"
                    />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="block w-full border-2 border-red-200 rounded-lg px-4 py-3 text-blue-900 
                               focus:border-red-400 focus:ring-2 focus:ring-red-100 
                               hover:border-yellow-400 transition-all duration-300
                               bg-red-50 focus:bg-white"
                        placeholder="Ingresa tu contraseña"
                        @keyup.enter="deleteUser"
                    />

                    <InputError :message="form.errors.password" class="mt-2 text-red-600 font-medium" />
                </div>

                <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
                    <SecondaryButton 
                        @click="closeModal"
                        class="px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg 
                               hover:bg-gray-200 focus:ring-4 focus:ring-gray-100 
                               transition-all duration-300"
                    >
                        Cancelar
                    </SecondaryButton>

                    <DangerButton
                        :class="{ 'opacity-50': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                        class="px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 
                               hover:from-red-700 hover:to-red-800 
                               text-white font-bold rounded-lg shadow-lg 
                               hover:shadow-xl transform hover:scale-105 
                               focus:ring-4 focus:ring-red-200 
                               disabled:opacity-50 disabled:cursor-not-allowed 
                               transition-all duration-300"
                    >
                        Eliminar Cuenta
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
