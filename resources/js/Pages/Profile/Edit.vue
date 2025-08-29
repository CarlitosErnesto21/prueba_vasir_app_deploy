<!-- filepath: c:\Users\ernes\Documents\modulos\proyecto_graduacion\AgenciaVasir\resources\js\Pages\Profile\Edit.vue -->
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Mi Perfil - VASIR" />

    <AuthenticatedLayout>
        <template #header>
            <!-- Header con gradiente y diseño moderno -->
            <div class="bg-gradient-to-r from-red-600 to-red-800 rounded-lg shadow-lg p-4 sm:p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 sm:space-x-4">
                        <!-- Icono de perfil -->
                        <div class="bg-white/20 rounded-full p-2 sm:p-3">
                            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-white">
                                👤 Mi Perfil
                            </h1>
                            <p class="text-red-100 text-sm sm:text-base mt-1">
                                Gestiona tu información personal y configuración de cuenta
                            </p>
                        </div>
                    </div>
                    
                    <!-- Badge de usuario -->
                    <div class="hidden sm:flex bg-white/20 rounded-full px-3 py-1">
                        <span class="text-white text-sm font-medium">
                            👨‍💼 Usuario Activo
                        </span>
                    </div>
                </div>
            </div>
        </template>

        <!-- Contenido principal -->
        <div class="py-6 sm:py-8 lg:py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                
                <!-- Grid responsivo con espaciado mejorado -->
                <div class="grid gap-6 sm:gap-8 lg:grid-cols-2 xl:grid-cols-3">
                    
                    <!-- Sección 1: Información Personal -->
                    <div class="lg:col-span-2 xl:col-span-2">
                        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <!-- Header de la sección -->
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 sm:p-6 border-b border-gray-100">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-blue-500 rounded-lg p-2">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg sm:text-xl font-bold text-gray-800">
                                            📝 Información Personal
                                        </h3>
                                        <p class="text-gray-600 text-sm">
                                            Actualiza tu nombre, email y datos de contacto
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Contenido -->
                            <div class="p-4 sm:p-6 lg:p-8">
                                <UpdateProfileInformationForm
                                    :must-verify-email="mustVerifyEmail"
                                    :status="status"
                                    class="max-w-full"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Sección 2: Seguridad -->
                    <div class="xl:col-span-1">
                        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <!-- Header de la sección -->
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-4 sm:p-6 border-b border-gray-100">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-green-500 rounded-lg p-2">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg sm:text-xl font-bold text-gray-800">
                                            🔐 Seguridad
                                        </h3>
                                        <p class="text-gray-600 text-sm">
                                            Cambia tu contraseña
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Contenido -->
                            <div class="p-4 sm:p-6">
                                <UpdatePasswordForm class="max-w-full" />
                            </div>
                        </div>
                    </div>

                    <!-- Sección 3: Zona Peligrosa (Eliminar Cuenta) -->
                    <div class="lg:col-span-2 xl:col-span-3">
                        <div class="bg-white rounded-xl shadow-lg border border-red-200 overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <!-- Header de la sección -->
                            <div class="bg-gradient-to-r from-red-50 to-pink-50 p-4 sm:p-6 border-b border-red-200">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-red-500 rounded-lg p-2">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg sm:text-xl font-bold text-red-800">
                                            ⚠️ Zona Peligrosa
                                        </h3>
                                        <p class="text-red-600 text-sm">
                                            Acciones irreversibles - Procede con cuidado
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Contenido -->
                            <div class="p-4 sm:p-6 lg:p-8">
                                <DeleteUserForm class="max-w-2xl" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información adicional -->
                <div class="mt-8 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 sm:p-6 border border-blue-200">
                    <div class="flex items-start space-x-3">
                        <div class="bg-blue-500 rounded-full p-1 mt-1">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-blue-800 mb-1">
                                💡 Consejos de Seguridad
                            </h4>
                            <div class="text-sm text-blue-700 space-y-1">
                                <p>• Usa una contraseña fuerte con al menos 8 caracteres</p>
                                <p>• Incluye mayúsculas, minúsculas, números y símbolos</p>
                                <p>• No compartas tus credenciales con otras personas</p>
                                <p>• Verifica que tu email esté siempre actualizado</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Animaciones suaves */
.transition-shadow {
    transition: box-shadow 0.3s ease-in-out;
}

/* Efectos hover mejorados */
.hover\:shadow-xl:hover {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Mejoras de responsividad */
@media (max-width: 640px) {
    .grid {
        grid-template-columns: 1fr;
    }
}

@media (min-width: 1024px) {
    .lg\:col-span-2 {
        grid-column: span 2;
    }
}

@media (min-width: 1280px) {
    .xl\:col-span-3 {
        grid-column: span 3;
    }
    .xl\:col-span-2 {
        grid-column: span 2;
    }
    .xl\:col-span-1 {
        grid-column: span 1;
    }
}

/* Efectos de gradiente suaves */
.bg-gradient-to-r {
    background-image: linear-gradient(to right, var(--tw-gradient-stops));
}
</style>