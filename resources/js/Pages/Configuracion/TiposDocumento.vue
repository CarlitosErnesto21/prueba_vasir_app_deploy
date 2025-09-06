<template>
    <AuthenticatedLayout>
        <Toast />
        <ConfirmDialog />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header con botón de regreso y botón agregar -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center space-x-4">
                                <Link :href="route('clientes')" 
                                    class="flex items-center text-gray-600 hover:text-red-600 transition-colors duration-200 p-2 rounded-lg hover:bg-gray-100"
                                    title="Regresar a Clientes">
                                    <FontAwesomeIcon :icon="faArrowLeft" class="h-5 w-5" />
                                </Link>
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">Tipos de Documento</h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Gestiona los tipos de documentos disponibles para los clientes
                                    </p>
                                </div>
                            </div>
                            <button 
                                @click="showCreateModal = true"
                                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2"
                            >
                                <i class="pi pi-plus"></i>
                                Agregar Tipo
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow sm:rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <FontAwesomeIcon :icon="faIdCard" class="h-6 w-6 text-blue-600" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total de Tipos</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ totalTipos }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow sm:rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <FontAwesomeIcon :icon="faUsers" class="h-6 w-6 text-green-600" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">En Uso</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ tiposEnUso }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow sm:rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <FontAwesomeIcon :icon="faFileAlt" class="h-6 w-6 text-yellow-600" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Disponibles</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ tiposDisponibles }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de tipos de documento -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="mb-4">
                            <input 
                                v-model="searchTerm"
                                type="text"
                                placeholder="Buscar tipos de documento..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            />
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            ID
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nombre
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Fecha de Creación
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="tipo in filteredTipos" :key="tipo.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ tipo.id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <FontAwesomeIcon :icon="faIdCard" class="h-5 w-5 text-gray-400 mr-2" />

                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ tipo.nombre }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(tipo.created_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button 
                                                    @click="editTipo(tipo)"
                                                    class="text-blue-600 hover:text-blue-900 p-1"
                                                    title="Editar"
                                                >
                                                    <i class="pi pi-pencil"></i>
                                                </button>
                                                <button 
                                                    @click="deleteTipo(tipo)"
                                                    class="text-red-600 hover:text-red-900 p-1"
                                                    title="Eliminar"
                                                >
                                                    <i class="pi pi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div v-if="filteredTipos.length === 0" class="text-center py-8">
                            <FontAwesomeIcon :icon="faFileAlt" class="h-12 w-12 text-gray-400 mx-auto mb-4" />
                            <p class="text-gray-500">No se encontraron tipos de documento</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Crear/Editar Tipo -->
        <div v-if="showCreateModal || showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-96 max-w-full mx-4">
                <h3 class="text-lg font-semibold mb-4">
                    {{ showCreateModal ? 'Crear Nuevo Tipo de Documento' : 'Editar Tipo de Documento' }}
                </h3>
                
                <form @submit.prevent="showCreateModal ? createTipo() : updateTipo()" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Tipo</label>
                        <input 
                            v-model="tipoForm.nombre"
                            type="text"
                            required
                            maxlength="20"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            placeholder="Ej: Cédula, Pasaporte, etc."
                        />
                        <small class="text-gray-500">Máximo 20 caracteres</small>
                    </div>
                    
                    <div class="flex justify-end space-x-3 pt-4">
                        <button 
                            type="button"
                            @click="closeModals"
                            class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50"
                        >
                            Cancelar
                        </button>
                        <button 
                            type="submit"
                            :disabled="loading"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:bg-gray-400 transition-colors duration-200"
                        >
                            {{ loading ? 'Guardando...' : (showCreateModal ? 'Crear Tipo' : 'Actualizar Tipo') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { 
    faIdCard, 
    faUsers,
    faFileAlt,
    faArrowLeft
} from '@fortawesome/free-solid-svg-icons';
import { route } from 'ziggy-js';

// Props desde el controller
const props = defineProps({
    tipoDocumentos: Array
});

// Toast para notificaciones
const toast = useToast();

// Confirm para diálogos de confirmación
const confirm = useConfirm();

// Variables reactivas
const searchTerm = ref('');
const showCreateModal = ref(false);
const showEditModal = ref(false);
const editingTipo = ref(null);
const loading = ref(false);
const tipos = ref(props.tipoDocumentos || []);

const tipoForm = ref({
    nombre: ''
});

// Computed properties
const filteredTipos = computed(() => {
    return tipos.value.filter(tipo => {
        return !searchTerm.value || 
            tipo.nombre.toLowerCase().includes(searchTerm.value.toLowerCase());
    });
});

const totalTipos = computed(() => tipos.value.length);
const tiposEnUso = computed(() => tipos.value.filter(t => t.clientes_count > 0).length);
const tiposDisponibles = computed(() => tipos.value.filter(t => (t.clientes_count || 0) === 0).length);

// Métodos
const loadTipos = async () => {
    try {
        const response = await axios.get('/api/tipo-documentos');
        if (response.data.success) {
            tipos.value = response.data.tipos;
        }
    } catch (error) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Error al cargar los tipos de documento',
            life: 3000
        });
    }
};

