<template>
    <header class="bg-red-600 text-white fixed top-0 left-0 w-full z-50 h-8 flex items-center">
        <div class="flex items-center h-full px-4 text-xs space-x-6 w-full">
            <div class="flex items-center space-x-2 ml-4">
                <a href="https://www.facebook.com/share/1C7tZxDHzh/" class="ml-2 hover:text-blue-300">😎Facebook</a>
                <a href="https://www.tiktok.com/@vasir_sv?_t=ZM-8wz8jwve57Y&_r=1" class="hover:text-blue-300">🎶TikTok</a>
                <a href="https://www.instagram.com/vasir_sv?igsh=MWx3aGFzdnB5Y2l2OA==" class="hover:text-blue-300">❤️Instagram</a>
                <a href="https://vasirsv.n1co.shop/?fbclid=PAZXh0bgNhZW0CMTEAAaeNe5ijdUbNvoQJ50Rf5CrJ66ixqACW8axuEShnlyN2_ofiouy166aAzWTzqw_aem_d5k2FX_C98iq0MGK-eaKAw" class="hover:text-blue-300">🛒Tienda en Línea</a>
            </div>
        </div>
    </header>
    <header class="bg-gradient-to-r from-white to-white text-black shadow-md fixed top-8 left-0 w-full z-50">
        <div class="px-6 py-3 flex justify-between items-center">
            <!-- Botón menú hamburguesa SOLO en móvil -->
            <button @click="isSidebarOpen = !isSidebarOpen"
                class="block md:hidden text-black mr-3"
                style="font-size: 150%;">
                <FontAwesomeIcon :icon="faList"/>
            </button>
            <div class="flex items-center space-x-8">
                <Link :href="route('Catalogo')" class="flex items-center cursor-pointer select-none">
                    <img src="../../../imagenes/logo.png" class="w-25 h-10 inline-block align-middle" />
                </Link>
            </div>
            <!-- Menú de navegación con desplegable -->
            <nav class="hidden md:flex space-x-12 items-center font-bold">
                <Link :href="route('Catalogo')" class="hover:text-red-600 transition">Inicio</Link>
                <div class="relative group">
                    <button class="hover:text-red-600 transition flex items-center focus:outline-none">
                        Paquetes
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="absolute left-0 mt-2 w-40 bg-white border rounded shadow-lg opacity-0 group-hover:opacity-100 group-hover:visible invisible transition-opacity z-50">
                        <Link href="route('paquetes')" class="block px-4 py-2 text-black hover:bg-red-100">Paquetes turísticos</Link>
                        <Link href="route('reservaciones')" class="block px-4 py-2 text-black hover:bg-red-100">Reservaciones</Link>
                    </div>
                </div>
                <Link href="route('tienda')" class="hover:text-red-600 transition">Tienda</Link>
                <Link href="route('sobre-nosotros')" class="hover:text-red-600 transition">Sobre Nosotros</Link>
                <Link href="route('contactos')" class="hover:text-red-600 transition">Contactos</Link>
            </nav>    
            <!--Datos de la sesion-->
            <div class="flex items-center space-x-4">
                <template v-if="!$page.props.auth || !$page.props.auth.user">
                    <!-- Botón Login -->
                    <Link :href="route('login')" class="px-4 py-2 rounded bg-black text-white hover:bg-red-600 transition">
                        Iniciar Sesión
                    </Link>
                    <!-- Botón Register -->
                    <Link :href="route('register')" class="px-4 py-2 rounded bg-indigo-500 text-white hover:bg-indigo-600 transition">
                        Registrarse 
                    </Link>
                </template>
                <template v-else>
                    <!-- Icono y nombre/email del usuario -->
                    <div
                        class="flex items-center space-x-2 rounded cursor-default"
                        :title="$page.props.auth.user.email">
                        <span class="text-md text-black font-medium">
                            {{ $page.props.auth.user.name || $page.props.auth.user.email }}
                        </span>
                        <FontAwesomeIcon
                            :icon="faUser"
                            class="text-black h-5"/>
                    </div>
                    <!-- Botón Cerrar sesión -->
                    <button @click="logout" class="rounded text-black transition flex items-center">
                        <FontAwesomeIcon :icon="faDoorOpen" class="h-5"/>
                    </button>
                </template>
            </div>
        </div>
    </header>
    <div class="mt-32 px-6">
        <div class="flex overflow-x-auto space-x-6 pb-4">
            <div
                v-for="(product, idx) in products"
                :key="product.id"
                class="min-w-[250px] max-w-xs bg-white rounded shadow p-4 flex flex-col items-center">
                <img
                    v-if="product.imagenes && product.imagenes.length > 0"
                    :src="`/images/productos/${product.imagenes[0].nombre}`"
                    alt="Producto"
                    class="w-40 h-40 object-cover mb-2 rounded"
                />
                <img
                    v-else
                    src="/storage/no-image.png"
                    alt="Sin imagen"
                    class="w-40 h-40 object-cover mb-2 rounded opacity-40"
                />
                <div class="font-bold text-lg mb-1 flex items-center">
                    {{ product.nombre }}
                </div>
                <div class="text-gray-600 text-sm mb-2">{{ product.descripcion }}</div>
                <div class="text-gray-800 text-base font-semibold mb-2">Precio: ${{ product.precio }}</div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faList, faUser, faDoorOpen } from '@fortawesome/free-solid-svg-icons'
import axios from 'axios'

// Ejemplo de funciones para los botones (ajusta según tu lógica real)
const isSidebarOpen = ref(false)
const login = () => router.visit(route('login'))
const logout = () => router.post(route('logout'))

// Obtener productos desde una API REST al montar el componente
const products = ref([])

onMounted(async () => {
    try {
        const response = await axios.get('/api/productos')
        products.value = response.data
    } catch (error) {
        products.value = []
    }
})
</script>