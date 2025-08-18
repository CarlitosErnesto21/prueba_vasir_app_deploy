<template>
    <AuthenticatedLayout>
        <div class="container mx-auto px-4 py-8">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                    <h1 class="text-2xl font-bold text-white flex items-center">
                        <FontAwesomeIcon :icon="faFileArchive" class="mr-3" />
                        Respaldo de Base de Datos
                    </h1>
                    <p class="text-red-100 mt-2">Genere y gestione copias de seguridad de la base de datos</p>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <!-- Generar Respaldo -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Generar Nuevo Respaldo</h2>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center">
                                <FontAwesomeIcon :icon="faInfoCircle" class="text-blue-500 mr-2" />
                                <p class="text-blue-700">
                                    Esta acción creará una copia completa de la base de datos actual.
                                </p>
                            </div>
                        </div>
                        
                        <button 
                            @click="generateBackup" 
                            :disabled="isGenerating"
                            class="bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white font-semibold py-3 px-6 rounded-lg flex items-center transition-colors duration-200"
                        >
                            <FontAwesomeIcon 
                                :icon="isGenerating ? faSpinner : faDownload" 
                                :class="{ 'animate-spin': isGenerating }" 
                                class="mr-2" 
                            />
                            {{ isGenerating ? 'Generando...' : 'Generar Respaldo' }}
                        </button>
                    </div>

                    <!-- Lista de Respaldos -->
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Respaldos Disponibles</h2>
                        
                        <div v-if="backups.length === 0" class="text-center py-8 text-gray-500">
                            <FontAwesomeIcon :icon="faFolderOpen" class="text-4xl mb-2" />
                            <p>No hay respaldos disponibles</p>
                        </div>

                        <div v-else class="space-y-3">
                            <div 
                                v-for="backup in backups" 
                                :key="backup.id"
                                class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200"
                            >
                                <div class="flex items-center">
                                    <FontAwesomeIcon :icon="faFileArchive" class="text-red-600 mr-3" />
                                    <div>
                                        <h3 class="font-medium text-gray-800">{{ backup.name }}</h3>
                                        <p class="text-sm text-gray-500">{{ backup.date }} - {{ backup.size }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex space-x-2">
                                    <button 
                                        @click="downloadBackup(backup.id)"
                                        class="text-blue-600 hover:text-blue-800 p-2 rounded-full hover:bg-blue-50 transition-colors duration-200"
                                        title="Descargar"
                                    >
                                        <FontAwesomeIcon :icon="faDownload" />
                                    </button>
                                    <button 
                                        @click="deleteBackup(backup.id)"
                                        class="text-red-600 hover:text-red-800 p-2 rounded-full hover:bg-red-50 transition-colors duration-200"
                                        title="Eliminar"
                                    >
                                        <FontAwesomeIcon :icon="faTrash" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { 
    faFileArchive, 
    faDownload, 
    faTrash, 
    faFolderOpen, 
    faInfoCircle, 
    faSpinner 
} from '@fortawesome/free-solid-svg-icons';

const isGenerating = ref(false);
const backups = ref([
    // Datos de ejemplo - reemplazar con datos reales del backend
    {
        id: 1,
        name: 'backup_2025_08_17_143022.sql',
        date: '17/08/2025 14:30',
        size: '2.4 MB'
    },
    {
        id: 2,
        name: 'backup_2025_08_16_093015.sql',
        date: '16/08/2025 09:30',
        size: '2.3 MB'
    }
]);

const generateBackup = async () => {
    isGenerating.value = true;
    try {
        // Aquí iría la lógica para generar el respaldo
        await new Promise(resolve => setTimeout(resolve, 2000)); // Simulación
        alert('Respaldo generado exitosamente');
        // Recargar lista de respaldos
    } catch (error) {
        alert('Error al generar el respaldo');
    } finally {
        isGenerating.value = false;
    }
};

const downloadBackup = (backupId) => {
    // Lógica para descargar respaldo
    alert(`Descargando respaldo ID: ${backupId}`);
};

const deleteBackup = (backupId) => {
    if (confirm('¿Está seguro de eliminar este respaldo?')) {
        // Lógica para eliminar respaldo
        backups.value = backups.value.filter(backup => backup.id !== backupId);
    }
};

onMounted(() => {
    // Cargar respaldos desde el backend
});
</script>
