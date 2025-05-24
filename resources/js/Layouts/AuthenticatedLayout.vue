<script setup>
/*import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);*/
import { ref } from 'vue';
//import ApplicationLogo from '@/Components/ApplicationLogo.vue';
//import Dropdown from '@/Components/Dropdown.vue';
//import DropdownLink from '@/Components/DropdownLink.vue';
//import NavLink from '@/Components/NavLink.vue';
//import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { usePage, Link } from '@inertiajs/vue3'; //link para que no recargue toda la pagina
import Toast from 'primevue/toast';
//import { FontAwesomeIcon, } from "@fortawesome/vue-fontawesome";
//import { faList, faTags, faDoorOpen, faFileAlt, faLayerGroup, faCircleXmark, faUserCircle, 
  //  faChevronDown, faHouseChimney, faTableList, faStoreAlt, faReceipt } from "@fortawesome/free-solid-svg-icons";
import axios from 'axios';
import Avatar from 'primevue/avatar';

const page = usePage();
//const user = page.props.auth.user;
const isSidebarOpen = ref(false);
const anioActual = ref(new Date().getFullYear());
const isOpen = ref(false);
const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
};
//FUNCIONES PARA LA LOGICA DEL COMPONENTE
const logout = async () => {
    try{
        await axios.post('/logout');
        window.location.href = '/login';
    }catch(err){
        console.error('Error al cerrar la sesion', err);
    }
}
</script>

