<template>
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-2 sm:px-4 lg:px-6 py-2 sm:py-3 lg:py-4">
            <h1 class="text-sm sm:text-lg lg:text-2xl font-bold text-white flex items-center">
                <span class="text-sm sm:text-lg lg:text-2xl mr-1 sm:mr-2 lg:mr-3">🏢</span>
                <span class="hidden sm:inline">Información Corporativa</span>
                <span class="sm:hidden">Info Corporativa</span>
            </h1>
            <p class="text-blue-100 mt-0.5 sm:mt-1 lg:mt-2 text-xs sm:text-sm lg:text-base hidden sm:block">Configure el contenido que aparece en la página "Sobre Nosotros"</p>
        </div>

        <!-- Content -->
        <div class="p-2 sm:p-4 lg:p-6">
            <div class="grid grid-cols-1 gap-3 sm:gap-6 lg:gap-8">
                <!-- Descripción de la Empresa -->
                <div class="bg-gray-50 rounded-lg p-2 sm:p-4 lg:p-6 border border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center mb-2 sm:mb-3 lg:mb-4">
                        <span class="text-sm sm:text-xl lg:text-2xl mb-1 sm:mb-0 sm:mr-3">📝</span>
                        <div>
                            <label class="block text-sm sm:text-base lg:text-lg font-semibold text-gray-800">
                                Descripción de la Empresa
                            </label>
                            <p class="text-xs sm:text-sm text-gray-600 hidden sm:block">Presentación principal en el encabezado de "Sobre Nosotros"</p>
                        </div>
                    </div>
                    <textarea 
                        v-model="settings.description"
                        rows="3"
                        class="w-full px-2 sm:px-3 lg:px-4 py-1.5 sm:py-2 lg:py-3 text-xs sm:text-sm lg:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none shadow-sm"
                        placeholder="Describe brevemente qué es VASIR, su propósito y lo que ofrece a los clientes..."
                    ></textarea>
                </div>

                <!-- Grid para Misión y Visión -->
                <div class="grid grid-cols-1 gap-2 sm:gap-4 lg:gap-6">
                    <!-- Misión -->
                    <div class="bg-slate-50 rounded-lg p-2 sm:p-4 lg:p-6 border border-slate-200">
                        <div class="flex flex-col sm:flex-row sm:items-center mb-2 sm:mb-3 lg:mb-4">
                            <span class="text-sm sm:text-xl lg:text-2xl mb-1 sm:mb-0 sm:mr-3">🎯</span>
                            <div>
                                <label class="block text-sm sm:text-base lg:text-lg font-semibold text-gray-800">
                                    Misión Corporativa
                                </label>
                                <p class="text-xs sm:text-sm text-slate-600 hidden sm:block">¿Cuál es el propósito de VASIR?</p>
                            </div>
                        </div>
                        <textarea 
                            v-model="settings.mission"
                            rows="3"
                            class="w-full px-2 sm:px-3 lg:px-4 py-1.5 sm:py-2 lg:py-3 text-xs sm:text-sm lg:text-base border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none shadow-sm bg-white"
                            placeholder="Nuestra misión es..."
                        ></textarea>
                    </div>
                    
                    <!-- Visión -->
                    <div class="bg-slate-50 rounded-lg p-2 sm:p-4 lg:p-6 border border-slate-200">
                        <div class="flex flex-col sm:flex-row sm:items-center mb-2 sm:mb-3 lg:mb-4">
                            <span class="text-sm sm:text-xl lg:text-2xl mb-1 sm:mb-0 sm:mr-3">🌟</span>
                            <div>
                                <label class="block text-sm sm:text-base lg:text-lg font-semibold text-gray-800">
                                    Visión Corporativa
                                </label>
                                <p class="text-xs sm:text-sm text-slate-600 hidden sm:block">¿Hacia dónde se dirige VASIR?</p>
                            </div>
                        </div>
                        <textarea 
                            v-model="settings.vision"
                            rows="3"
                            class="w-full px-2 sm:px-3 lg:px-4 py-1.5 sm:py-2 lg:py-3 text-xs sm:text-sm lg:text-base border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none shadow-sm bg-white"
                            placeholder="Nuestra visión es..."
                        ></textarea>
                    </div>
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
