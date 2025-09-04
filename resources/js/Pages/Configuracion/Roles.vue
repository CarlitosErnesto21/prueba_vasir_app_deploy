<template>
    <AuthenticatedLayout>
        <!-- Toast Component for notifications -->
        <Toast />
        
        <div class="container mx-auto px-4 py-8">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-bold text-white flex items-center">
                                <FontAwesomeIcon :icon="faUsers" class="mr-3" />
                                Gestión de Usuarios Internos
                            </h1>
                            <p class="text-red-100 mt-2">Gestione empleados, administradores y sus permisos</p>
                        </div>
                        <button 
                            @click="openCreateUserModal"
                            class="bg-white text-red-600 px-4 py-2 rounded-lg font-semibold hover:bg-red-50 transition-colors duration-200 flex items-center"
                        >
                            <FontAwesomeIcon :icon="faUserPlus" class="mr-2" />
                            Nuevo Usuario
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-red-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <FontAwesomeIcon :icon="faUsers" class="h-8 w-8 text-red-600" />
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-red-600">Total Usuarios</p>
                                    <p class="text-2xl font-bold text-red-900">{{ users.length }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <FontAwesomeIcon :icon="faShieldHalved" class="h-8 w-8 text-blue-600" />
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-blue-600">Administradores</p>
                                    <p class="text-2xl font-bold text-blue-900">{{ getUserCountByRole('admin') }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <FontAwesomeIcon :icon="faUsers" class="h-8 w-8 text-green-600" />
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-green-600">Empleados</p>
                                    <p class="text-2xl font-bold text-green-900">{{ getUserCountByRole('empleado') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros -->
                    <div class="mb-6 flex flex-wrap gap-4">
                        <div class="flex-1 min-w-64">
                            <input 
                                v-model="searchTerm"
                                type="text" 
                                placeholder="Buscar por nombre o email..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            />
                        </div>
                        <div class="relative">
                            <select 
                                v-model="selectedRole"
                                class="px-4 py-2 pr-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white cursor-pointer w-full"
                                style="appearance: none; -webkit-appearance: none; -moz-appearance: none;"
                            >
                                <option value="">Todos los roles</option>
                                <option value="admin">Administrador</option>
                                <option value="empleado">Empleado</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <button 
                            @click="refreshData"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                        >
                            Actualizar
                        </button>
                    </div>

                    <!-- Lista de Usuarios -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Usuario
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Roles
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Fecha Registro
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Última Actualización
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="user in paginatedUsers" :key="user.id" class="hover:bg-gray-50">
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
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ user.email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-wrap gap-1">
                                            <span 
                                                v-for="role in user.roles" 
                                                :key="role"
                                                :class="getRoleColor(role)" 
                                                class="px-2 py-1 text-xs font-semibold rounded-full"
                                            >
                                                {{ role }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ user.created_at }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ user.updated_at }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <button 
                                            @click="openEditRolesAndPermissionsModal(user)"
                                            class="text-orange-600 hover:text-orange-900 transition-colors"
                                            title="Editar Roles y Permisos"
                                        >
                                            <FontAwesomeIcon :icon="faEdit" class="h-5 w-5" />
                                        </button>
                                        <button 
                                            @click="showUserPermissions(user)"
                                            class="text-blue-600 hover:text-blue-900 transition-colors"
                                            title="Ver Permisos"
                                        >
                                            <FontAwesomeIcon :icon="faEye" class="h-5 w-5" />
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="filteredUsers.length > 0" class="mt-6 flex items-center justify-between">
                        <div class="flex items-center text-sm text-gray-500">
                            <span>Mostrando {{ paginationInfo.start }} - {{ paginationInfo.end }} de {{ paginationInfo.total }} usuarios</span>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <!-- Items per page selector -->
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-500">Mostrar:</span>
                                <div class="relative">
                                    <select 
                                        v-model="itemsPerPage" 
                                        @change="currentPage = 1"
                                        class="appearance-none border border-gray-300 rounded px-2 py-1 pr-6 text-sm bg-white focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500"
                                    >
                                        <option :value="5">5</option>
                                        <option :value="10">10</option>
                                        <option :value="15">15</option>
                                        <option :value="20">20</option>
                                    </select>
                                    <!-- Custom dropdown arrow -->
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Pagination buttons -->
                            <div class="flex items-center space-x-1">
                                <button 
                                    @click="goToPage(1)"
                                    :disabled="currentPage === 1"
                                    class="px-2 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    ⟪
                                </button>
                                <button 
                                    @click="goToPage(currentPage - 1)"
                                    :disabled="currentPage === 1"
                                    class="px-2 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    ⟨
                                </button>
                                
                                <span v-for="page in visiblePages" :key="page">
                                    <button 
                                        v-if="page !== '...'"
                                        @click="goToPage(page)"
                                        :class="page === currentPage ? 'bg-red-600 text-white' : 'text-gray-700 hover:bg-gray-50'"
                                        class="px-3 py-1 text-sm border border-gray-300 rounded"
                                    >
                                        {{ page }}
                                    </button>
                                    <span v-else class="px-2 text-gray-500">...</span>
                                </span>
                                
                                <button 
                                    @click="goToPage(currentPage + 1)"
                                    :disabled="currentPage === totalPages"
                                    class="px-2 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    ⟩
                                </button>
                                <button 
                                    @click="goToPage(totalPages)"
                                    :disabled="currentPage === totalPages"
                                    class="px-2 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    ⟫
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- No results message -->
                    <div v-if="filteredUsers.length === 0" class="text-center py-8">
                        <p class="text-gray-500">No se encontraron usuarios con los filtros aplicados.</p>
                    </div>
                </div>
            </div>

            <!-- Modal para Editar Roles y Permisos -->
            <div v-if="showEditRolesAndPermissionsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 w-[600px] max-w-full mx-4 max-h-[90vh] overflow-y-auto">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <FontAwesomeIcon :icon="faEdit" class="mr-2 text-red-600" />
                        {{ editModalTitle }}
                    </h3>
                    
                    <div class="mb-6">
                        <p class="text-gray-600 mb-1">Usuario: <strong>{{ currentUser?.name }}</strong></p>
                        <p class="text-gray-600 mb-3">Email: <strong>{{ currentUser?.email }}</strong></p>
                        
                        <!-- Campos editables del empleado -->
                        <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-800 mb-3">Información del Empleado</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Cargo</label>
                                    <input 
                                        v-model="currentEmployeeData.cargo"
                                        type="text"
                                        maxlength="25"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-sm"
                                        placeholder="Ej: Ejecutivo de ventas, Administrador, etc."
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                                    <input 
                                        v-model="currentEmployeeData.telefono"
                                        type="tel"
                                        maxlength="8"
                                        pattern="[0-9]{8}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-sm"
                                        placeholder="12345678"
                                    />
                                </div>
                            </div>
                        </div>
                        
                        <!-- Mostrar rol actual -->
                        <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                            <p class="text-sm font-medium text-blue-800 mb-2">Rol Actual:</p>
                            <div class="flex flex-wrap gap-1">
                                <span 
                                    v-for="role in currentUser?.roles" 
                                    :key="role"
                                    :class="getRoleColor(role)" 
                                    class="px-2 py-1 text-xs font-semibold rounded-full"
                                >
                                    {{ role }}
                                </span>
                                <span v-if="!currentUser?.roles?.length" class="px-2 py-1 text-xs bg-gray-200 text-gray-600 rounded-full">
                                    Sin rol asignado
                                </span>
                            </div>
                        </div>
                        
                        <!-- Seleccionar nuevo rol -->
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cambiar Rol:</label>
                        <div class="space-y-2 mb-4">
                            <div v-for="role in availableRoles" :key="role" class="flex items-center">
                                <input 
                                    :id="`role-${role}`"
                                    type="radio" 
                                    :value="role"
                                    v-model="selectedUserRole"
                                    name="userRole"
                                    class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300"
                                />
                                <label :for="`role-${role}`" class="ml-2 text-sm text-gray-700 capitalize">
                                    {{ role }}
                                </label>
                            </div>
                        </div>
                        
                        <!-- Gestión de Permisos -->
                        <div class="border-t pt-4">
                            <p class="text-sm text-gray-600 mb-3">Los roles son organizacionales. Los permisos se gestionan manualmente.</p>
                            
                            <!-- Permisos Asignados -->
                            <div v-if="currentUserPermissions.length > 0" class="mb-4">
                                <h4 class="text-sm font-medium text-green-700 mb-2 flex items-center">
                                    <FontAwesomeIcon :icon="faCheck" class="mr-2" />
                                    Permisos Asignados ({{ currentUserPermissions.length }})
                                </h4>
                                <div class="space-y-2 max-h-32 overflow-y-auto">
                                    <div 
                                        v-for="permission in currentUserPermissions" 
                                        :key="permission"
                                        class="flex items-center justify-between p-2 bg-green-50 border border-green-200 rounded"
                                    >
                                        <div class="flex items-center">
                                            <FontAwesomeIcon :icon="faCheck" class="text-green-600 mr-2" />
                                            <span class="text-sm text-green-800 font-medium">{{ permission }}</span>
                                        </div>
                                        <button 
                                            @click="removePermissionFromUser(permission)"
                                            class="text-red-600 hover:text-red-800 transition-colors"
                                            :disabled="currentUserPermissions.length === 1"
                                            :title="currentUserPermissions.length === 1 ? 'No se puede eliminar el último permiso' : 'Eliminar permiso'"
                                        >
                                            <FontAwesomeIcon :icon="faTimes" />
                                        </button>
                                    </div>
                                </div>
                                <div v-if="currentUserPermissions.length === 1" class="mt-2 flex items-center text-orange-600 text-xs">
                                    <FontAwesomeIcon :icon="faWarning" class="mr-1" />
                                    No se puede eliminar el último permiso. El usuario debe tener al menos uno.
                                </div>
                            </div>
                            
                            <!-- Permisos Disponibles para Asignar -->
                            <div v-if="availablePermissionsForUser.length > 0">
                                <h4 class="text-sm font-medium text-blue-700 mb-2 flex items-center">
                                    <FontAwesomeIcon :icon="faPlus" class="mr-2" />
                                    Asignar Nuevos Permisos ({{ availablePermissionsForUser.length }})
                                </h4>
                                <div class="space-y-2 max-h-32 overflow-y-auto">
                                    <div 
                                        v-for="permission in availablePermissionsForUser" 
                                        :key="permission.name"
                                        class="flex items-center justify-between p-2 bg-white border border-gray-200 rounded hover:bg-blue-50 transition-colors"
                                    >
                                        <span class="text-sm text-gray-700">{{ permission.name }}</span>
                                        <button 
                                            @click="assignPermissionToUser(permission.name)"
                                            class="text-blue-600 hover:text-blue-800 transition-colors"
                                            title="Asignar permiso"
                                        >
                                            <FontAwesomeIcon :icon="faPlus" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div v-if="availablePermissionsForUser.length === 0 && currentUserPermissions.length > 0" class="text-center py-3 text-gray-500 text-sm">
                                ✅ El usuario tiene todos los permisos disponibles
                            </div>
                        </div>
                    </div>
                    
                                        <div class="flex justify-end space-x-3">
                        <button 
                            @click="attemptCloseEditModal"
                            class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                        >
                            Cerrar
                        </button>
                        <button 
                            @click="saveAllChanges"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="loading || !hasChanges"
                        >
                            <span v-if="loading">Guardando...</span>
                            <span v-else>{{ saveButtonText }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal para Ver Solo Permisos (simplificado) -->
            <div v-if="showPermissionsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 w-96 max-w-full mx-4">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <FontAwesomeIcon :icon="faShieldHalved" class="mr-2 text-blue-600" />
                        Permisos del Usuario
                    </h3>
                    
                    <div class="mb-4">
                        <p class="text-gray-600 mb-2">Usuario: <strong>{{ currentUser?.name }}</strong></p>
                        <p class="text-gray-600 mb-4">Roles: 
                            <span 
                                v-for="role in currentUser?.roles" 
                                :key="role"
                                :class="getRoleColor(role)" 
                                class="px-2 py-1 text-xs font-semibold rounded-full ml-1"
                            >
                                {{ role }}
                            </span>
                        </p>
                        
                        <label class="block text-sm font-medium text-gray-700 mb-2">Permisos:</label>
                        <div v-if="loading" class="text-center py-4">
                            <p class="text-gray-500">Cargando permisos...</p>
                        </div>
                        <div v-else-if="currentUserPermissions.length > 0" class="space-y-2 max-h-48 overflow-y-auto">
                            <div 
                                v-for="permission in currentUserPermissions" 
                                :key="permission"
                                class="flex items-center p-2 bg-gray-50 rounded"
                            >
                                <FontAwesomeIcon :icon="faCheck" class="text-green-600 mr-2" />
                                <span class="text-sm text-gray-700">{{ permission }}</span>
                            </div>
                        </div>
                        <div v-else class="text-center py-4">
                            <p class="text-gray-500">No se encontraron permisos para este usuario.</p>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button 
                            @click="closePermissionsModal"
                            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
                        >
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para Crear Usuario -->
        <div v-if="showCreateUserModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-[700px] max-w-full mx-4 max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Crear Nuevo Usuario Interno</h3>
                
                <form @submit.prevent="createInternalUser" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo</label>
                        <input 
                            v-model="newUserForm.name"
                            type="text"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            placeholder="Ingrese el nombre completo"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input 
                            v-model="newUserForm.email"
                            type="email"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            placeholder="usuario@ejemplo.com"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                        <div class="relative">
                            <input 
                                v-model="newUserForm.password"
                                :type="showNewUserPassword ? 'text' : 'password'"
                                required
                                minlength="8"
                                class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                placeholder="Mínimo 8 caracteres"
                            />
                            <button
                                type="button"
                                @click="showNewUserPassword = !showNewUserPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors"
                            >
                                <svg v-if="!showNewUserPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                        <select 
                            v-model="newUserForm.role"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        >
                            <option value="empleado">Empleado</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>

                    <!-- Campos adicionales para empleados -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cargo</label>
                            <input 
                                v-model="newUserForm.cargo"
                                type="text"
                                required
                                maxlength="25"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                placeholder="Ej: Ejecutivo de ventas, Administrador, etc."
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                            <input 
                                v-model="newUserForm.telefono"
                                type="tel"
                                required
                                maxlength="8"
                                pattern="[0-9]{8}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                placeholder="12345678"
                            />
                        </div>
                    </div>

                    <!-- Sección de Permisos -->
                    <div class="border-t pt-4">
                        <div class="mb-3">
                            <h4 class="text-md font-semibold text-gray-800 mb-2 flex items-center">
                                <FontAwesomeIcon :icon="faKey" class="mr-2 text-green-600" />
                                Asignar Permisos al Usuario
                            </h4>
                            <p class="text-sm text-gray-600">
                                Seleccione exactamente qué permisos tendrá este usuario. El rol es solo organizacional.
                            </p>
                        </div>

                        <!-- Lista de permisos disponibles -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Permisos Disponibles:
                            </label>
                            <div class="max-h-60 overflow-y-auto border rounded p-3 bg-gray-50">
                                <div v-if="permissions.length > 0" class="space-y-2">
                                    <div 
                                        v-for="permission in permissions" 
                                        :key="permission.id"
                                        class="flex items-center p-2 rounded border hover:bg-gray-100 transition-colors"
                                        :class="newUserForm.selectedPermissions.includes(permission.name) ? 'bg-green-50 border-green-200' : 'bg-white border-gray-200'"
                                    >
                                        <input 
                                            :id="`permission-${permission.id}`"
                                            type="checkbox" 
                                            :value="permission.name"
                                            v-model="newUserForm.selectedPermissions"
                                            :disabled="newUserForm.selectedPermissions.length === 1 && newUserForm.selectedPermissions.includes(permission.name)"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                                        />
                                        <label 
                                            :for="`permission-${permission.id}`" 
                                            class="ml-3 text-sm cursor-pointer"
                                            :class="newUserForm.selectedPermissions.includes(permission.name) ? 'text-green-800 font-medium' : 'text-gray-700'"
                                        >
                                            {{ permission.name }}
                                        </label>
                                    </div>
                                </div>
                                <div v-else class="text-center py-3 text-gray-500">
                                    No hay permisos disponibles
                                </div>
                            </div>
                            
                            <!-- Contador y validación -->
                            <div class="mt-3 flex items-center justify-between">
                                <div class="text-sm">
                                    <span class="font-medium text-gray-700">
                                        Permisos seleccionados: {{ newUserForm.selectedPermissions.length }}
                                    </span>
                                    <span v-if="newUserForm.selectedPermissions.length === 0" class="text-red-600 ml-2">
                                        ⚠️ Debe seleccionar al menos un permiso
                                    </span>
                                    <span v-else class="text-green-600 ml-2">
                                        ✅ Configuración válida
                                    </span>
                                </div>
                                
                                <!-- Botones de acción rápida -->
                                <div class="flex gap-2">
                                    <button 
                                        type="button"
                                        @click="clearAllPermissions"
                                        class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200 transition-colors"
                                        :disabled="newUserForm.selectedPermissions.length === 0"
                                    >
                                        Limpiar
                                    </button>
                                    <button 
                                        type="button"
                                        @click="selectAllPermissions"
                                        class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded hover:bg-green-200 transition-colors"
                                    >
                                        Seleccionar todos
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button 
                            type="button"
                            @click="closeCreateUserModal"
                            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
                        >
                            Cancelar
                        </button>
                        <button 
                            type="submit"
                            :disabled="loading || newUserForm.selectedPermissions.length === 0"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ loading ? 'Creando...' : 'Crear Usuario' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal para Gestionar Permisos -->
        <div v-if="showManagePermissionsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-[600px] max-w-full mx-4 max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-semibold mb-4 flex items-center">
                    <FontAwesomeIcon :icon="faKey" class="mr-2 text-green-600" />
                    Gestionar Permisos de Usuario
                </h3>
                
                <div class="mb-4">
                    <p class="text-gray-600 mb-2">Usuario: <strong>{{ currentUser?.name }}</strong></p>
                    <p class="text-gray-600 mb-4">Rol: 
                        <span 
                            v-for="role in currentUser?.roles" 
                            :key="role"
                            :class="getRoleColor(role)" 
                            class="px-2 py-1 text-xs font-semibold rounded-full ml-1"
                        >
                            {{ role }}
                        </span>
                    </p>
                    <p class="text-sm text-gray-500">
                        Los roles son organizacionales. Los permisos se gestionan manualmente.
                    </p>
                </div>

                <div class="space-y-6">
                    <!-- Permisos Asignados -->
                    <div>
                        <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                            <FontAwesomeIcon :icon="faCheck" class="mr-2 text-green-600" />
                            Permisos Asignados ({{ currentUserPermissions.length }})
                        </h4>
                        <div v-if="currentUserPermissions.length > 0" class="space-y-2 max-h-40 overflow-y-auto border rounded p-3">
                            <div 
                                v-for="permission in currentUserPermissions" 
                                :key="permission"
                                class="flex items-center justify-between p-2 bg-green-50 rounded border"
                            >
                                <div class="flex items-center">
                                    <FontAwesomeIcon :icon="faCheck" class="text-green-600 mr-2" />
                                    <span class="text-sm text-gray-700">{{ permission }}</span>
                                </div>
                                <button 
                                    @click="removePermissionFromUser(permission)"
                                    :disabled="loading || currentUserPermissions.length === 1"
                                    class="text-red-600 hover:text-red-800 transition-colors disabled:opacity-50"
                                    title="Remover permiso"
                                >
                                    <FontAwesomeIcon :icon="faTimes" />
                                </button>
                            </div>
                        </div>
                        <div v-else class="text-center py-4 text-gray-500 border rounded">
                            No tiene permisos asignados
                        </div>
                        
                        <!-- Advertencia si solo tiene un permiso -->
                        <div v-if="currentUserPermissions.length === 1" class="mt-2 text-xs text-orange-600 flex items-center">
                            <FontAwesomeIcon :icon="faWarning" class="mr-1" />
                            No se puede eliminar el último permiso. El usuario debe tener al menos uno.
                        </div>
                    </div>

                    <!-- Permisos Disponibles -->
                    <div>
                        <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                            <FontAwesomeIcon :icon="faPlus" class="mr-2 text-blue-600" />
                            Asignar Nuevos Permisos ({{ availablePermissionsForUser.length }})
                        </h4>
                        <div v-if="availablePermissionsForUser.length > 0" class="space-y-2 max-h-40 overflow-y-auto border rounded p-3">
                            <div 
                                v-for="permission in availablePermissionsForUser" 
                                :key="permission.id"
                                class="flex items-center justify-between p-2 bg-gray-50 rounded border hover:bg-gray-100 transition-colors"
                            >
                                <span class="text-sm text-gray-700">{{ permission.name }}</span>
                                <button 
                                    @click="assignPermissionToUser(permission)"
                                    :disabled="loading"
                                    class="text-blue-600 hover:text-blue-800 transition-colors disabled:opacity-50"
                                    title="Asignar permiso"
                                >
                                    <FontAwesomeIcon :icon="faPlus" />
                                </button>
                            </div>
                        </div>
                        <div v-else class="text-center py-4 text-gray-500 border rounded">
                            No hay permisos adicionales disponibles
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                    <button 
                        @click="closeManagePermissionsModal"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
                        :disabled="loading"
                    >
                        {{ loading ? 'Procesando...' : 'Cerrar' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal de Confirmación para Cerrar -->
        <div v-if="showConfirmCloseModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <FontAwesomeIcon :icon="faWarning" class="h-6 w-6 text-amber-500 mr-3" />
                        <h3 class="text-lg font-semibold text-gray-900">Cambios sin guardar</h3>
                    </div>
                    
                    <p class="text-gray-600 mb-6">
                        Hay cambios sin guardar. ¿Deseas salir? Se perderán los datos que modificaste.
                    </p>
                    
                    <div class="flex justify-end space-x-3">
                        <button
                            @click="showConfirmCloseModal = false"
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors"
                        >
                            Seguir Editando
                        </button>
                        <button
                            @click="closeEditRolesAndPermissionsModal"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors"
                        >
                            Salir sin Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faUsers, faShieldHalved, faEdit, faTrash, faPlus, faTimes, faCheck, faEye, faUserPlus, faKey, faExclamationTriangle as faWarning } from '@fortawesome/free-solid-svg-icons';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';

// Toast instance
const toast = useToast();

// Props
const props = defineProps({
    users: Array,
    roles: Array,
    permissions: Array,
});

// Estados para mostrar/ocultar contraseña
const showNewUserPassword = ref(false);

// Reactive data
const users = ref(props.users || []);
const roles = ref(props.roles || []);
const permissions = ref(props.permissions || []);
const searchTerm = ref('');
const selectedRole = ref('');
const showEditRolesAndPermissionsModal = ref(false);
const showPermissionsModal = ref(false);
const showManagePermissionsModal = ref(false);
const showCreateUserModal = ref(false);
const showConfirmCloseModal = ref(false);
const currentUser = ref(null);
const currentUserPermissions = ref([]);
const originalUserPermissions = ref([]);
const currentEmployeeData = ref(null);
const originalEmployeeData = ref(null);
const originalUserRole = ref('');
const availablePermissionsForUser = ref([]);
const loading = ref(false);
const permissionLoading = ref(false);

// Pagination
const currentPage = ref(1);
const itemsPerPage = ref(5);

// User roles management
const userRoles = ref([]);
const selectedUserRole = ref('');

// New user form
const newUserForm = ref({
    name: '',
    email: '',
    password: '',
    role: 'empleado',
    cargo: '',
    telefono: '',
    selectedPermissions: []  // Array simple de permisos seleccionados manualmente
});

// Computed properties
const filteredUsers = computed(() => {
    let filtered = users.value;
    
    if (searchTerm.value) {
        filtered = filtered.filter(user => 
            user.name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
            user.email.toLowerCase().includes(searchTerm.value.toLowerCase())
        );
    }
    
    if (selectedRole.value) {
        filtered = filtered.filter(user => 
            user.roles.includes(selectedRole.value)
        );
    }
    
    return filtered;
});

// Pagination computed properties
const totalPages = computed(() => {
    return Math.ceil(filteredUsers.value.length / itemsPerPage.value);
});

const paginatedUsers = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredUsers.value.slice(start, end);
});

const paginationInfo = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value + 1;
    const end = Math.min(currentPage.value * itemsPerPage.value, filteredUsers.value.length);
    return {
        start,
        end,
        total: filteredUsers.value.length
    };
});

const availableRoles = computed(() => {
    // Solo mostrar roles para usuarios internos (empleados y administradores)
    return ['admin', 'empleado'];
});

const editModalTitle = computed(() => {
    if (!currentUser.value || !currentUser.value.roles || currentUser.value.roles.length === 0) {
        return 'Editar datos del usuario';
    }
    
    const userRole = currentUser.value.roles[0];
    if (userRole === 'admin') {
        return 'Editar datos del administrador';
    } else if (userRole === 'empleado') {
        return 'Editar datos del empleado';
    }
    
    return 'Editar datos del usuario';
});

const hasChanges = computed(() => {
    // Verificar cambios en el rol
    const roleChanged = selectedUserRole.value !== originalUserRole.value;
    
    // Verificar cambios en los datos del empleado
    const employeeDataChanged = currentEmployeeData.value && originalEmployeeData.value && (
        currentEmployeeData.value.cargo !== originalEmployeeData.value.cargo ||
        currentEmployeeData.value.telefono !== originalEmployeeData.value.telefono
    );
    
    // Verificar cambios en permisos
    const permissionsChanged = originalUserPermissions.value.length > 0 && (
        currentUserPermissions.value.length !== originalUserPermissions.value.length ||
        !currentUserPermissions.value.every(permission => originalUserPermissions.value.includes(permission)) ||
        !originalUserPermissions.value.every(permission => currentUserPermissions.value.includes(permission))
    );
    
    return roleChanged || employeeDataChanged || permissionsChanged;
});

const saveButtonText = computed(() => {
    return 'Actualizar';
});

const visiblePages = computed(() => {
    const pages = [];
    const total = totalPages.value;
    const current = currentPage.value;
    
    if (total <= 7) {
        // Si hay 7 páginas o menos, mostrar todas
        for (let i = 1; i <= total; i++) {
            pages.push(i);
        }
    } else {
        // Lógica para mostrar páginas con puntos suspensivos
        if (current <= 4) {
            pages.push(1, 2, 3, 4, 5, '...', total);
        } else if (current >= total - 3) {
            pages.push(1, '...', total - 4, total - 3, total - 2, total - 1, total);
        } else {
            pages.push(1, '...', current - 1, current, current + 1, '...', total);
        }
    }
    
    return pages;
});

// Methods
const getUserCountByRole = (roleName) => {
    return users.value.filter(user => user.roles.includes(roleName)).length;
};

// Pagination methods
const goToPage = (page) => {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
    }
};

