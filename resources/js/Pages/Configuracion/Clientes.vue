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
                        <button 
                            @click="showCreateModal = true"
                            class="bg-white text-red-600 px-4 py-2 rounded-lg font-semibold hover:bg-red-50 transition-colors duration-200 flex items-center"
                        >
                            <FontAwesomeIcon :icon="faUserPlus" class="mr-2" />
                            Nuevo Usuario
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <!-- Filtros y Búsqueda -->
                    <div class="mb-6 flex flex-wrap gap-4">
                        <div class="flex-1 min-w-64">
                            <input 
                                v-model="searchTerm"
                                type="text" 
                                placeholder="Buscar por nombre, email o identificación..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            />
                        </div>
                        <select 
                            v-model="statusFilter"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        >
                            <option value="">Todos los estados</option>
                            <option value="active">Activo</option>
                            <option value="inactive">Inactivo</option>
                            <option value="pending">Pendiente</option>
                        </select>
                        <select 
                            v-model="roleFilter"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        >
                            <option value="">Todos los roles</option>
                            <option value="admin">Administrador</option>
                            <option value="empleado">Empleado</option>
                            <option value="cliente">Cliente</option>
                        </select>
                    </div>

                    <!-- Estadísticas -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <FontAwesomeIcon :icon="faUsers" class="text-blue-500 text-2xl mr-3" />
                                <div>
                                    <p class="text-blue-600 text-sm font-medium">Total Clientes</p>
                                    <p class="text-2xl font-bold text-blue-800">{{ totalUsers }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <FontAwesomeIcon :icon="faUserCheck" class="text-green-500 text-2xl mr-3" />
                                <div>
                                    <p class="text-green-600 text-sm font-medium">Activos</p>
                                    <p class="text-2xl font-bold text-green-800">{{ activeUsers }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <FontAwesomeIcon :icon="faUserClock" class="text-yellow-500 text-2xl mr-3" />
                                <div>
                                    <p class="text-yellow-600 text-sm font-medium">Pendientes</p>
                                    <p class="text-2xl font-bold text-yellow-800">{{ pendingUsers }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <FontAwesomeIcon :icon="faUserSlash" class="text-red-500 text-2xl mr-3" />
                                <div>
                                    <p class="text-red-600 text-sm font-medium">Inactivos</p>
                                    <p class="text-2xl font-bold text-red-800">{{ inactiveUsers }}</p>
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
                                <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img 
                                                    :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=ed1c24&color=fff&size=40`"
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
                                                @click="editUser(user)"
                                                class="text-blue-600 hover:text-blue-900"
                                                title="Editar"
                                            >
                                                <FontAwesomeIcon :icon="faEdit" />
                                            </button>
                                            <button 
                                                @click="toggleUserStatus(user)"
                                                :class="user.status === 'active' ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900'"
                                                :title="user.status === 'active' ? 'Desactivar' : 'Activar'"
                                            >
                                                <FontAwesomeIcon :icon="user.status === 'active' ? faUserSlash : faUserCheck" />
                                            </button>
                                            <button 
                                                @click="deleteUser(user)"
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

            <!-- Modal Crear/Editar Usuario -->
            <div v-if="showCreateModal || showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 w-96 max-w-full mx-4 max-h-screen overflow-y-auto">
                    <h3 class="text-lg font-semibold mb-4">
                        {{ showCreateModal ? 'Crear Nuevo Usuario' : 'Editar Usuario' }}
                    </h3>
                    
                    <form @submit.prevent="showCreateModal ? createUser() : updateUser()" class="space-y-4">
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
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                            <select 
                                v-model="userForm.role"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            >
                                <option value="admin">Administrador</option>
                                <option value="empleado">Empleado</option>
                                <option value="cliente">Cliente</option>
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
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                            >
                                {{ showCreateModal ? 'Crear' : 'Actualizar' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { 
    faUsers, 
    faUserPlus, 
    faUserCheck, 
    faUserSlash, 
    faUserClock, 
    faEdit, 
    faTrash 
} from '@fortawesome/free-solid-svg-icons';

const searchTerm = ref('');
const statusFilter = ref('');
const roleFilter = ref('');
const showCreateModal = ref(false);
const showEditModal = ref(false);
const editingUser = ref(null);

const userForm = ref({
    name: '',
    email: '',
    identification: '',
    phone: '',
    role: 'cliente',
    password: ''
});

// Datos de ejemplo - reemplazar con datos reales del backend
const users = ref([
    {
        id: 1,
        name: 'Carlos Ernesto',
        email: 'carlos@vasir.com',
        identification: '001-010195-1001A',
        phone: '+505 8888-8888',
        role: 'admin',
        status: 'active',
        lastLogin: '2025-08-17 14:30'
    },
    {
        id: 2,
        name: 'Maria González',
        email: 'maria@vasir.com',
        identification: '001-050290-1002B',
        phone: '+505 7777-7777',
        role: 'empleado',
        status: 'active',
        lastLogin: '2025-08-17 10:15'
    },
    {
        id: 3,
        name: 'Juan Pérez',
        email: 'juan@cliente.com',
        identification: '001-120185-1003C',
        phone: '+505 6666-6666',
        role: 'cliente',
        status: 'pending',
        lastLogin: null
    }
]);

const filteredUsers = computed(() => {
    return users.value.filter(user => {
        const matchesSearch = !searchTerm.value || 
            user.name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
            user.email.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
            user.identification.toLowerCase().includes(searchTerm.value.toLowerCase());
        
        const matchesStatus = !statusFilter.value || user.status === statusFilter.value;
        const matchesRole = !roleFilter.value || user.role === roleFilter.value;
        
        return matchesSearch && matchesStatus && matchesRole;
    });
});

const totalUsers = computed(() => users.value.length);
const activeUsers = computed(() => users.value.filter(u => u.status === 'active').length);
const pendingUsers = computed(() => users.value.filter(u => u.status === 'pending').length);
const inactiveUsers = computed(() => users.value.filter(u => u.status === 'inactive').length);

const getRoleClass = (role) => {
    const classes = {
        admin: 'bg-red-100 text-red-800',
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

const createUser = () => {
    // Lógica para crear usuario
    const newUser = {
        id: users.value.length + 1,
        ...userForm.value,
        status: 'pending',
        lastLogin: null
    };
    users.value.push(newUser);
    closeModals();
    alert('Usuario creado exitosamente');
};

const editUser = (user) => {
    editingUser.value = user;
    userForm.value = { ...user };
    showEditModal.value = true;
};

const updateUser = () => {
    if (editingUser.value) {
        Object.assign(editingUser.value, userForm.value);
        closeModals();
        alert('Usuario actualizado exitosamente');
    }
};

const toggleUserStatus = (user) => {
    const action = user.status === 'active' ? 'desactivar' : 'activar';
    if (confirm(`¿Está seguro de ${action} este usuario?`)) {
        user.status = user.status === 'active' ? 'inactive' : 'active';
    }
};

const deleteUser = (user) => {
    if (confirm('¿Está seguro de eliminar este usuario? Esta acción no se puede deshacer.')) {
        const index = users.value.findIndex(u => u.id === user.id);
        if (index !== -1) {
            users.value.splice(index, 1);
        }
    }
};

const closeModals = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    editingUser.value = null;
    userForm.value = {
        name: '',
        email: '',
        identification: '',
        phone: '',
        role: 'cliente',
        password: ''
    };
};
</script>