const createTipo = async () => {
    loading.value = true;
    try {
        const response = await axios.post('/api/tipo-documentos', tipoForm.value);
        
        if (response.data.success) {
            tipos.value.push(response.data.tipo_documento);
            closeModals();
            toast.add({
                severity: 'success',
                summary: 'Éxito',
                detail: 'Tipo de documento creado exitosamente',
                life: 3000
            });
        }
    } catch (error) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: error.response?.data?.message || 'Error al crear el tipo de documento',
            life: 3000
        });
    } finally {
        loading.value = false;
    }
};

const editTipo = (tipo) => {
    editingTipo.value = tipo;
    tipoForm.value = { nombre: tipo.nombre };
    showEditModal.value = true;
};

const updateTipo = async () => {
    if (!editingTipo.value) return;
    
    loading.value = true;
    try {
        const response = await axios.put(`/api/tipo-documentos/${editingTipo.value.id}`, tipoForm.value);
        
        if (response.data.success) {
            const index = tipos.value.findIndex(t => t.id === editingTipo.value.id);
            if (index !== -1) {
                tipos.value[index] = response.data.tipo_documento;
            }
            closeModals();
            toast.add({
                severity: 'success',
                summary: 'Éxito',
                detail: 'Tipo de documento actualizado exitosamente',
                life: 3000
            });
        }
    } catch (error) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: error.response?.data?.message || 'Error al actualizar el tipo de documento',
            life: 3000
        });
    } finally {
        loading.value = false;
    }
};

const deleteTipo = async (tipo) => {
    confirm.require({
        message: '¿Está seguro de eliminar este tipo de documento? Esta acción no se puede deshacer.',
        header: 'Confirmación de Eliminación',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
        acceptLabel: 'Aceptar',
        rejectLabel: 'Cancelar',
        accept: async () => {
            try {
                const response = await axios.delete(`/api/tipo-documentos/${tipo.id}`);
                if (response.data.success) {
                    const index = tipos.value.findIndex(t => t.id === tipo.id);
                    if (index !== -1) {
                        tipos.value.splice(index, 1);
                    }
                    toast.add({
                        severity: 'success',
                        summary: 'Éxito',
                        detail: 'Tipo de documento eliminado exitosamente',
                        life: 3000
                    });
                }
            } catch (error) {
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: error.response?.data?.message || 'Error al eliminar el tipo de documento',
                    life: 3000
                });
            }
        }
    });
};

const closeModals = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    editingTipo.value = null;
    loading.value = false;
    tipoForm.value = { nombre: '' };
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('es-ES');
};

// Cargar datos al montar el componente
onMounted(() => {
    if (tipos.value.length === 0) {
        loadTipos();
    }
});
</script>
