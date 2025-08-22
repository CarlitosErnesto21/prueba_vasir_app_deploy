<template>
    <AuthenticatedLayout>
        <!-- Toast para notificaciones -->
        <Toast />
        
        <div class="container mx-auto px-4 py-8">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                    <h1 class="text-2xl font-bold text-white flex items-center">
                        <FontAwesomeIcon :icon="faGear" class="mr-3" />
                        Configuración del Sistema
                    </h1>
                    <p class="text-red-100 mt-2">Configure los parámetros generales del sistema</p>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <!-- Información General del Sistema -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                            <span class="text-2xl mr-2">⚙️</span>
                            Información General del Sistema
                        </h2>
                        <div class="bg-gray-50 rounded-lg p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Sistema</label>
                                    <input 
                                        v-model="settings.systemName"
                                        type="text"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Versión</label>
                                    <input 
                                        v-model="settings.version"
                                        type="text"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email de Contacto</label>
                                    <input 
                                        v-model="settings.contactEmail"
                                        type="email"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono de Contacto</label>
                                    <input 
                                        v-model="settings.contactPhone"
                                        type="tel"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Configuración de Seguridad -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                            <span class="text-2xl mr-2">🔐</span>
                            Seguridad y Autenticación
                        </h2>
                        <div class="bg-gray-50 rounded-lg p-6">
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input 
                                        v-model="settings.requireEmailVerification"
                                        type="checkbox"
                                        class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                                    />
                                    <label class="ml-2 text-sm text-gray-700">
                                        Requerir verificación de email
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input 
                                        v-model="settings.enableTwoFactor"
                                        type="checkbox"
                                        class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                                    />
                                    <label class="ml-2 text-sm text-gray-700">
                                        Habilitar autenticación de dos factores
                                    </label>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Tiempo de sesión (minutos)
                                    </label>
                                    <input 
                                        v-model="settings.sessionTimeout"
                                        type="number"
                                        min="5"
                                        max="1440"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Configuración de Notificaciones -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                            <span class="text-2xl mr-2">�</span>
                            Notificaciones
                        </h2>
                        <div class="bg-gray-50 rounded-lg p-6">
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input 
                                        v-model="settings.emailNotifications"
                                        type="checkbox"
                                        class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                                    />
                                    <label class="ml-2 text-sm text-gray-700">
                                        Enviar notificaciones por email
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input 
                                        v-model="settings.smsNotifications"
                                        type="checkbox"
                                        class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                                    />
                                    <label class="ml-2 text-sm text-gray-700">
                                        Enviar notificaciones por SMS
                                    </label>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Servidor SMTP
                                    </label>
                                    <input 
                                        v-model="settings.smtpServer"
                                        type="text"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contenido del Sitio Web -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                            <span class="text-2xl mr-2">🌐</span>
                            Contenido del Sitio Web
                        </h2>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Descripción de la Empresa
                                    </label>
                                    <textarea 
                                        v-model="settings.description"
                                        rows="4"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                        placeholder="Describe brevemente qué es VASIR y qué hacen..."
                                    ></textarea>
                                    <p class="text-xs text-blue-600 mt-1">Este texto aparecerá en el encabezado principal de la página Sobre Nosotros</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Misión de la Empresa
                                    </label>
                                    <textarea 
                                        v-model="settings.mission"
                                        rows="4"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                        placeholder="Describe la misión de VASIR..."
                                    ></textarea>
                                    <p class="text-xs text-blue-600 mt-1">Este texto aparecerá en la sección "Nuestra Misión" de la página Sobre Nosotros</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Visión de la Empresa
                                    </label>
                                    <textarea 
                                        v-model="settings.vision"
                                        rows="4"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                        placeholder="Describe la visión de VASIR..."
                                    ></textarea>
                                    <p class="text-xs text-blue-600 mt-1">Este texto aparecerá en la sección "Nuestra Visión" de la página Sobre Nosotros</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gestión de Base de Datos -->
                    <div class="mb-8 border-t pt-8">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                            <FontAwesomeIcon :icon="faDatabase" class="mr-2 text-red-600" />
                            Gestión de Base de Datos
                        </h2>
                        <div class="bg-gray-50 rounded-lg p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-800 mb-2">Respaldo de Base de Datos</h3>
                                    <p class="text-sm text-gray-600 mb-4">
                                        Cree un respaldo completo de la base de datos para proteger su información.
                                    </p>
                                    <button 
                                        @click="goToBackup"
                                        class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 flex items-center shadow-lg"
                                    >
                                        <FontAwesomeIcon :icon="faFileArchive" class="mr-2" />
                                        Crear Respaldo
                                    </button>
                                </div>
                                <div>
                                    <h3 class="text-lg font-medium text-gray-800 mb-2">Información de la Base de Datos</h3>
                                    <div class="space-y-2 text-sm text-gray-600">
                                        <p><span class="font-medium">Último respaldo:</span> {{ databaseInfo.last_backup_formatted }}</p>
                                        <p><span class="font-medium">Estado:</span> 
                                            <span :class="getStatusClass(databaseInfo.status)" class="font-medium">
                                                {{ databaseInfo.status_text }}
                                            </span>
                                        </p>
                                        <p><span class="font-medium">Tamaño aproximado:</span> {{ databaseInfo.database_size }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="flex justify-end space-x-4">
                        <button 
                            @click="resetSettings"
                            class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200"
                        >
                            Restablecer
                        </button>
                        <button 
                            @click="saveSettings"
                            :disabled="isSaving"
                            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:bg-gray-400 transition-colors duration-200 flex items-center"
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
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { useToast } from 'primevue/usetoast';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Toast from 'primevue/toast';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faGear, faSpinner, faFileArchive, faDatabase } from '@fortawesome/free-solid-svg-icons';

