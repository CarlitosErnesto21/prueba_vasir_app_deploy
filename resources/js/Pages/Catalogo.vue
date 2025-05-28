<template>
    <Head title="Catálogo" />
    <header class="bg-gradient-to-r from-white to-white text-black shadow-md fixed top-0 left-0 w-full z-50">
        <div class="px-6 py-3 flex justify-between items-center">
            <!-- Botón menú hamburguesa SOLO en móvil -->
            <button @click="isSidebarOpen = !isSidebarOpen"
                class="block md:hidden text-black mr-3"
                style="font-size: 150%;">
                <FontAwesomeIcon :icon="faList"/>
            </button>
            <div class="text-xl font-semibold text-black">
                <Link :href="route('Catalogo')" class="text-xl font-semibold text-black flex items-center cursor-pointer select-none">
                    <img src="../../../imagenes/logo.jpg" class="w-22 h-7 inline-block align-middle" />
                </Link>
            </div>
            <!--Datos de la sesion-->
            <div class="flex items-center space-x-4">
                <!-- Botón Login -->
                <Link :href="route('login')" class="px-4 py-2 rounded  bg-black text-white hover:bg-red-600  transition">
                    Iniciar Sesión
                </Link>
                <!-- Botón Register -->
                <Link :href="route('register')" class="px-4 py-2 rounded bg-indigo-500 text-white hover:bg-indigo-600 transition">
                    Registrarse 
                </Link>
            </div>
        </div>
    </header>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faList, faUser, faDoorOpen } from '@fortawesome/free-solid-svg-icons'

// Ejemplo de funciones para los botones (ajusta según tu lógica real)
const isSidebarOpen = ref(false)
const login = () => router.visit(route('login'))
const logout = () => router.post(route('logout'))
</script>