// Watch for search/filter changes to reset pagination
watch([searchTerm, selectedRole], () => {
    currentPage.value = 1;
});

const openEditRolesAndPermissionsModal = async (user) => {
    currentUser.value = { ...user };
    selectedUserRole.value = user.roles[0] || '';
    originalUserRole.value = user.roles[0] || '';
    
    // Inicializar permisos
    currentUserPermissions.value = [];
    originalUserPermissions.value = [];
    
    // Inicializar datos del empleado
    currentEmployeeData.value = {
        cargo: '',
        telefono: ''
    };
    originalEmployeeData.value = {
        cargo: '',
        telefono: ''
    };
    
    loading.value = true;
    showEditRolesAndPermissionsModal.value = true;
    
    try {
        await loadUserPermissions(user);
        await loadEmployeeData(user);
    } catch (error) {
        console.error('Error loading user data:', error);
        showErrorMessage('Error al cargar los datos del usuario');
    } finally {
        loading.value = false;
    }
};

const attemptCloseEditModal = () => {
    if (hasChanges.value) {
        showConfirmCloseModal.value = true;
    } else {
        closeEditRolesAndPermissionsModal();
    }
};

const closeEditRolesAndPermissionsModal = () => {
    showEditRolesAndPermissionsModal.value = false;
    showConfirmCloseModal.value = false;
    currentUser.value = null;
    selectedUserRole.value = '';
    originalUserRole.value = '';
    currentUserPermissions.value = [];
    originalUserPermissions.value = [];
    currentEmployeeData.value = null;
    originalEmployeeData.value = null;
    availablePermissionsForUser.value = [];
};