<template>

    <!--<div>
        <div class="min-h-screen bg-gray-100">
            <nav
                class="border-b border-gray-100 bg-white"
            >
                <!-- Primary Navigation Menu 
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo 
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links 
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                >
                                    Dashboard                           
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Settings Dropdown 
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger 
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu 
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            Dashboard
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options 
                    <div
                        class="border-t border-gray-200 pb-1 pt-4"
                    >
                        <div class="px-4">
                            <div
                                class="text-base font-medium text-gray-800"
                            >
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading 
            <header
                class="bg-white shadow"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content 
            <main>
                <slot />
            </main>
        </div>
    </div>-->
    <div class="h-screen flex flex-col">
    <!-- Header principal -->
    <header class="bg-gradient-to-r from-white to-red-200 text-black shadow-md fixed top-0 left-0 w-full z-50">
        <div class="px-6 py-3 flex justify-between items-center">
            <!--Boton para ocultar/mostrar el slider-->
            <button @click="isSidebarOpen = !isSidebarOpen" class="block md:hidden text-black 
                mr-3" style="font-size: 150%;">
                <FontAwesomeIcon :icon="faList"/>
            </button>
            <div class="text-3xl font-semibold text-black">
                VAS<img src="../../../imagenes/logovasir.png" class="w-8 h-9 inline-block align-middle">R
            </div>
            <!--Datos de la sesion-->
            <div class="flex items-center space-x-4">
                <div class="relative group">
                        <div style="background-color: #f48c1f; padding-top: 4px; padding-left: 3px;
                            padding-right: 3px; border-radius: 7px;">
                            <Avatar image="/images/vasir.ico" class="!w-7 !h-7" size="small" shape="circle" />
                        </div>
                    <!-- Tooltip -->
                    <div class="absolute mt-5 w-80 bg-white text-black text-sm rounded-xl shadow-lg 
                        p-3 justify-center text-center opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-opacity duration-300"
                        style="margin-left: -215px;">
                        <p><strong>Usuario:</strong>&nbsp;{{ user.name }}</p>
                        <p><strong>Correo:</strong>&nbsp;{{ user.email }}</p>
                    </div>
                </div>
                <div class="relative group">
                    <div class="bg-red-400" style=" padding-top: 4px; padding-left: 4px;
                            padding-right: 4px; padding-bottom: 4px; border-radius: 7px;">
                        <button @click="logout" class="text-black text-xl"
                            style="background-color: #f48c1f; padding: 2px 3px; border-radius: 7px;">
                            <FontAwesomeIcon :icon="faDoorOpen"/>
                        </button>
                    </div>
                    <!-- Tooltip -->
                    <div class="absolute left-1/2 transform -translate-x-1/2 
                        mt-5 w-36 bg-white text-center text-black text-sm rounded-lg shadow-lg 
                        p-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible 
                        transition-opacity duration-300" style="margin-left: -35px;">
                        <p><strong>Cerrar Sesión</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="flex flex-1">
        <!-- Sidebar fijo -->
        <aside
            :class="{'-translate-x-full md:translate-x-0' : !isSidebarOpen, 'translate-x-0' : isSidebarOpen}"
            class="fixed top-16 left-0 h-[calc(100vh-64px)] w-40 bg-gradient-to-b bg-red-600
            text-black transition-transform duration-300 ease-in-out shadow-lg z-40 pt-8">
            <div class="px-4 text-xl font-bold text-white flex justify-center items-center">
                <span>MENÚ</span>
            </div>
        <nav class="mt-4 text-white">
            <ul>
                <li class="px-4 py-3 hover:bg-orange-600 flex items-center">
                    <Link href="route('dashboard')"  class="flex items-center w-full">
                        <FontAwesomeIcon :icon="faHouseChimney" class="mr-3" />
                        Inicio
                    </Link>
                </li>
                <li class="px-0 py-3 flex flex-col">
                    <button @click="toggleDropdown" class="ml-1 flex items-center w-full focus:outline-none px-4 py-2 hover:bg-orange-600">
                        <FontAwesomeIcon :icon="faTableList" class="mr-3" />
                        Catálogos
                        <FontAwesomeIcon :icon="faChevronDown" class="ml-auto transition-transform" :class="{'rotate-90': isOpen}" />
                    </button>
                    <!-- Menú desplegable alineado -->
                    <transition name="expand">
                        <ul v-if="isOpen" class="w-fullrounded-md shadow-lg overflow-hidden">
                            <li>
                                <Link href="route('categorias')" class="block w-full px-5 py-2 hover:bg-orange-600">
                                    <FontAwesomeIcon :icon="faLayerGroup" class="mr-3" />
                                    Categorías
                                </Link>
                            </li>
                            <li>
                                <Link href="route('marcas')" class="block w-full px-5 py-2 hover:bg-orange-600">
                                    <FontAwesomeIcon :icon="faTags" class="mr-3" />
                                    Marcas
                                </Link>
                            </li>
                        </ul>
                    </transition>
                </li>
                <li class="px-4 py-3 hover:bg-orange-600 flex items-center">
                    <Link href="route('productos')" class="flex items-center w-full">
                        <FontAwesomeIcon :icon="faStoreAlt" class="mr-3" />
                        Productos
                    </Link>
                </li>
                <li class="px-5 py-3 hover:bg-orange-600 flex items-center">
                    <Link href="route('reservas')"  class="flex items-center w-full">
                        <FontAwesomeIcon :icon="faReceipt" class="mr-3" />
                        Reservas
                    </Link>
                </li>
                <li class="px-5 py-3 hover:bg-orange-600 flex items-center">
                    <Link href="route('reservas.rango')"  class="flex items-center w-full">
                        <FontAwesomeIcon :icon="faFileAlt" class="mr-3" />
                        Reportes
                    </Link>
                </li>
            </ul>
        </nav>
        </aside>
        <!-- Contenedor para header secundario y contenido -->
        <div class="flex-1 flex flex-col" style="margin-left: 10rem;">
            <!-- Header secundario fijo -->
            <header class="fixed top-16 left-40 right-0 bg-red-500 shadow px-8 py-4 flex items-center z-40"
                style="height: 64px;">
                <h2 class="text-xl font-semibold text-white">Dashboard</h2>
            </header>
            <!-- Contenido principal con padding para no quedar debajo de los headers -->
            <main class="flex-1 p-10 overflow-auto bg-white"
                style="padding-top: 128px;">
                <slot />
            </main>
        </div>
    </div>
    <!--Div para el contenido dinamico-->
    <!--fin paradiv para sidebar y contenido dinamico-->
   </div>
</template>
