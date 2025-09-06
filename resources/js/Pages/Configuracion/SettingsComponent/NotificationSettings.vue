<template>
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-4 lg:px-6 py-3 lg:py-4">
            <h1 class="text-lg sm:text-xl lg:text-2xl font-bold text-white flex items-center">
                <span class="text-lg sm:text-xl lg:text-2xl mr-2 lg:mr-3">📧</span>
                <span class="hidden sm:inline">Configuración de Notificaciones</span>
                <span class="sm:hidden">Notificaciones</span>
            </h1>
            <p class="text-red-100 mt-1 lg:mt-2 text-xs sm:text-sm lg:text-base">Gestión de comunicaciones y servicios de mensajería</p>
        </div>

        <!-- Content -->
        <div class="p-4 lg:p-6">
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 lg:gap-6">
                <!-- Tipos de notificaciones -->
                <div class="space-y-3 lg:space-y-4">
                    <h3 class="text-base lg:text-lg font-medium text-gray-800 mb-2 lg:mb-3">Tipos de Notificaciones</h3>
                    <div class="flex items-center p-3 lg:p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <input 
                            v-model="settings.emailNotifications"
                            type="checkbox"
                            class="h-4 w-4 lg:h-5 lg:w-5 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                        />
                        <label class="ml-2 lg:ml-3 text-xs sm:text-sm font-medium text-gray-700">
                            Enviar notificaciones por email
                        </label>
                    </div>
                    <div class="flex items-center p-3 lg:p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <input 
                            v-model="settings.smsNotifications"
                            type="checkbox"
                            class="h-4 w-4 lg:h-5 lg:w-5 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                        />
                        <label class="ml-2 lg:ml-3 text-xs sm:text-sm font-medium text-gray-700">
                            Enviar notificaciones por SMS
                        </label>
                    </div>
                </div>
                
                <!-- Configuración SMTP -->
                <div>
                    <h3 class="text-base lg:text-lg font-medium text-gray-800 mb-2 lg:mb-3">Configuración SMTP</h3>
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                            Servidor SMTP
                        </label>
                        <input 
                            v-model="settings.smtpServer"
                            type="text"
                            class="w-full px-3 lg:px-4 py-2 text-sm lg:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent shadow-sm"
                            placeholder="smtp.gmail.com"
                        />
                        <p class="text-xs text-gray-500 mt-1">Servidor para envío de correos electrónicos</p>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-4 mt-6 lg:mt-8 pt-4 lg:pt-6 border-t border-gray-200">
                <button 
                    @click="$emit('reset')"
                    class="w-full sm:w-auto px-4 lg:px-6 py-2 text-sm lg:text-base border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200"
                >
                    Restablecer
                </button>
                <button 
                    @click="$emit('save')"
                    :disabled="isSaving"
                    class="w-full sm:w-auto px-4 lg:px-6 py-2 text-sm lg:text-base bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:bg-gray-400 transition-colors duration-200 flex items-center justify-center"
                >
                    <FontAwesomeIcon 
                        v-if="isSaving" 
                        :icon="faSpinner" 
                        class="animate-spin mr-2" 
                    />
                    {{ isSaving ? 'Guardando...' : 'Guardar Configuración' }}
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
