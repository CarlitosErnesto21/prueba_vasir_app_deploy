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
            <div class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="bg-blue-600 rounded-lg p-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl font-semibold text-gray-900">Mi Perfil</h1>
                                <p class="text-sm text-gray-600">Gestiona tu información personal y configuración de cuenta</p>
                            </div>
                        </div>
                        
                        <div class="hidden sm:flex items-center space-x-2 text-sm text-gray-500">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <span>Cuenta Activa</span>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Navegación de secciones -->
                <div class="mb-8">
                    <nav class="flex space-x-8 border-b border-gray-200">
                        <a href="#profile" class="border-b-2 border-blue-600 pb-3 px-1 text-sm font-medium text-blue-600">
                            Información Personal
                        </a>
                        <a href="#security" class="border-b-2 border-transparent pb-3 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            Seguridad
                        </a>
                        <a href="#account" class="border-b-2 border-transparent pb-3 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            Configuración
                        </a>
                    </nav>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                    
                    <!-- Contenido principal -->
                    <div class="lg:col-span-3 space-y-8">
                        
                        <!-- Información Personal -->
                        <section id="profile" class="bg-white shadow-sm rounded-lg border border-gray-200">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h2 class="text-lg font-medium text-gray-900">Información Personal</h2>
                                <p class="mt-1 text-sm text-gray-600">
                                    Actualiza tu información de perfil y dirección de correo electrónico.
                                </p>
                            </div>
                            <div class="px-6 py-6">
                                <UpdateProfileInformationForm
                                    :must-verify-email="mustVerifyEmail"
                                    :status="status"
                                />
                            </div>
                        </section>

                        <!-- Seguridad -->
                        <section id="security" class="bg-white shadow-sm rounded-lg border border-gray-200">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h2 class="text-lg font-medium text-gray-900">Seguridad</h2>
                                <p class="mt-1 text-sm text-gray-600">
                                    Actualiza tu contraseña para mantener tu cuenta segura.
                                </p>
                            </div>
                            <div class="px-6 py-6">
                                <UpdatePasswordForm />
                            </div>
                        </section>

                        <!-- Configuración de cuenta -->
                        <section id="account" class="bg-white shadow-sm rounded-lg border border-red-200">
                            <div class="px-6 py-4 border-b border-red-200 bg-red-50">
                                <h2 class="text-lg font-medium text-red-900">Eliminar Cuenta</h2>
                                <p class="mt-1 text-sm text-red-700">
                                    Esta acción es irreversible. Todos tus datos serán eliminados permanentemente.
                                </p>
                            </div>
                            <div class="px-6 py-6">
                                <DeleteUserForm />
                            </div>
                        </section>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6 sticky top-8">
                            <h3 class="text-sm font-medium text-gray-900 mb-4">Estado de la Cuenta</h3>
                            
                            <!-- Estado de verificación -->
                            <div class="space-y-4">
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-sm text-gray-600">Email</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Verificado
                                    </span>
                                </div>
                                
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-sm text-gray-600">Contraseña</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Segura
                                    </span>
                                </div>
                                
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-sm text-gray-600">Último acceso</span>
                                    <span class="text-xs text-gray-500">Hoy</span>
                                </div>
                            </div>

                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Recomendaciones de Seguridad</h4>
                                <ul class="text-xs text-gray-600 space-y-2">
                                    <li class="flex items-start">
                                        <span class="w-1.5 h-1.5 bg-blue-600 rounded-full mt-1.5 mr-2 flex-shrink-0"></span>
                                        Usa contraseñas únicas y seguras
                                    </li>
                                    <li class="flex items-start">
                                        <span class="w-1.5 h-1.5 bg-blue-600 rounded-full mt-1.5 mr-2 flex-shrink-0"></span>
                                        Mantén tu email actualizado
                                    </li>
                                    <li class="flex items-start">
                                        <span class="w-1.5 h-1.5 bg-blue-600 rounded-full mt-1.5 mr-2 flex-shrink-0"></span>
                                        Revisa regularmente la actividad
                                    </li>
                                </ul>
                            </div>

                            <!-- Soporte -->
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">¿Necesitas ayuda?</h4>
                                <p class="text-xs text-gray-600 mb-3">
                                    Contacta con nuestro equipo de soporte para cualquier consulta.
                                </p>
                                <button class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-medium py-2 px-3 rounded-md transition-colors">
                                    Contactar Soporte
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.sticky {
    position: sticky;
}

.transition-colors {
    transition-property: background-color, border-color, color, fill, stroke;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}
</style>