const loadUserPermissions = async (user) => {
    try {
        const response = await axios.get(`/roles/users/${user.id}/permissions`);
        if (response.data.success) {
            currentUserPermissions.value = [...response.data.permissions];
            originalUserPermissions.value = [...response.data.permissions];
            
            // Calculate available permissions (permissions not yet assigned)
            availablePermissionsForUser.value = permissions.value.filter(
                permission => !currentUserPermissions.value.includes(permission.name)
            );
        }
    } catch (error) {
        console.error('Error fetching user permissions:', error);
        throw error;
    }
};

const loadEmployeeData = async (user) => {
    try {
        const response = await axios.get(`/roles/users/${user.id}/employee`);
        if (response.data.success && response.data.employee) {
            const employeeData = {
                cargo: response.data.employee.cargo || '',
                telefono: response.data.employee.telefono || ''
            };
            currentEmployeeData.value = { ...employeeData };
            originalEmployeeData.value = { ...employeeData };
        } else {
            // Si no existe registro de empleado, mantener valores vacíos
            const emptyData = { cargo: '', telefono: '' };
            currentEmployeeData.value = { ...emptyData };
            originalEmployeeData.value = { ...emptyData };
        }
    } catch (error) {
        console.error('Error fetching employee data:', error);
        // No lanzar el error, solo mantener valores vacíos
        const emptyData = { cargo: '', telefono: '' };
        currentEmployeeData.value = { ...emptyData };
        originalEmployeeData.value = { ...emptyData };
    }
};

