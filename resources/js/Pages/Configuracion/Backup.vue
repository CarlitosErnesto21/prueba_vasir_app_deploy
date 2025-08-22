<template>
    <AuthenticatedLayout>
        <div class="container mx-auto px-4 py-8">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <button 
                                @click="navigateWithConfirmation(route('settings'))"
                                class="flex items-center text-white hover:text-red-200 transition-colors duration-200 p-2 rounded-lg hover:bg-red-800 mr-4"
                                title="Regresar a Configuración"
                            >
                                <FontAwesomeIcon :icon="faArrowLeft" class="h-5 w-5" />
                            </button>
                            <div>
                                <h1 class="text-2xl font-bold text-white flex items-center">
                                    <FontAwesomeIcon :icon="faFileArchive" class="mr-3" />
                                    Respaldo de Base de Datos
                                    <!-- Indicador de cambios pendientes -->
                                    <span v-if="hasUnsavedChanges || pendingActions.length > 0" 
                                          class="ml-3 bg-yellow-400 text-yellow-900 text-sm px-2 py-1 rounded-full font-medium">
                                        {{ totalPendingChanges }} cambios pendientes
                                    </span>
                                </h1>
                                <p class="text-red-100 mt-2">Genere y gestione copias de seguridad de la base de datos</p>
                            </div>
                        </div>
                        
                        <!-- Botón Guardar Configuración en el Header -->
                        <div class="flex items-center space-x-3">
                            <button 
                                @click="saveBackupSettings"
                                :disabled="isSavingSettings || (!hasUnsavedChanges && pendingActions.length === 0)"
                                class="bg-green-600 hover:bg-green-700 disabled:bg-gray-500 disabled:opacity-50 text-white font-semibold py-2 px-6 rounded-lg flex items-center transition-colors duration-200 shadow-lg"
                            >
                                <FontAwesomeIcon 
                                    :icon="isSavingSettings ? faSpinner : faDownload" 
                                    :class="{ 'animate-spin': isSavingSettings }" 
                                    class="mr-2" 
                                />
                                {{ isSavingSettings ? 'Guardando...' : 'Guardar Configuración' }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <!-- 1. CONFIGURACIÓN DE RESPALDOS AUTOMÁTICOS - PRIMERA SECCIÓN -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                            <span class="text-2xl mr-2">⚙️</span>
                            Configuración de Respaldos Automáticos
                        </h2>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                            <div class="space-y-6">
                                <!-- Estado actual con indicador visual -->
                                <div class="bg-white border rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div :class="backupSettings.auto_backup_enabled ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                                                 class="px-3 py-1 rounded-full text-sm font-medium mr-3">
                                                {{ backupSettings.auto_backup_enabled ? '🟢 Activo' : '🔴 Inactivo' }}
                                            </div>
                                            <span class="text-gray-700 font-medium">
                                                Estado de respaldos automáticos
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center">
                                    <input 
                                        :checked="backupSettings.auto_backup_enabled"
                                        @change="confirmBackupToggle"
                                        type="checkbox"
                                        class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                                    />
                                    <label class="ml-2 text-sm text-gray-700">
                                        Habilitar respaldos automáticos
                                    </label>
                                </div>

                                <!-- Alerta cuando los respaldos están deshabilitados -->
                                <div v-if="!backupSettings.auto_backup_enabled" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <FontAwesomeIcon :icon="faInfoCircle" class="h-5 w-5 text-yellow-400" />
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-yellow-800">
                                                Respaldos automáticos deshabilitados
                                            </h3>
                                            <div class="mt-2 text-sm text-yellow-700">
                                                <p>
                                                    Sus datos no están siendo respaldados automáticamente. 
                                                    Se recomienda habilitar esta función para proteger la información del sistema.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="backupSettings.auto_backup_enabled" class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Frecuencia de respaldo
                                        </label>
                                        <select 
                                            v-model="backupSettings.backup_frequency"
                                            @change="markAsModified"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        >
                                            <option value="every_2_minutes">Cada 2 minutos (solo para pruebas)</option>
                                            <option value="hourly">Cada hora</option>
                                            <option value="daily">Diario</option>
                                            <option value="weekly">Semanal</option>
                                            <option value="monthly">Mensual</option>
                                        </select>
                                        
                                        <!-- Alerta para la opción de 2 minutos -->
                                        <div v-if="backupSettings.backup_frequency === 'every_2_minutes'" class="mt-3 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                            <div class="flex">
                                                <div class="flex-shrink-0">
                                                    <FontAwesomeIcon :icon="faInfoCircle" class="h-5 w-5 text-yellow-400" />
                                                </div>
                                                <div class="ml-3">
                                                    <h3 class="text-sm font-medium text-yellow-800">
                                                        ⚠️ Modo de Prueba Activado
                                                    </h3>
                                                    <div class="mt-1 text-sm text-yellow-700">
                                                        <p>
                                                            Esta frecuencia está diseñada únicamente para comprobar que los respaldos automáticos funcionen correctamente. 
                                                            Se recomienda cambiar a una frecuencia más apropiada (diaria, semanal) una vez verificado el funcionamiento.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Días de retención (1-365)
                                        </label>
                                        <input 
                                            v-model="backupSettings.backup_retention_days"
                                            @input="markAsModified"
                                            type="number"
                                            min="1"
                                            max="365"
                                            class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        />
                                        <p class="text-xs text-gray-500 mt-1">Los respaldos más antiguos se eliminarán automáticamente</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. GESTIÓN MANUAL DE RESPALDOS -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Gestión Manual de Respaldos</h2>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center">
                                <FontAwesomeIcon :icon="faInfoCircle" class="text-blue-500 mr-2" />
                                <p class="text-blue-700">
                                    Las acciones se ejecutarán cuando haga clic en "Guardar Configuración".
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex space-x-4">
                            <button 
                                @click="generateBackup" 
                                :disabled="isSavingSettings"
                                class="bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white font-semibold py-3 px-6 rounded-lg flex items-center transition-colors duration-200"
                            >
                                <FontAwesomeIcon :icon="faDownload" class="mr-2" />
                                Generar Respaldo
                            </button>
                            
                            <button 
                                @click="cleanupBackups" 
                                :disabled="isSavingSettings"
                                class="bg-yellow-600 hover:bg-yellow-700 disabled:bg-gray-400 text-white font-semibold py-3 px-6 rounded-lg flex items-center transition-colors duration-200"
                            >
                                <FontAwesomeIcon :icon="faBroom" class="mr-2" />
                                Limpiar Antiguos
                            </button>
                        </div>
                    </div>

                    <!-- 3. ACCIONES PENDIENTES -->
                    <div v-if="pendingActions.length > 0" class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Acciones Pendientes</h2>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="space-y-4">
                                <!-- Acciones generales (generar, limpiar) -->
                                <div v-for="(action, index) in pendingActions.filter(a => a.type !== 'delete_backup')" 
                                     :key="index" 
                                     class="flex items-center justify-between bg-white p-3 rounded border">
                                    <span class="text-gray-700">{{ action.description }}</span>
                                    <button @click="removePendingAction(pendingActions.findIndex(a => a === action))"
                                            class="text-red-600 hover:text-red-800 p-1 rounded"
                                            title="Cancelar acción">
                                        <FontAwesomeIcon :icon="faTrash" />
                                    </button>
                                </div>
                                
                                <!-- Respaldos marcados para eliminación -->
                                <div v-if="pendingDeleteBackups.length > 0" class="border-t pt-4">
                                    <h3 class="text-md font-medium text-gray-700 mb-2">Respaldos marcados para eliminación:</h3>
                                    <div class="space-y-2">
                                        <div v-for="(deleteAction, index) in pendingDeleteBackups" 
                                             :key="deleteAction.backupId"
                                             class="flex items-center justify-between bg-red-50 border border-red-200 p-3 rounded">
                                            <div class="flex items-center">
                                                <FontAwesomeIcon :icon="faFileArchive" class="text-red-600 mr-3" />
                                                <div>
                                                    <span class="font-medium text-gray-800">{{ deleteAction.backupName }}</span>
                                                    <p class="text-sm text-gray-500">Será eliminado al guardar configuración</p>
                                                </div>
                                            </div>
                                            <button @click="restoreBackupFromPending(deleteAction)"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm flex items-center"
                                                    title="Restaurar a lista disponible">
                                                <FontAwesomeIcon :icon="faArrowLeft" class="mr-1" />
                                                Restaurar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. RESPALDOS DISPONIBLES -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Respaldos Disponibles</h2>
                        
                        <div v-if="isLoading" class="text-center py-8 text-gray-500">
                            <FontAwesomeIcon :icon="faSpinner" class="text-4xl mb-2 animate-spin" />
                            <p>Cargando respaldos...</p>
                        </div>
                        
                        <div v-else-if="availableBackups.length === 0" class="text-center py-8 text-gray-500">
                            <FontAwesomeIcon :icon="faFolderOpen" class="text-4xl mb-2" />
                            <p v-if="backups.length === 0">No hay respaldos disponibles</p>
                            <p v-else>Todos los respaldos están marcados para eliminación</p>
                        </div>

                        <div v-else class="space-y-3">
                            <div 
                                v-for="backup in availableBackups" 
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
        <ConfirmDialog 
            :style="{ width: '450px' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        />
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
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
import { route } from 'ziggy-js';

const props = defineProps({
    backupSettings: {
        type: Object,
        default: () => ({
            auto_backup_enabled: false,
            backup_frequency: 'daily',
            backup_retention_days: 7
        })
    }
});

const toast = useToast();
const confirm = useConfirm();
const isGenerating = ref(false);
const isLoading = ref(false);
const isSavingSettings = ref(false);
const backups = ref([]);

// Configuración del sistema: número de respaldos a mantener
const KEEP_LATEST_BACKUPS = 3; // Configurable: número de respaldos recientes a mantener

// Configuraciones de backup automático
const backupSettings = ref({
    auto_backup_enabled: props.backupSettings.auto_backup_enabled,
    backup_frequency: props.backupSettings.backup_frequency,
    backup_retention_days: props.backupSettings.backup_retention_days
});

// Variable para controlar el estado anterior del checkbox
const previousBackupState = ref(props.backupSettings.auto_backup_enabled);

// Variables para controlar cambios pendientes
const hasUnsavedChanges = ref(false);
const originalSettings = ref({
    auto_backup_enabled: props.backupSettings.auto_backup_enabled,
    backup_frequency: props.backupSettings.backup_frequency,
    backup_retention_days: props.backupSettings.backup_retention_days
});
const pendingActions = ref([]);

// Computed properties para filtrar respaldos
const availableBackups = computed(() => {
    // Obtener IDs de backups que están pendientes de eliminación
    const pendingDeleteIds = pendingActions.value
        .filter(action => action.type === 'delete_backup')
        .map(action => action.backupId);
    
    // Filtrar backups excluyendo los que están pendientes de eliminación
    return backups.value.filter(backup => !pendingDeleteIds.includes(backup.id));
});

const pendingDeleteBackups = computed(() => {
    // Obtener acciones de eliminación con información completa del backup
    return pendingActions.value
        .filter(action => action.type === 'delete_backup')
        .map(action => {
            const backup = backups.value.find(b => b.id === action.backupId);
            return {
                ...action,
                backup: backup || { name: action.backupName, id: action.backupId }
            };
        });
});

// Computed property para calcular el total de cambios pendientes
const totalPendingChanges = computed(() => {
    let count = pendingActions.value.length;
    
    // Verificar si hay cambios en la configuración de backup
    const settingsChanged = 
        backupSettings.value.auto_backup_enabled !== originalSettings.value.auto_backup_enabled ||
        backupSettings.value.backup_frequency !== originalSettings.value.backup_frequency ||
        backupSettings.value.backup_retention_days !== originalSettings.value.backup_retention_days;
    
    if (settingsChanged) {
        count += 1;
    }
    
    return count;
});

// Función para marcar como modificado
const markAsModified = () => {
    hasUnsavedChanges.value = true;
};

// Función para agregar acción pendiente
const addPendingAction = (action) => {
    pendingActions.value.push(action);
    hasUnsavedChanges.value = true;
};

// Función para remover acción pendiente
const removePendingAction = (index) => {
    const action = pendingActions.value[index];
    pendingActions.value.splice(index, 1);
    
    // Verificar si hay cambios en configuración o acciones pendientes
    const settingsChanged = 
        backupSettings.value.auto_backup_enabled !== originalSettings.value.auto_backup_enabled ||
        backupSettings.value.backup_frequency !== originalSettings.value.backup_frequency ||
        backupSettings.value.backup_retention_days !== originalSettings.value.backup_retention_days;
    
    hasUnsavedChanges.value = settingsChanged || pendingActions.value.length > 0;
    
    // Mostrar mensaje de restauración si era una eliminación
    if (action.type === 'delete_backup') {
        toast.add({
            severity: 'info',
            summary: 'Respaldo Restaurado',
            detail: `El respaldo "${action.backupName}" ha sido restaurado a la lista disponible.`,
            life: 3000
        });
    }
};

// Función específica para restaurar respaldos desde acciones pendientes
const restoreBackupFromPending = (deleteAction) => {
    // Buscar el índice real en pendingActions usando backupId
    const realIndex = pendingActions.value.findIndex(action => 
        action.type === 'delete_backup' && action.backupId === deleteAction.backupId
    );
    
    if (realIndex !== -1) {
        removePendingAction(realIndex);
    }
};

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

const generateBackup = () => {
    // Verificar si ya existe una acción pendiente para generar backup
    const existingAction = pendingActions.value.find(action => action.type === 'generate_backup');
    
    if (existingAction) {
        toast.add({
            severity: 'warn',
            summary: 'Acción Ya Pendiente',
            detail: 'Ya existe una acción pendiente para generar un respaldo.',
            life: 3000
        });
        return;
    }
    
    addPendingAction({
        type: 'generate_backup',
        description: 'Generar nuevo respaldo',
        timestamp: new Date().toISOString()
    });
    
    toast.add({
        severity: 'info',
        summary: 'Acción Pendiente',
        detail: 'La generación del respaldo se ejecutará al guardar la configuración.',
        life: 4000
    });
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

const deleteBackup = (backupId) => {
    // Buscar información del backup en la lista
    const backup = backups.value.find(b => b.id === backupId);
    const backupName = backup ? backup.name : `Backup ${backupId}`;
    
    // Agregar inmediatamente a acciones pendientes
    addPendingAction({
        type: 'delete_backup',
        description: `Eliminar respaldo: ${backupName}`,
        backupId: backupId,
        backupName: backupName,
        timestamp: new Date().toISOString()
    });
    
    toast.add({
        severity: 'info',
        summary: 'Respaldo Marcado',
        detail: `El respaldo "${backupName}" ha sido marcado para eliminación.`,
        life: 3000
    });
};

const cleanupBackups = () => {
    // Verificar si hay respaldos disponibles para limpiar
    if (availableBackups.value.length === 0) {
        toast.add({
            severity: 'info',
            summary: 'No Hay Nada Que Limpiar',
            detail: 'No hay respaldos disponibles para limpiar.',
            life: 3000
        });
        return;
    }
    
    // Si hay KEEP_LATEST_BACKUPS o menos respaldos, no hay necesidad de limpiar
    if (availableBackups.value.length <= KEEP_LATEST_BACKUPS) {
        toast.add({
            severity: 'info',
            summary: 'No Hay Nada Que Limpiar',
            detail: `Solo hay ${availableBackups.value.length} respaldo(s). Se mantienen automáticamente los últimos ${KEEP_LATEST_BACKUPS}.`,
            life: 3000
        });
        return;
    }
    
    // Calcular qué archivos se eliminarán (todos excepto los últimos KEEP_LATEST_BACKUPS)
    const sortedBackups = [...availableBackups.value].sort((a, b) => {
        // Intentar ordenar por fecha, si falla usar el nombre del archivo
        try {
            // Convertir formato "dd/mm/yyyy HH:MM" a Date
            const dateA = a.date.split(' ');
            const [dayA, monthA, yearA] = dateA[0].split('/');
            const timeA = dateA[1] || '00:00';
            const fullDateA = new Date(`${yearA}-${monthA}-${dayA}T${timeA}`);
            
            const dateB = b.date.split(' ');
            const [dayB, monthB, yearB] = dateB[0].split('/');
            const timeB = dateB[1] || '00:00';
            const fullDateB = new Date(`${yearB}-${monthB}-${dayB}T${timeB}`);
            
            return fullDateB - fullDateA; // Más reciente primero
        } catch (error) {
            // Si falla el parseo de fechas, ordenar por nombre del archivo
            console.warn('Error parsing backup dates, falling back to filename sort:', error);
            return b.name.localeCompare(a.name);
        }
    });
    
    const backupsToDelete = sortedBackups.slice(KEEP_LATEST_BACKUPS); // Los que sobran después de mantener los más recientes
    
    if (backupsToDelete.length === 0) {
        toast.add({
            severity: 'info',
            summary: 'No Hay Nada Que Limpiar',
            detail: `Ya tienes solo los últimos ${KEEP_LATEST_BACKUPS} respaldos.`,
            life: 3000
        });
        return;
    }
    
    // Verificar si ya existen acciones pendientes para estos backups
    const existingCleanup = backupsToDelete.some(backup => 
        pendingActions.value.some(action => 
            action.type === 'delete_backup' && action.backupId === backup.id
        )
    );
    
    if (existingCleanup) {
        toast.add({
            severity: 'warn',
            summary: 'Acción Ya Pendiente',
            detail: 'Algunos de estos respaldos ya están marcados para eliminación.',
            life: 3000
        });
        return;
    }
    
    const fileNames = backupsToDelete.map(b => b.name).join(', ');
    
    confirm.require({
        message: `¿Está seguro de limpiar los respaldos antiguos? Se eliminarán ${backupsToDelete.length} respaldo(s): ${fileNames}. Se mantendrán solo los últimos ${KEEP_LATEST_BACKUPS}. Esta acción se ejecutará al guardar la configuración.`,
        header: 'Confirmar Limpieza',
        rejectLabel: 'Cancelar',
        acceptLabel: 'Agregar a Pendientes',
        rejectClass: 'p-button-secondary p-button-outlined',
        acceptClass: 'p-button-warning',
        accept: () => {
            // Agregar cada backup a eliminar como acción individual
            backupsToDelete.forEach(backup => {
                addPendingAction({
                    type: 'delete_backup',
                    backupId: backup.id,
                    backupName: backup.name,
                    description: `Eliminar respaldo: ${backup.name}`,
                    timestamp: new Date().toISOString(),
                    isFromCleanup: true // Marcador para saber que vino de limpieza
                });
            });
            
            toast.add({
                severity: 'info',
                summary: 'Respaldos Marcados para Eliminación',
                detail: `${backupsToDelete.length} respaldo(s) marcados para eliminación. Se ejecutará al guardar la configuración.`,
                life: 4000
            });
        }
    });
};

// Función para confirmar el cambio de estado de respaldos automáticos
const confirmBackupToggle = (event) => {
    const currentState = backupSettings.value.auto_backup_enabled;
    const newState = event.target.checked; // El estado que se quiere cambiar
    
    // Si es el mismo estado, no hacer nada
    if (currentState === newState) return;
    
    const message = newState 
        ? '¿Está seguro de que desea HABILITAR los respaldos automáticos?\n\nEsto iniciará el proceso de respaldos según la frecuencia configurada.'
        : '¿Está seguro de que desea DESHABILITAR los respaldos automáticos?\n\nEsto detendrá la protección automática de sus datos. Se recomienda mantener los respaldos habilitados para la seguridad de la información.';
    
    const confirmMessage = newState 
        ? 'Habilitar Respaldos Automáticos'
        : 'Deshabilitar Respaldos Automáticos';
    
    confirm.require({
        message: message,
        header: confirmMessage,
        icon: newState ? 'pi pi-check-circle' : 'pi pi-exclamation-triangle',
        acceptClass: newState ? 'p-button-success' : 'p-button-danger',
        rejectClass: 'p-button-outlined',
        acceptLabel: newState ? 'Sí, Habilitar' : 'Sí, Deshabilitar',
        rejectLabel: 'Cancelar',
        accept: () => {
            // Confirmar el cambio - actualizar el estado
            backupSettings.value.auto_backup_enabled = newState;
            previousBackupState.value = newState;
            markAsModified(); // Marcar como modificado
            
            toast.add({
                severity: newState ? 'success' : 'warn',
                summary: newState ? 'Respaldos Habilitados' : 'Respaldos Deshabilitados',
                detail: newState 
                    ? 'Los respaldos automáticos han sido habilitados. Recuerde guardar la configuración.'
                    : 'Los respaldos automáticos han sido deshabilitados. Recuerde guardar la configuración.',
                life: 6000
            });
        },
        reject: () => {
            // Revertir el checkbox al estado anterior
            event.target.checked = currentState;
            toast.add({
                severity: 'info',
                summary: 'Cambio Cancelado',
                detail: 'El estado de los respaldos automáticos no ha sido modificado.',
                life: 3000
            });
        }
    });
};

// Función para guardar configuraciones de backup automático
const saveBackupSettings = async () => {
    isSavingSettings.value = true;
    let hasErrors = false;
    
    try {
        // 1. Guardar configuraciones de backup automático si han cambiado
        const settingsChanged = 
            backupSettings.value.auto_backup_enabled !== originalSettings.value.auto_backup_enabled ||
            backupSettings.value.backup_frequency !== originalSettings.value.backup_frequency ||
            backupSettings.value.backup_retention_days !== originalSettings.value.backup_retention_days;
            
        if (settingsChanged) {
            const response = await axios.post('/api/backup-settings', {
                auto_backup_enabled: backupSettings.value.auto_backup_enabled,
                backup_frequency: backupSettings.value.backup_frequency,
                backup_retention_days: backupSettings.value.backup_retention_days
            });

            if (!response.data.success) {
                throw new Error(response.data.message || 'Error al guardar configuración');
            }
            
            // Actualizar el estado original después de guardar exitosamente
            originalSettings.value = { ...backupSettings.value };
            previousBackupState.value = backupSettings.value.auto_backup_enabled;
        }

        // 2. Ejecutar acciones pendientes en orden
        for (const action of pendingActions.value) {
            try {
                switch (action.type) {
                    case 'generate_backup':
                        await executeGenerateBackup();
                        break;
                    case 'cleanup_backups':
                        await executeCleanupBackups();
                        break;
                    case 'delete_backup':
                        await executeDeleteBackup(action.backupId);
                        break;
                }
            } catch (error) {
                hasErrors = true;
                toast.add({
                    severity: 'error',
                    summary: `Error en: ${action.description}`,
                    detail: error.message || 'Error desconocido',
                    life: 5000
                });
            }
        }

        // 3. Recargar lista de respaldos
        await loadBackups();

        // 4. Limpiar cambios pendientes y marcar como guardado
        pendingActions.value = [];
        hasUnsavedChanges.value = false;

        // 5. Mostrar mensaje de éxito y redirigir si todo está bien
        const message = hasErrors 
            ? 'Configuración guardada, pero algunas acciones tuvieron errores. Revise los mensajes anteriores.'
            : 'Todas las configuraciones y acciones se han ejecutado correctamente.';
            
        toast.add({
            severity: hasErrors ? 'warn' : 'success',
            summary: 'Proceso Completado',
            detail: message,
            life: hasErrors ? 6000 : 3000
        });
        
        // 6. Redirigir a settings solo si no hay errores
        if (!hasErrors) {
            // Pequeño delay para que el usuario vea el mensaje de éxito
            setTimeout(() => {
                router.visit(route('settings'));
            }, 1500);
        }
        
    } catch (error) {
        toast.add({
            severity: 'error',
            summary: 'Error al Guardar',
            detail: error.response?.data?.message || error.message || 'No se pudo guardar la configuración',
            life: 5000
        });
    } finally {
        isSavingSettings.value = false;
    }
};

// Funciones auxiliares para ejecutar acciones
const executeGenerateBackup = async () => {
    const response = await axios.post('/api/backups/generate', {}, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    });
    
    if (!response.data.success) {
        throw new Error(response.data.message || 'Error al generar respaldo');
    }
    
    // Pequeño delay para que el archivo esté disponible
    await new Promise(resolve => setTimeout(resolve, 1000));
};

const executeCleanupBackups = async () => {
    const response = await axios.post('/api/backups/cleanup', {}, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    });
    
    if (!response.data.success) {
        throw new Error(response.data.message || 'Error al limpiar respaldos');
    }
};

