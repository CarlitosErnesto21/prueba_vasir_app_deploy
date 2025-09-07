<template>
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-2 sm:px-4 lg:px-6 py-2 sm:py-3 lg:py-4">
            <h1 class="text-sm sm:text-lg lg:text-2xl font-bold text-white flex items-center">
                <span class="text-sm sm:text-lg lg:text-2xl mr-1 sm:mr-2 lg:mr-3">🔐</span>
                <span class="hidden sm:inline">Seguridad y Autenticación</span>
                <span class="sm:hidden">Seguridad</span>
            </h1>
            <p class="text-red-100 mt-0.5 sm:mt-1 lg:mt-2 text-xs sm:text-sm lg:text-base hidden sm:block">Configuraciones de seguridad y control de acceso</p>
        </div>

        <!-- Content -->
        <div class="p-2 sm:p-4 lg:p-6">
            <div class="space-y-2 sm:space-y-4 lg:space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-2 sm:gap-4 lg:gap-6">
                    <div class="space-y-2 sm:space-y-3 lg:space-y-4">
                        <div class="flex items-center p-2 sm:p-3 lg:p-4 bg-red-50 rounded-lg border border-red-200">
                            <input 
                                v-model="currentFormData.requireEmailVerification"
                                type="checkbox"
                                class="h-3 w-3 sm:h-4 sm:w-4 lg:h-5 lg:w-5 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                            />
                            <label class="ml-1.5 sm:ml-2 lg:ml-3 text-xs sm:text-sm font-medium text-gray-700">
                                Requerir verificación de email
                            </label>
                        </div>
                        <div class="flex items-center p-2 sm:p-3 lg:p-4 bg-red-50 rounded-lg border border-red-200">
                            <input 
                                v-model="currentFormData.enableTwoFactor"
                                type="checkbox"
                                class="h-3 w-3 sm:h-4 sm:w-4 lg:h-5 lg:w-5 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                            />
                            <label class="ml-1.5 sm:ml-2 lg:ml-3 text-xs sm:text-sm font-medium text-gray-700">
                                Habilitar autenticación de dos factores
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                            Tiempo de sesión (min)
                        </label>
                        <input 
                            v-model.number="currentFormData.sessionTimeout"
                            type="number"
                            min="5"
                            max="1440"
                            class="w-full px-2 sm:px-3 lg:px-4 py-1.5 sm:py-2 text-xs sm:text-sm lg:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent shadow-sm"
                        />
                        <p class="text-xs text-gray-500 mt-0.5 sm:mt-1 hidden sm:block">Entre 5 y 1440 minutos (24 horas)</p>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex flex-col space-y-1 sm:flex-row sm:justify-end sm:space-y-0 sm:space-x-2 lg:space-x-4 mt-3 sm:mt-6 lg:mt-8 pt-2 sm:pt-4 lg:pt-6 border-t border-gray-200">
                <button 
                    @click="handleReset"
                    :disabled="!canReset || isSaving"
                    class="w-full sm:w-auto px-2 sm:px-4 lg:px-6 py-1.5 sm:py-2 text-xs sm:text-sm lg:text-base border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed transition-colors duration-200"
                >
                    Restablecer
                </button>
                <button 
                    @click="handleSave"
                    :disabled="!canSave || isSaving"
                    class="w-full sm:w-auto px-2 sm:px-4 lg:px-6 py-1.5 sm:py-2 text-xs sm:text-sm lg:text-base bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors duration-200 flex items-center justify-center"
                >
                    <FontAwesomeIcon 
                        v-if="isSaving" 
                        :icon="faSpinner" 
                        class="animate-spin mr-1 sm:mr-2" 
                    />
                    {{ isSaving ? 'Guardando...' : 'Guardar' }}
                </button>
            </div>
        </div>

        <!-- Modal de cambios sin guardar -->
        <UnsavedChangesModal
            v-model:visible="showUnsavedModal"
            @continue-editing="cancelExit"
            @exit-without-saving="confirmExit"
        />
    </div>
</template>

<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faSpinner } from '@fortawesome/free-solid-svg-icons'
import { watch } from 'vue'
import { useUnsavedChanges } from './components/useUnsavedChanges'
import UnsavedChangesModal from './components/UnsavedChangesModal.vue'

const props = defineProps({
    settings: {
        type: Object,
        required: true
    },
    isSaving: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['save', 'reset', 'unsaved-changes'])

// Usar el composable para manejar cambios no guardados
const saveCallback = async (formData) => {
    emit('save', formData)
}

const {
    hasUnsavedChanges,
    showUnsavedModal,
    currentFormData,
    canSave,
    canReset,
    saveChanges,
    resetChanges,
    confirmExit,
    cancelExit
} = useUnsavedChanges(props.settings, saveCallback)

// Funciones para manejar los botones
const handleSave = async () => {
    try {
        await saveChanges()
    } catch (error) {
        console.error('Error al guardar configuración de seguridad:', error)
    }
}

const handleReset = () => {
    resetChanges()
    emit('reset')
}

// Watcher para emitir cambios al componente padre
watch(hasUnsavedChanges, (newValue) => {
    emit('unsaved-changes', newValue)
}, { immediate: true })
</script>