const assignPermissionToUser = async (permissionName) => {
    if (!currentUser.value || permissionLoading.value) return;
    
    // Solo modificar el array temporal, NO enviar al servidor
    if (!currentUserPermissions.value.includes(permissionName)) {
        currentUserPermissions.value.push(permissionName);
        
        // Actualizar permisos disponibles
        availablePermissionsForUser.value = permissions.value.filter(
            permission => !currentUserPermissions.value.includes(permission.name)
        );
    }
};

const removePermissionFromUser = async (permissionName) => {
    if (!currentUser.value || permissionLoading.value) return;
    
    if (currentUserPermissions.value.length === 1) {
        showErrorMessage('No se puede eliminar el último permiso. El usuario debe tener al menos uno.');
        return;
    }
    
    // Solo modificar el array temporal, NO enviar al servidor
    const index = currentUserPermissions.value.indexOf(permissionName);
    if (index > -1) {
        currentUserPermissions.value.splice(index, 1);
        
        // Actualizar permisos disponibles
        availablePermissionsForUser.value = permissions.value.filter(
            permission => !currentUserPermissions.value.includes(permission.name)
        );
    }
};

const updateUserRoles = async () => {
    if (!currentUser.value || !selectedUserRole.value) {
        showErrorMessage('Por favor seleccione un rol');
        return;
    }
    
    loading.value = true;
    
    try {
        const response = await axios.post(`/roles/users/${currentUser.value.id}/update-roles`, {
            roles: [selectedUserRole.value]
        });
        
        if (response.data.success) {
            // Update the user in the local state
            const userIndex = users.value.findIndex(u => u.id === currentUser.value.id);
            if (userIndex !== -1) {
                users.value[userIndex] = response.data.user;
                currentUser.value = response.data.user;
            }
            
            showSuccessMessage(`Rol actualizado correctamente a ${selectedUserRole.value}`);
        }
    } catch (error) {
        console.error('Error updating roles:', error);
        showErrorMessage(error.response?.data?.message || 'Error al actualizar el rol');
    } finally {
        loading.value = false;
    }
};