const page = usePage();
const toast = useToast();

const props = defineProps({
    siteSettings: {
        type: Object,
        default: () => ({})
    },
    databaseInfo: {
        type: Object,
        default: () => ({
            last_backup_formatted: 'No disponible',
            database_size: 'No disponible',
            status: 'unknown',
            status_text: 'Desconocido'
        })
    }
});

const isSaving = ref(false);

// Configuraciones del sistema
const settings = ref({
    // Configuraciones para contenido del sitio
    mission: props.siteSettings.mission || '',
    vision: props.siteSettings.vision || '',
    description: props.siteSettings.description || '',
    
    // Configuraciones del sistema (solo para display, no funcionales aún)
    systemName: 'Sistema VASIR',
    version: '1.0.0',
    contactEmail: 'contacto@vasir.com',
    contactPhone: '+505 8888-8888',
    requireEmailVerification: true,
    enableTwoFactor: false,
    sessionTimeout: 120,
    emailNotifications: true,
    smsNotifications: false,
    smtpServer: 'smtp.gmail.com'
});

// Configuraciones originales para restablecer
const originalSettings = ref({...settings.value});

// Función para obtener las clases CSS según el estado de la base de datos
const getStatusClass = (status) => {
    switch (status) {
        case 'operational':
            return 'text-green-600';
        case 'high_load':
            return 'text-yellow-600';
        case 'warning':
            return 'text-orange-600';
        case 'error':
            return 'text-red-600';
        default:
            return 'text-gray-600';
    }
};

const saveSettings = async () => {
    isSaving.value = true;
    try {
        // Guardar configuraciones de contenido del sitio
        await router.post(route('settings.update'), {
            mission: settings.value.mission,
            vision: settings.value.vision,
            description: settings.value.description
        }, {
            onSuccess: (page) => {
                // Actualizar las configuraciones originales
                originalSettings.value.mission = settings.value.mission;
                originalSettings.value.vision = settings.value.vision;
                originalSettings.value.description = settings.value.description;
                
                // Mostrar toast de éxito
                toast.add({
                    severity: 'success',
                    summary: '¡Éxito!',
                    detail: 'Configuración de la empresa actualizada correctamente',
                    life: 3000
                });
                
                console.log('Configuración guardada exitosamente');
            },
            onError: (errors) => {
                console.error('Error al guardar la configuración:', errors);
                
                // Mostrar toast de error
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'Error al guardar la configuración',
                    life: 5000
                });
            }
        });
        
    } catch (error) {
        console.error('Error al guardar la configuración:', error);
        
        // Mostrar toast de error
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Error al guardar la configuración',
            life: 5000
        });
    } finally {
        isSaving.value = false;
    }
};

const resetSettings = () => {
    if (confirm('¿Está seguro de restablecer todos los cambios?')) {
        settings.value = {...originalSettings.value};
    }
};

const goToBackup = () => {
    router.visit(route('backup'));
};
</script>
