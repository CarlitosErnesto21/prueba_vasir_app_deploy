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
                            <Link :href="route('tipodocumentos')" 
                                class="p-button-sm p-button-info w-full sm:w-auto rounded-lg">
                                <FontAwesomeIcon :icon="faIdCard" size="lg" class="drop-shadow-md"/>
                                <span class="ml-2 whitespace-nowrap">Agregar tipo documento</span>
                            </Link>
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
                    <div class="grid grid-cols-1 gap-4 mb-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <FontAwesomeIcon :icon="faUsers" class="text-blue-500 text-2xl mr-3" />
                                <div>
                                    <p class="text-blue-600 text-sm font-medium">Total Clientes</p>
                                    <p class="text-2xl font-bold text-blue-800">{{ totalClientes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Clientes -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Apellido</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="cliente in filteredClientes" :key="cliente.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ cliente.nombre }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ cliente.apellido }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ cliente.telefono }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ cliente.email || (cliente.user && cliente.user.email) || 'No registrado' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button @click="verDetalles(cliente)" class="text-blue-600 hover:text-blue-900" title="Ver Detalles">
                                            <FontAwesomeIcon :icon="faIdCard" />
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        <!-- Modal Detalles Cliente -->
            <div v-if="showDetallesModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 w-96 max-w-full mx-4 max-h-screen overflow-y-auto">
                    <h3 class="text-lg font-semibold mb-4">Detalles del Cliente</h3>
                    <div v-if="clienteSeleccionado">
                        <p><strong>Nombre:</strong> {{ clienteSeleccionado.nombre }}</p>
                        <p><strong>Apellido:</strong> {{ clienteSeleccionado.apellido }}</p>
                        <p><strong>Género:</strong> {{ clienteSeleccionado.genero }}</p>
                        <p><strong>Fecha de Nacimiento:</strong> {{ clienteSeleccionado.fecha_nacimiento }}</p>
                        <p><strong>Tipo de Documento:</strong> 
                            {{ clienteSeleccionado.tipo_documento_nombre || 
                            (clienteSeleccionado.tipo_documento && clienteSeleccionado.tipo_documento.nombre) || 
                            'No registrado' }}
                        </p>
                        <p><strong>Número de Identificación:</strong> {{ clienteSeleccionado.numero_identificacion }}</p>
                        <p><strong>Teléfono:</strong> {{ clienteSeleccionado.telefono }}</p>
                        <p><strong>Correo:</strong> {{ clienteSeleccionado.email || (clienteSeleccionado.user && clienteSeleccionado.user.email) || 'No registrado' }}</p>
                        <p><strong>Dirección:</strong> {{ clienteSeleccionado.direccion }}</p>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="button" @click="closeDetallesModal" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
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
const showDetallesModal = ref(false);
const clienteSeleccionado = ref(null);
const loading = ref(false);
const clientes = ref([]);
const tipoDocumentos = ref([]);

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

const loadTipoDocumentos = async () => {
    try {
        const response = await axios.get('/api/tipo-documentos');
        if (response.data.success) {
            tipoDocumentos.value = response.data.tipos;
        }
    } catch (error) {
        console.error('Error loading tipos documento:', error);
    }
};

// Cargar datos al montar el componente
onMounted(() => {
    loadClientes();
    loadTipoDocumentos();
});

const filteredClientes = computed(() => {
    return clientes.value.filter(cliente => {
        return !searchTerm.value || 
            cliente.nombre.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
            cliente.apellido.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
            (cliente.email && cliente.email.toLowerCase().includes(searchTerm.value.toLowerCase())) ||
            (cliente.telefono && cliente.telefono.toLowerCase().includes(searchTerm.value.toLowerCase()));
    });
});

const totalClientes = computed(() => clientes.value.length);

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

// Función para ver detalles del cliente
const verDetalles = (cliente) => {
    clienteSeleccionado.value = cliente;
    showDetallesModal.value = true;
};

const closeDetallesModal = () => {
    showDetallesModal.value = false;
    clienteSeleccionado.value = null;
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
