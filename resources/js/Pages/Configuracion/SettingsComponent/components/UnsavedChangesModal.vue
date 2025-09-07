<template>
    <Dialog 
        v-model:visible="isVisible" 
        :header="title" 
        :modal="true" 
        :style="{ width: '400px' }" 
        :closable="false"
    >
        <div class="flex items-center gap-3">
            <FontAwesomeIcon :icon="faExclamationTriangle" class="h-8 w-8 text-red-500" />
            <div class="flex flex-col">
                <span>{{ message }}</span>
                <span v-if="subtitle" class="text-red-600 text-sm font-medium mt-1">{{ subtitle }}</span>
            </div>
        </div>
        <template #footer>
            <div class="flex justify-center gap-3 w-full">
                <button 
                    type="button" 
                    class="bg-white hover:bg-green-100 text-green-600 border border-green-600 px-6 py-2 rounded-md transition-all duration-200 ease-in-out flex items-center gap-2"
                    @click="continueEditing"
                >
                    <FontAwesomeIcon :icon="faPencil" class="h-4" />
                    <span>{{ continueText }}</span>
                </button>
                <button 
                    type="button" 
                    class="bg-red-500 hover:bg-red-700 text-white border-none px-6 py-2 rounded-md transition-all duration-200 ease-in-out flex items-center gap-2"
                    @click="exitWithoutSaving"
                >
                    <FontAwesomeIcon :icon="faSignOut" class="h-4" />
                    <span>{{ exitText }}</span>
                </button>
            </div>
        </template>
    </Dialog>
</template>

<script setup>
import { computed } from 'vue'
import Dialog from 'primevue/dialog'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faExclamationTriangle, faPencil, faSignOut } from '@fortawesome/free-solid-svg-icons'

const props = defineProps({
    visible: {
        type: Boolean,
        default: false
    },
    title: {
        type: String,
        default: 'Cambios sin guardar'
    },
    message: {
        type: String,
        default: '¡Tienes información sin guardar!'
    },
    subtitle: {
        type: String,
        default: '¿Deseas salir sin guardar?'
    },
    continueText: {
        type: String,
        default: 'Continuar'
    },
    exitText: {
        type: String,
        default: 'Salir sin guardar'
    }
})

const emit = defineEmits(['update:visible', 'continue-editing', 'exit-without-saving'])

const isVisible = computed({
    get() {
        return props.visible
    },
    set(value) {
        emit('update:visible', value)
    }
})

const continueEditing = () => {
    emit('continue-editing')
    isVisible.value = false
}

const exitWithoutSaving = () => {
    emit('exit-without-saving')
    isVisible.value = false
}
</script>
