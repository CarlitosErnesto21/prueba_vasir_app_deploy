<template>
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-2 sm:px-4 lg:px-6 py-2 sm:py-3 lg:py-4">
            <h1 class="text-sm sm:text-lg lg:text-2xl font-bold text-white flex items-center">
                <span class="text-sm sm:text-lg lg:text-2xl mr-1 sm:mr-2 lg:mr-3">⚙️</span>
                <span class="hidden sm:inline">Configuración General del Sistema</span>
                <span class="sm:hidden">Configuraciones generales</span>
            </h1>
            <p class="text-red-100 mt-0.5 sm:mt-1 lg:mt-2 text-xs sm:text-sm lg:text-base hidden sm:block">Información básica y configuraciones técnicas del sistema</p>
        </div>

        <!-- Content -->
        <div class="p-2 sm:p-4 lg:p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-2 sm:gap-4 lg:gap-6">
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Nombre del Sistema</label>
                    <input 
                        v-model="settings.systemName"
                        type="text"
                        class="w-full px-2 sm:px-3 lg:px-4 py-1.5 sm:py-2 text-xs sm:text-sm lg:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent shadow-sm"
                    />
                </div>
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Versión</label>
                    <input 
                        v-model="settings.version"
                        type="text"
                        class="w-full px-2 sm:px-3 lg:px-4 py-1.5 sm:py-2 text-xs sm:text-sm lg:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent shadow-sm"
                    />
                </div>
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Email de Contacto</label>
                    <input 
                        v-model="settings.contactEmail"
                        type="email"
                        class="w-full px-2 sm:px-3 lg:px-4 py-1.5 sm:py-2 text-xs sm:text-sm lg:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent shadow-sm"
                    />
                </div>
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Teléfono de Contacto</label>
                    <input 
                        v-model="settings.contactPhone"
                        type="tel"
                        class="w-full px-2 sm:px-3 lg:px-4 py-1.5 sm:py-2 text-xs sm:text-sm lg:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent shadow-sm"
                    />
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex flex-col space-y-1 sm:flex-row sm:justify-end sm:space-y-0 sm:space-x-2 lg:space-x-4 mt-3 sm:mt-6 lg:mt-8 pt-2 sm:pt-4 lg:pt-6 border-t border-gray-200">
                <button 
                    @click="$emit('reset')"
                    class="w-full sm:w-auto px-2 sm:px-4 lg:px-6 py-1.5 sm:py-2 text-xs sm:text-sm lg:text-base border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200"
                >
                    Restablecer
                </button>
                <button 
                    @click="$emit('save')"
                    :disabled="isSaving"
                    class="w-full sm:w-auto px-2 sm:px-4 lg:px-6 py-1.5 sm:py-2 text-xs sm:text-sm lg:text-base bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:bg-gray-400 transition-colors duration-200 flex items-center justify-center"
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
    </div>
</template>

<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faSpinner } from '@fortawesome/free-solid-svg-icons'

defineProps({
    settings: {
        type: Object,
        required: true
    },
    isSaving: {
        type: Boolean,
        default: false
    }
})

defineEmits(['save', 'reset'])
</script>