const showUserPermissions = async (user) => {
    currentUser.value = user;
    loading.value = true;
    
    try {
        const response = await axios.get(`/roles/users/${user.id}/permissions`);
        if (response.data.success) {
            currentUserPermissions.value = response.data.permissions || [];
            showPermissionsModal.value = true;
        }
    } catch (error) {
        console.error('Error fetching permissions:', error);
        showErrorMessage('Error al obtener los permisos del usuario');
    } finally {
        loading.value = false;
    }
};

const closePermissionsModal = () => {
    showPermissionsModal.value = false;
    currentUser.value = null;
    currentUserPermissions.value = [];
};

// Manage permissions functions
const openPermissionsModal = async (user) => {
    currentUser.value = user;
    loading.value = true;
    
    try {
        const response = await axios.get(`/roles/users/${user.id}/permissions`);
        if (response.data.success) {
            currentUserPermissions.value = response.data.permissions || [];
            
            // Filtrar permisos disponibles (que no tiene asignados)
            availablePermissionsForUser.value = permissions.value.filter(permission => 
                !currentUserPermissions.value.includes(permission.name)
            );
            
            showManagePermissionsModal.value = true;
        }
    } catch (error) {
        console.error('Error fetching permissions:', error);
        showErrorMessage('Error al obtener los permisos del usuario');
    } finally {
        loading.value = false;
    }
};

