<template>
    <AuthenticatedLayout>
        <div class="container mx-auto px-4 py-8">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                    <div class="flex items-center">
                        <button 
                            @click="router.visit(route('settings'))"
                            class="flex items-center text-white hover:text-red-200 transition-colors duration-200 p-2 rounded-lg hover:bg-red-800 mr-4"
                            title="Regresar a Configuración"
                        >
                            <FontAwesomeIcon :icon="faArrowLeft" class="h-5 w-5" />
                        </button>
                        <div>
                            <h1 class="text-2xl font-bold text-white flex items-center">
                                <FontAwesomeIcon :icon="faFileArchive" class="mr-3" />
                                Respaldo de Base de Datos
                            </h1>
                            <p class="text-red-100 mt-2">Genere y gestione copias de seguridad de la base de datos</p>
                        </div>
                    </div>
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
                        
                        <div class="flex space-x-4">
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
                            
                            <button 
                                @click="cleanupBackups" 
                                :disabled="isGenerating"
                                class="bg-yellow-600 hover:bg-yellow-700 disabled:bg-gray-400 text-white font-semibold py-3 px-6 rounded-lg flex items-center transition-colors duration-200"
                            >
                                <FontAwesomeIcon :icon="faBroom" class="mr-2" />
                                Limpiar Antiguos
                            </button>
                        </div>
                    </div>

                    <!-- Lista de Respaldos -->
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Respaldos Disponibles</h2>
                        
                        <div v-if="isLoading" class="text-center py-8 text-gray-500">
                            <FontAwesomeIcon :icon="faSpinner" class="text-4xl mb-2 animate-spin" />
                            <p>Cargando respaldos...</p>
                        </div>
                        
                        <div v-else-if="backups.length === 0" class="text-center py-8 text-gray-500">
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
        
        <!-- Toast para notificaciones -->
        <Toast />
        <!-- Dialog de confirmación -->
        <ConfirmDialog />
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Toast from 'primevue/toast';
import ConfirmDialog from 'primevue/confirmdialog';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { 
    faFileArchive, 
    faDownload, 
    faTrash, 
    faFolderOpen, 
    faInfoCircle, 
    faSpinner,
    faArrowLeft,
    faBroom
} from '@fortawesome/free-solid-svg-icons';
import axios from 'axios';

const toast = useToast();
const confirm = useConfirm();
const isGenerating = ref(false);
const isLoading = ref(false);
const backups = ref([]);

const loadBackups = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get('/api/backups', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        
        if (response.data.success) {
            backups.value = response.data.backups;
        } else {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: response.data.message || 'Error al cargar los backups',
                life: 5000
            });
        }
    } catch (error) {
        if (error.response) {
            toast.add({
                severity: 'error',
                summary: 'Error de Conexión',
                detail: `Error ${error.response.status}: ${error.response.data.message || 'Error desconocido'}`,
                life: 5000
            });
        } else {
            toast.add({
                severity: 'error',
                summary: 'Error de Red',
                detail: 'No se pudo conectar con el servidor',
                life: 5000
            });
        }
    } finally {
        isLoading.value = false;
    }
};

const generateBackup = async () => {
    isGenerating.value = true;
    try {
        const response = await axios.post('/api/backups/generate', {}, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        
        if (response.data.success) {
            toast.add({
                severity: 'success',
                summary: 'Éxito',
                detail: 'Respaldo generado exitosamente',
                life: 4000
            });
            
            // Agregar un pequeño delay para que el archivo esté disponible
            await new Promise(resolve => setTimeout(resolve, 1000));
            await loadBackups(); // Recargar lista de respaldos
        } else {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: response.data.message || 'Error al generar el respaldo',
                life: 5000
            });
        }
    } catch (error) {
        if (error.response) {
            toast.add({
                severity: 'error',
                summary: 'Error al Generar',
                detail: error.response.data.message || 'Error desconocido',
                life: 8000
            });
        } else {
            toast.add({
                severity: 'error',
                summary: 'Error de Red',
                detail: 'No se pudo generar el respaldo',
                life: 5000
            });
        }
    } finally {
        isGenerating.value = false;
    }
};

const downloadBackup = async (backupId) => {
    try {
        const response = await axios.get(`/api/backups/${backupId}/download`, {
            responseType: 'blob'
        });
        
        // Crear un enlace de descarga
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        
        // Obtener el nombre del archivo del header o usar uno por defecto
        const contentDisposition = response.headers['content-disposition'];
        let filename = 'backup.zip';
        if (contentDisposition) {
            const filenameMatch = contentDisposition.match(/filename="(.+)"/);
            if (filenameMatch) {
                filename = filenameMatch[1];
            }
        }
        
        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);
        
        toast.add({
            severity: 'success',
            summary: 'Descarga Iniciada',
            detail: 'El archivo se está descargando',
            life: 3000
        });
        
    } catch (error) {
        toast.add({
            severity: 'error',
            summary: 'Error de Descarga',
            detail: 'No se pudo descargar el respaldo',
            life: 5000
        });
    }
};

const deleteBackup = async (backupId) => {
    confirm.require({
        message: '¿Está seguro de eliminar este respaldo?',
        header: 'Confirmar Eliminación',
        icon: 'pi pi-exclamation-triangle',
        rejectLabel: 'Cancelar',
        acceptLabel: 'Eliminar',
        rejectClass: 'p-button-secondary p-button-outlined',
        acceptClass: 'p-button-danger',
        accept: async () => {
            try {
                const response = await axios.delete(`/api/backups/${backupId}`);
                if (response.data.success) {
                    toast.add({
                        severity: 'success',
                        summary: 'Eliminado',
                        detail: 'Respaldo eliminado exitosamente',
                        life: 3000
                    });
                    await loadBackups(); // Recargar lista de respaldos
                } else {
                    toast.add({
                        severity: 'error',
                        summary: 'Error',
                        detail: response.data.message || 'Error al eliminar el respaldo',
                        life: 5000
                    });
                }
            } catch (error) {
                toast.add({
                    severity: 'error',
                    summary: 'Error de Eliminación',
                    detail: 'No se pudo eliminar el respaldo',
                    life: 5000
                });
            }
        }
    });
};

const cleanupBackups = async () => {
    confirm.require({
        message: '¿Está seguro de limpiar los respaldos antiguos? Se mantendrán solo los últimos 3 backups.',
        header: 'Confirmar Limpieza',
        icon: 'pi pi-exclamation-triangle',
        rejectLabel: 'Cancelar',
        acceptLabel: 'Limpiar',
        rejectClass: 'p-button-secondary p-button-outlined',
        acceptClass: 'p-button-warning',
        accept: async () => {
            try {
                const response = await axios.post('/api/backups/cleanup', {}, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.data.success) {
                    toast.add({
                        severity: 'success',
                        summary: 'Limpieza Completada',
                        detail: response.data.message,
                        life: 5000
                    });
                    
                    await loadBackups(); // Recargar lista de respaldos
                } else {
                    toast.add({
                        severity: 'error',
                        summary: 'Error en Limpieza',
                        detail: response.data.message || 'Error al limpiar respaldos',
                        life: 5000
                    });
                }
            } catch (error) {
                toast.add({
                    severity: 'error',
                    summary: 'Error de Limpieza',
                    detail: error.response?.data?.message || 'No se pudo ejecutar la limpieza de respaldos',
                    life: 5000
                });
            }
        }
    });
};

onMounted(() => {
    loadBackups();
});
</script>