const executeDeleteBackup = async (backupId) => {
    const response = await axios.delete(`/api/backups/${backupId}`);
    
    if (!response.data.success) {
        throw new Error(response.data.message || 'Error al eliminar respaldo');
    }
};

// Función para manejar salida con cambios sin guardar
const handleBeforeUnload = (event) => {
    if (hasUnsavedChanges.value) {
        event.preventDefault();
        event.returnValue = ''; // Chrome requires returnValue to be set
        return 'Hay cambios sin guardar. ¿Está seguro de que quiere salir?';
    }
};

// Función para navegar con confirmación si hay cambios
const navigateWithConfirmation = (url) => {
    if (hasUnsavedChanges.value || pendingActions.value.length > 0) {
        confirm.require({
            message: 'Tiene cambios sin guardar y acciones pendientes. ¿Desea guardar antes de continuar o salir sin guardar?',
            header: 'Cambios Sin Guardar',
            icon: 'pi pi-exclamation-triangle',
            acceptLabel: 'Guardar y Continuar',
            rejectLabel: 'Salir Sin Guardar',
            acceptClass: 'p-button-success',
            rejectClass: 'p-button-danger',
            accept: async () => {
                await saveBackupSettings();
                router.visit(url);
            },
            reject: () => {
                router.visit(url);
            }
        });
    } else {
        router.visit(url);
    }
};

onMounted(() => {
    loadBackups();
    // Agregar listener para detectar intento de salir del navegador
    window.addEventListener('beforeunload', handleBeforeUnload);
});

onUnmounted(() => {
    window.removeEventListener('beforeunload', handleBeforeUnload);
});
</script>