const closeManagePermissionsModal = () => {
    showManagePermissionsModal.value = false;
    currentUser.value = null;
    currentUserPermissions.value = [];
    availablePermissionsForUser.value = [];
};

// Create user functions
const openCreateUserModal = () => {
    showCreateUserModal.value = true;
    // No necesitamos inicializar permisos por rol, empezamos vacío
    newUserForm.value.selectedPermissions = [];
};

// Funciones simplificadas para permisos
const clearAllPermissions = () => {
    newUserForm.value.selectedPermissions = [];
};

const selectAllPermissions = () => {
    newUserForm.value.selectedPermissions = permissions.value.map(p => p.name);
};

// Update employee data function
const updateEmployeeData = async () => {
    if (!currentUser.value || !currentEmployeeData.value?.cargo || !currentEmployeeData.value?.telefono) {
        showErrorMessage('Por favor complete todos los campos del empleado');
        return;
    }

    loading.value = true;

    try {
        const response = await axios.put(`/roles/users/${currentUser.value.id}/employee`, {
            cargo: currentEmployeeData.value.cargo,
            telefono: currentEmployeeData.value.telefono
        });

        if (response.data.success) {
            showSuccessMessage('Datos del empleado actualizados correctamente');
            // Cerrar el modal después de la actualización exitosa
            closeEditRolesAndPermissionsModal();
        }
    } catch (error) {
        console.error('Error updating employee data:', error);
        if (error.response?.status === 422) {
            const errors = error.response.data.errors;
            if (errors.cargo) {
                showErrorMessage('Error en el campo cargo: ' + errors.cargo[0]);
            } else if (errors.telefono) {
                showErrorMessage('Error en el campo teléfono: ' + errors.telefono[0]);
            } else {
                showErrorMessage('Por favor revise los datos ingresados');
            }
        } else {
            showErrorMessage(error.response?.data?.message || 'Error al actualizar los datos del empleado');
        }
    } finally {
        loading.value = false;
    }
};

