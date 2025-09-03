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
        <div class="min-h-screen bg-gradient-to-br from-red-50 via-white to-red-50">
            <!-- Header elegante -->
            <div class="bg-gradient-to-r from-red-700 via-red-600 to-red-500 shadow-xl">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-6">
                            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-xl p-4 shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold text-white">Mi Perfil</h1>
                                <p class="text-red-100 text-lg">Gestiona tu información personal y configuración de cuenta</p>
                            </div>
                        </div>
                        
                        <div class="hidden sm:flex items-center space-x-3 bg-white bg-opacity-15 backdrop-blur-sm rounded-lg px-4 py-2">
                            <div class="w-3 h-3 bg-green-400 rounded-full shadow-lg animate-pulse"></div>
                            <span class="text-white font-medium">Cuenta Activa</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="py-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    
                    <!-- Navegación de secciones elegante -->
                    <div class="mb-10">
                        <nav class="flex space-x-1 bg-white rounded-xl shadow-lg border border-red-100 p-2">
                            <a href="#profile" class="flex-1 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg py-3 px-6 text-center font-semibold shadow-md transition-all duration-300 hover:shadow-lg">
                                <div class="flex items-center justify-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span>Información Personal</span>
                                </div>
                            </a>
                            <a href="#security" class="flex-1 text-red-600 hover:bg-red-50 rounded-lg py-3 px-6 text-center font-semibold transition-all duration-300">
                                <div class="flex items-center justify-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    <span>Seguridad</span>
                                </div>
                            </a>
                            <a href="#account" class="flex-1 text-red-600 hover:bg-red-50 rounded-lg py-3 px-6 text-center font-semibold transition-all duration-300">
                                <div class="flex items-center justify-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span>Configuración</span>
                                </div>
                            </a>
                        </nav>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                        
                        <!-- Contenido principal -->
                        <div class="lg:col-span-3 space-y-8">
                        
                            <!-- Información Personal -->
                            <section id="profile" class="bg-white shadow-xl rounded-2xl border border-red-100 overflow-hidden">
                                <div class="px-8 py-6 bg-gradient-to-r from-red-50 to-red-100 border-b border-red-200">
                                    <div class="flex items-center space-x-4">
                                        <div class="bg-red-600 rounded-lg p-3">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h2 class="text-xl font-bold text-red-900">Información Personal</h2>
                                            <p class="text-red-700">
                                                Actualiza tu información de perfil y dirección de correo electrónico.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-8">
                                    <UpdateProfileInformationForm
                                        :must-verify-email="mustVerifyEmail"
                                        :status="status"
                                    />
                                </div>
                            </section>

                            <!-- Seguridad -->
                            <section id="security" class="bg-white shadow-xl rounded-2xl border border-red-100 overflow-hidden">
                                <div class="px-8 py-6 bg-gradient-to-r from-red-50 to-red-100 border-b border-red-200">
                                    <div class="flex items-center space-x-4">
                                        <div class="bg-red-600 rounded-lg p-3">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h2 class="text-xl font-bold text-red-900">Seguridad de la Cuenta</h2>
                                            <p class="text-red-700">
                                                Actualiza tu contraseña para mantener tu cuenta segura.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-8">
                                    <UpdatePasswordForm />
                                </div>
                            </section>

                            <!-- Configuración de cuenta -->
                            <section id="account" class="bg-white shadow-xl rounded-2xl border border-red-300 overflow-hidden">
                                <div class="px-8 py-6 bg-gradient-to-r from-red-100 to-red-200 border-b border-red-300">
                                    <div class="flex items-center space-x-4">
                                        <div class="bg-red-700 rounded-lg p-3">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h2 class="text-xl font-bold text-red-900">Eliminar Cuenta</h2>
                                            <p class="text-red-800">
                                                Esta acción es irreversible. Todos tus datos serán eliminados permanentemente.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-8">
                                    <DeleteUserForm />
                                </div>
                            </section>
                        </div>

                        <!-- Sidebar Mejorado -->
                        <div class="lg:col-span-1">
                            <div class="bg-white shadow-xl rounded-2xl border border-red-100 overflow-hidden sticky top-8">
                                <!-- Header del sidebar -->
                                <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-6">
                                    <h3 class="text-lg font-bold text-white mb-2">Estado de la Cuenta</h3>
                                    <p class="text-red-100 text-sm">Información de seguridad actual</p>
                                </div>
                                
                                <!-- Estado de verificación -->
                                <div class="p-6 space-y-4">
                                    <div class="flex items-center justify-between py-3 px-4 bg-green-50 rounded-lg border border-green-200">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                            <span class="text-sm font-medium text-green-800">Email</span>
                                        </div>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                            Verificado
                                        </span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between py-3 px-4 bg-green-50 rounded-lg border border-green-200">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                            <span class="text-sm font-medium text-green-800">Contraseña</span>
                                        </div>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                            Segura
                                        </span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between py-3 px-4 bg-red-50 rounded-lg border border-red-200">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                                            <span class="text-sm font-medium text-red-800">Último acceso</span>
                                        </div>
                                        <span class="text-xs font-semibold text-red-600">Hoy</span>
                                    </div>
                                </div>

                                <!-- Recomendaciones de Seguridad -->
                                <div class="px-6 pb-6">
                                    <div class="bg-red-50 rounded-xl p-4 border border-red-200">
                                        <h4 class="text-sm font-bold text-red-900 mb-3 flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Recomendaciones de Seguridad
                                        </h4>
                                        <ul class="text-xs text-red-700 space-y-3">
                                            <li class="flex items-start">
                                                <span class="w-1.5 h-1.5 bg-red-600 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                                Usa contraseñas únicas y seguras
                                            </li>
                                            <li class="flex items-start">
                                                <span class="w-1.5 h-1.5 bg-red-600 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                                Mantén tu email actualizado
                                            </li>
                                            <li class="flex items-start">
                                                <span class="w-1.5 h-1.5 bg-red-600 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                                Revisa regularmente la actividad
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Soporte -->
                                <div class="px-6 pb-6">
                                    <div class="bg-gradient-to-br from-red-100 to-red-200 rounded-xl p-4 border border-red-300">
                                        <h4 class="text-sm font-bold text-red-900 mb-2 flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 109.75 9.75A9.75 9.75 0 0012 2.25z"/>
                                            </svg>
                                            ¿Necesitas ayuda?
                                        </h4>
                                        <p class="text-xs text-red-800 mb-3 leading-relaxed">
                                            Contacta con nuestro equipo de soporte para cualquier consulta.
                                        </p>
                                        <button class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white text-xs font-bold py-3 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                            Contactar Soporte
                                        </button>
                                    </div>
                                </div>
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