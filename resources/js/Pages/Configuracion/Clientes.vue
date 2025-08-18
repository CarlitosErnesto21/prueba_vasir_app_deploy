<template>
    <AuthenticatedLayout>
        <div class="container mx-auto px-4 py-8">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-bold text-white flex items-center">
                                <FontAwesomeIcon :icon="faUsers" class="mr-3" />
                                Gestión de Clientes
                            </h1>
                            <p class="text-red-100 mt-2">Administre clientes, perfiles y accesos del sistema</p>
                        </div>
                        <div class="flex space-x-3">
                            <button 
                                @click="goToTiposDocumento"
                                class="bg-white text-red-600 px-4 py-2 rounded-lg font-semibold hover:bg-red-50 transition-colors duration-200 flex items-center"
                            >
                                <FontAwesomeIcon :icon="faIdCard" class="mr-2" />
                                Tipos de Documentos
                            </button>
                            <button 
                                @click="showCreateModal = true"
                                class="bg-white text-red-600 px-4 py-2 rounded-lg font-semibold hover:bg-red-50 transition-colors duration-200 flex items-center"
                            >
                                <FontAwesomeIcon :icon="faUserPlus" class="mr-2" />
                                Agregar Cliente
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <!-- Filtros y Búsqueda -->
                    <div class="mb-6">
                        <div class="flex-1 min-w-64">
                            <input 
                                v-model="searchTerm"
                                type="text" 
                                placeholder="Buscar por nombre, email o identificación..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            />
                        </div>
                    </div>

                    <!-- Estadísticas -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <FontAwesomeIcon :icon="faUsers" class="text-blue-500 text-2xl mr-3" />
                                <div>
                                    <p class="text-blue-600 text-sm font-medium">Total Clientes</p>
                                    <p class="text-2xl font-bold text-blue-800">{{ totalClientes }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <FontAwesomeIcon :icon="faUserCheck" class="text-green-500 text-2xl mr-3" />
                                <div>
                                    <p class="text-green-600 text-sm font-medium">Activos</p>
                                    <p class="text-2xl font-bold text-green-800">{{ activeClientes }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <FontAwesomeIcon :icon="faUserClock" class="text-yellow-500 text-2xl mr-3" />
                                <div>
                                    <p class="text-yellow-600 text-sm font-medium">Pendientes</p>
                                    <p class="text-2xl font-bold text-yellow-800">{{ pendingClientes }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <FontAwesomeIcon :icon="faUserSlash" class="text-red-500 text-2xl mr-3" />
                                <div>
                                    <p class="text-red-600 text-sm font-medium">Inactivos</p>
                                    <p class="text-2xl font-bold text-red-800">{{ inactiveClientes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Clientes -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Usuario
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Contacto
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Rol
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Estado
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Último Acceso
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="user in filteredClientes" :key="user.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img 
                                                    :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=dc2626&color=fff&size=40`"
                                                    class="h-10 w-10 rounded-full"
                                                    :alt="user.name"
                                                />
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                                <div class="text-sm text-gray-500">ID: {{ user.identification }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ user.email }}</div>
                                        <div class="text-sm text-gray-500">{{ user.phone || 'No registrado' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="getRoleClass(user.role)" class="px-2 py-1 text-xs font-semibold rounded-full">
                                            {{ getRoleLabel(user.role) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="getStatusClass(user.status)" class="px-2 py-1 text-xs font-semibold rounded-full">
                                            {{ getStatusLabel(user.status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ user.lastLogin || 'Nunca' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button 
                                                @click="editCliente(user)"
                                                class="text-red-600 hover:text-red-900"
                                                title="Editar"
                                            >
                                                <FontAwesomeIcon :icon="faEdit" />
                                            </button>
                                            <button 
                                                @click="toggleClienteStatus(user)"
                                                :class="user.status === 'active' ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900'"
                                                :title="user.status === 'active' ? 'Desactivar' : 'Activar'"
                                            >
                                                <FontAwesomeIcon :icon="user.status === 'active' ? faUserSlash : faUserCheck" />
                                            </button>
                                            <button 
                                                @click="deleteCliente(user)"
                                                class="text-red-600 hover:text-red-900"
                                                title="Eliminar"
                                            >
                                                <FontAwesomeIcon :icon="faTrash" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        <!-- Modal Crear/Editar Cliente -->
            <div v-if="showCreateModal || showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 w-96 max-w-full mx-4 max-h-screen overflow-y-auto">
                    <h3 class="text-lg font-semibold mb-4">
                        {{ showCreateModal ? 'Crear Nuevo Cliente' : 'Editar Cliente' }}
                    </h3>
                    
                    <form @submit.prevent="showCreateModal ? createCliente() : updateCliente()" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo</label>
                            <input 
                                v-model="userForm.name"
                                type="text"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input 
                                v-model="userForm.email"
                                type="email"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Identificación</label>
                            <input 
                                v-model="userForm.identification"
                                type="text"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                            <input 
                                v-model="userForm.phone"
                                type="tel"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                            <input 
                                v-model="userForm.address"
                                type="text"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Nacimiento</label>
                            <input 
                                v-model="userForm.birth_date"
                                type="date"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Género</label>
                            <select 
                                v-model="userForm.gender"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            >
                                <option value="">Seleccione género</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Documento</label>
                            <select 
                                v-model="userForm.tipo_documento_id"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            >
                                <option value="">Seleccione tipo de documento</option>
                                <option v-for="tipo in tiposDocumento" :key="tipo.id" :value="tipo.id">
                                    {{ tipo.nombre }}
                                </option>
                            </select>
                        </div>
                        
                        <div v-if="showCreateModal">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                            <input 
                                v-model="userForm.password"
                                type="password"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            />
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
                                {{ loading ? 'Guardando...' : (showCreateModal ? 'Crear Cliente' : 'Actualizar Cliente') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { 
    faUsers, 
    faUserPlus,
    faUserCheck, 
    faUserSlash, 
    faUserClock, 
    faEdit, 
    faTrash,
    faIdCard
} from '@fortawesome/free-solid-svg-icons';

const searchTerm = ref('');
const showCreateModal = ref(false);
const showEditModal = ref(false);
const editingUser = ref(null);
const loading = ref(false);
const clientes = ref([]);
const tiposDocumento = ref([]);

const userForm = ref({
    name: '',
    email: '',
    identification: '',
    phone: '',
    address: '',
    birth_date: '',
    gender: '',
    tipo_documento_id: '',
    password: ''
});

// Cargar datos desde el backend
const loadClientes = async () => {
    try {
        const response = await axios.get('/api/clientes');
        if (response.data.success) {
            clientes.value = response.data.clientes;
        }
    } catch (error) {
        console.error('Error loading clientes:', error);
        alert('Error al cargar los clientes');
    }
};

const loadTiposDocumento = async () => {
    try {
        const response = await axios.get('/api/tipos-documento-options');
        if (response.data.success) {
            tiposDocumento.value = response.data.tipos;
        }
    } catch (error) {
        console.error('Error loading tipos documento:', error);
    }
};

// Cargar datos al montar el componente
onMounted(() => {
    loadClientes();
    loadTiposDocumento();
});

const filteredClientes = computed(() => {
    return clientes.value.filter(cliente => {
        return !searchTerm.value || 
            cliente.name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
            cliente.email.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
            cliente.identification.toLowerCase().includes(searchTerm.value.toLowerCase());
    });
});

const totalClientes = computed(() => clientes.value.length);
const activeClientes = computed(() => clientes.value.filter(c => c.status === 'active').length);
const pendingClientes = computed(() => clientes.value.filter(c => c.status === 'pending').length);
const inactiveClientes = computed(() => clientes.value.filter(c => c.status === 'inactive').length);

const getRoleClass = (role) => {
    const classes = {
        admin: 'bg-purple-100 text-purple-800',
        empleado: 'bg-blue-100 text-blue-800',
        cliente: 'bg-green-100 text-green-800'
    };
    return classes[role] || 'bg-gray-100 text-gray-800';
};

const getRoleLabel = (role) => {
    const labels = {
        admin: 'Administrador',
        empleado: 'Empleado',
        cliente: 'Cliente'
    };
    return labels[role] || role;
};

const getStatusClass = (status) => {
    const classes = {
        active: 'bg-green-100 text-green-800',
        inactive: 'bg-red-100 text-red-800',
        pending: 'bg-yellow-100 text-yellow-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const getStatusLabel = (status) => {
    const labels = {
        active: 'Activo',
        inactive: 'Inactivo',
        pending: 'Pendiente'
    };
    return labels[status] || status;
};

// Función para navegar a tipos de documento
const goToTiposDocumento = () => {
    router.visit('/configuracion/tipos-documento');
};

// Función para crear nuevo cliente
const createCliente = async () => {
    loading.value = true;
    try {
        const response = await axios.post('/api/clientes', {
            ...userForm.value,
            role: 'cliente' // Los clientes siempre tienen rol 'cliente'
        });
        
        if (response.data.success) {
            // Agregar el nuevo cliente a la lista
            clientes.value.push(response.data.cliente);
            closeModals();
            alert('Cliente creado exitosamente');
        }
    } catch (error) {
        console.error('Error al crear cliente:', error);
        alert(error.response?.data?.message || 'Error al crear el cliente');
    } finally {
        loading.value = false;
    }
};

const editCliente = (cliente) => {
    editingUser.value = cliente;
    userForm.value = { 
        ...cliente,
        tipo_documento_id: cliente.tipo_documento_id || ''
    };
    showEditModal.value = true;
};

const updateCliente = async () => {
    if (!editingUser.value) return;
    
    loading.value = true;
    try {
        const response = await axios.put(`/api/clientes/${editingUser.value.id}`, userForm.value);
        
        if (response.data.success) {
            // Actualizar el cliente en la lista
            const index = clientes.value.findIndex(u => u.id === editingUser.value.id);
            if (index !== -1) {
                clientes.value[index] = response.data.cliente;
            }
            closeModals();
            alert('Cliente actualizado exitosamente');
        }
    } catch (error) {
        console.error('Error al actualizar cliente:', error);
        alert(error.response?.data?.message || 'Error al actualizar el cliente');
    } finally {
        loading.value = false;
    }
};

const toggleClienteStatus = async (cliente) => {
    const action = cliente.status === 'active' ? 'desactivar' : 'activar';
    if (confirm(`¿Está seguro de ${action} este cliente?`)) {
        try {
            const response = await axios.patch(`/api/clientes/${cliente.id}/toggle-status`);
            if (response.data.success) {
                cliente.status = response.data.cliente.status;
                alert(`Cliente ${action}do exitosamente`);
            }
        } catch (error) {
            console.error('Error al cambiar estado del cliente:', error);
            alert('Error al cambiar el estado del cliente');
        }
    }
};

const deleteCliente = async (cliente) => {
    if (confirm('¿Está seguro de eliminar este cliente? Esta acción no se puede deshacer.')) {
        try {
            const response = await axios.delete(`/api/clientes/${cliente.id}`);
            if (response.data.success) {
                const index = clientes.value.findIndex(u => u.id === cliente.id);
                if (index !== -1) {
                    clientes.value.splice(index, 1);
                }
                alert('Cliente eliminado exitosamente');
            }
        } catch (error) {
            console.error('Error al eliminar cliente:', error);
            alert('Error al eliminar el cliente');
        }
    }
};

const closeModals = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    editingUser.value = null;
    loading.value = false;
    userForm.value = {
        name: '',
        email: '',
        identification: '',
        phone: '',
        address: '',
        birth_date: '',
        gender: '',
        tipo_documento_id: '',
        password: ''
    };
};
</script>