// Save all changes function
const saveAllChanges = async () => {
    if (!currentUser.value) {
        showErrorMessage('Error: No hay usuario seleccionado');
        return;
    }

    const roleChanged = selectedUserRole.value !== originalUserRole.value;
    const employeeDataChanged = currentEmployeeData.value && originalEmployeeData.value && (
        currentEmployeeData.value.cargo !== originalEmployeeData.value.cargo ||
        currentEmployeeData.value.telefono !== originalEmployeeData.value.telefono
    );
    const permissionsChanged = originalUserPermissions.value.length > 0 && (
        currentUserPermissions.value.length !== originalUserPermissions.value.length ||
        !currentUserPermissions.value.every(permission => originalUserPermissions.value.includes(permission)) ||
        !originalUserPermissions.value.every(permission => currentUserPermissions.value.includes(permission))
    );

    if (!roleChanged && !employeeDataChanged && !permissionsChanged) {
        showErrorMessage('No hay cambios para guardar');
        return;
    }

    loading.value = true;

    try {
        let promises = [];
        let successMessages = [];

        // Actualizar datos del empleado si han cambiado
        if (employeeDataChanged) {
            if (!currentEmployeeData.value?.cargo || !currentEmployeeData.value?.telefono) {
                showErrorMessage('Por favor complete todos los campos del empleado');
                loading.value = false;
                return;
            }
            
            promises.push(
                axios.put(`/roles/users/${currentUser.value.id}/employee`, {
                    cargo: currentEmployeeData.value.cargo,
                    telefono: currentEmployeeData.value.telefono
                }).then(() => {
                    successMessages.push('Datos del empleado actualizados');
                })
            );
        }

        // Actualizar rol si ha cambiado
        if (roleChanged) {
            promises.push(
                axios.post(`/roles/users/${currentUser.value.id}/update-roles`, {
                    roles: [selectedUserRole.value]
                }).then((response) => {
                    if (response.data.success) {
                        // Actualizar usuario en la lista principal
                        const userIndex = users.value.findIndex(u => u.id === currentUser.value.id);
                        if (userIndex !== -1) {
                            users.value[userIndex] = response.data.user;
                        }
                        successMessages.push('Rol actualizado');
                    }
                })
            );
        }

        // Actualizar permisos si han cambiado
        if (permissionsChanged) {
            promises.push(
                axios.put(`/roles/users/${currentUser.value.id}/sync-permissions`, {
                    permissions: currentUserPermissions.value
                }).then((response) => {
                    if (response.data.success) {
                        // Actualizar usuario en la lista principal
                        const userIndex = users.value.findIndex(u => u.id === currentUser.value.id);
                        if (userIndex !== -1) {
                            users.value[userIndex] = response.data.user;
                        }
                        successMessages.push('Permisos actualizados');
                    }
                })
            );
        }

        // Ejecutar todas las promesas
        await Promise.all(promises);

        // Mostrar mensaje de éxito y cerrar modal
        showSuccessMessage(successMessages.join(' y ') + ' correctamente');
        closeEditRolesAndPermissionsModal();

    } catch (error) {
        console.error('Error saving changes:', error);
        if (error.response?.status === 422) {
            const errors = error.response.data.errors;
            if (errors.cargo) {
                showErrorMessage('Error en el campo cargo: ' + errors.cargo[0]);
            } else if (errors.telefono) {
                showErrorMessage('Error en el campo teléfono: ' + errors.telefono[0]);
            } else if (errors.roles) {
                showErrorMessage('Error en el rol seleccionado');
            } else if (errors.permissions) {
                showErrorMessage('Error en los permisos seleccionados');
            } else {
                showErrorMessage('Por favor revise los datos ingresados');
            }
        } else {
            showErrorMessage(error.response?.data?.message || 'Error al guardar los cambios');
        }
    } finally {
        loading.value = false;
    }
};

const createInternalUser = async () => {
    if (!newUserForm.value.name || !newUserForm.value.email || !newUserForm.value.password || !newUserForm.value.cargo || !newUserForm.value.telefono) {
        showErrorMessage('Por favor complete todos los campos');
        return;
    }

    if (newUserForm.value.selectedPermissions.length === 0) {
        showErrorMessage('Debe seleccionar al menos un permiso para el usuario');
        return;
    }

    loading.value = true;

    try {
        // Crear el usuario con permisos en una sola petición
        const response = await axios.post('/roles/create-user', {
            name: newUserForm.value.name,
            email: newUserForm.value.email,
            password: newUserForm.value.password,
            role: newUserForm.value.role,
            cargo: newUserForm.value.cargo,
            telefono: newUserForm.value.telefono,
            permissions: newUserForm.value.selectedPermissions
        });

        if (response.data.success) {
            let newUser = response.data.user;
            
            // Agregar nuevo usuario a la lista
            users.value.push(newUser);
            
            closeCreateUserModal();
            
            showSuccessMessage(response.data.message || `Usuario ${newUserForm.value.name} creado correctamente`);
        }
    } catch (error) {
        console.error('Error creating user:', error);
        if (error.response?.status === 422) {
            const errors = error.response.data.errors;
            if (errors.email) {
                showErrorMessage('Este email ya está registrado');
            } else if (errors.permissions) {
                showErrorMessage('Error en los permisos seleccionados');
            } else {
                showErrorMessage('Por favor revise los datos ingresados');
            }
        } else {
            showErrorMessage(error.response?.data?.message || 'Error al crear el usuario');
        }
    } finally {
        loading.value = false;
    }
};

const closeCreateUserModal = () => {
    showCreateUserModal.value = false;
    newUserForm.value = {
        name: '',
        email: '',
        password: '',
        role: 'empleado',
        cargo: '',
        telefono: '',
        selectedPermissions: []
    };
};

const getRoleColor = (roleName) => {
    const colors = {
        'admin': 'bg-red-100 text-red-800',
        'empleado': 'bg-blue-100 text-blue-800'
    };
    return colors[roleName] || 'bg-gray-100 text-gray-800';
};

const showSuccessMessage = (message) => {
    toast.add({
        severity: 'success',
        summary: 'Éxito',
        detail: message,
        life: 4000
    });
};

const showErrorMessage = (message) => {
    toast.add({
        severity: 'error',
        summary: 'Error',
        detail: message,
        life: 5000
    });
};

// Refresh data
const refreshData = () => {
    toast.add({
        severity: 'info',
        summary: 'Actualizando',
        detail: 'Recargando datos de usuarios y roles...',
        life: 2000
    });
    router.reload({ only: ['users', 'roles'] });
};
</script>
