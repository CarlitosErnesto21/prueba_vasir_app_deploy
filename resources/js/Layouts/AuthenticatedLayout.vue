<script setup>
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
    <Toast/>
   <div class="h-screen flex flex-col">
    <!--navbar-->
    <header class="bg-gradient-to-r from-orange-400 to-orange-600 text-black shadow-md fixed top-0 w-full z-50">
        <div class="px-6 py-3 flex justify-between items-center">
            <!--Boton para ocultar/mostrar el slider-->
            <button @click="isSidebarOpen = !isSidebarOpen" class="block md:hidden text-black 
                mr-3" style="font-size: 150%;">
                <FontAwesomeIcon :icon="faList"/>
            </button>
            <div class="text-2xl font-semibold text-black" 
                style="text-shadow: 1px 1px 2px rgba(0, 0, 0, 2), -1px -1px 2px white;
                font-family: Porter Sans Block;">
                Dashboard
            </div>
            <h1 class="font-semibold text-center flex-wrap text-2xl text-black" 
                style="text-shadow: 1px 1px 2px rgba(0, 0, 0, 2), -1px -1px 2px white; margin-left: 0%;
                font-family: Porter Sans Block;">
                TLAPALERÍA AKC
            </h1>
            <!--Datos de la sesion-->
            <div class="flex items-center space-x-4">
                <div class="relative group">
                    <div class="bg-orange-200 hover:bg-red-300" style=" padding-top: 4px; padding-left: 4px;
                            padding-right: 4px; padding-bottom: 4px; border-radius: 7px;">
                        <div style="background-color: #f48c1f; padding-top: 4px; padding-left: 3px;
                            padding-right: 3px; border-radius: 7px;">
                            <FontAwesomeIcon :icon="faUserCircle" class="text-2xl"/>
                        </div>
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
                    <div class="bg-orange-200" style=" padding-top: 4px; padding-left: 4px;
                            padding-right: 4px; padding-bottom: 4px; border-radius: 7px;">
                        <button @click="logout" class="text-black text-xl hover:text-red-900"
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
    <!--div para sidebar y contenido dinamico-->
    <div class="flex flex-1 pt-3">
        <aside :class="{'-translate-x-full md:translate-x-0' : !isSidebarOpen, 'translate-x-0' : isSidebarOpen}"
            class="fixed md:relative top-0 left-0 h-[calc(100vh-64px) w-56 bg-gradient-to-b from-orange-400 to-orange-600 
            text-black transition-transform duration-300 ease-in-out shadow-lg z-50] rounded-xl z-40"><br>
            <div class="px-4 pt-20 text-x font-semibold flex justify-center items-center text-white"> 
                <img src="../../../imagenes/logo.png" style="box-shadow: black 2px 2px 2px; border-radius: 100px;" 
                class="w-12 md:w-13 lg:w-13 xl:w-32 rounded-full relative">
            </div>
            <div class="px-4 pt-1 text-2xl font-semibold flex ml-14  text-black">
                <span style="text-shadow: 1px 1px 2px rgba(0, 0, 0, 2), -1px -1px 2px white; 
                    font-family: Porter Sans Block;">MENÚ</span>
            </div>
        <nav class="mt-4">
            <ul>
                <li class="px-6 py-3 hover:bg-orange-600 flex items-center">
                    <Link :href="route('dashboard')"  class="flex items-center w-full">
                        <FontAwesomeIcon :icon="faHouseChimney" class="mr-3" />
                        Inicio
                    </Link>
                </li>
                <li class="px-1 py-3 flex flex-col">
                    <button @click="toggleDropdown" class="ml-1 flex items-center w-full focus:outline-none px-4 py-2 hover:bg-orange-600">
                        <FontAwesomeIcon :icon="faTableList" class="mr-4" />
                        Catálogos
                        <FontAwesomeIcon :icon="faChevronDown" class="ml-auto transition-transform" :class="{'rotate-90': isOpen}" />
                    </button>
                    <!-- Menú desplegable alineado -->
                    <transition name="expand">
                        <ul v-if="isOpen" class="w-fullrounded-md shadow-lg overflow-hidden">
                            <li>
                                <Link :href="route('categorias')" class="block w-full px-10 py-2 hover:bg-orange-600">
                                    <FontAwesomeIcon :icon="faLayerGroup" class="mr-3" />
                                    Categorías
                                </Link>
                            </li>
                            <li>
                                <Link :href="route('marcas')" class="block w-full px-10 py-2 hover:bg-orange-600">
                                    <FontAwesomeIcon :icon="faTags" class="mr-3" />
                                    Marcas
                                </Link>
                            </li>
                        </ul>
                    </transition>
                </li>
                <li class="px-6 py-3 hover:bg-orange-600 flex items-center">
                    <Link :href="route('productos')" class="flex items-center w-full">
                        <FontAwesomeIcon :icon="faStoreAlt" class="mr-3" />
                        Productos
                    </Link>
                </li>
                <li class="px-6 py-3 hover:bg-orange-600 flex items-center">
                    <Link :href="route('reservas')"  class="flex items-center w-full">
                        <FontAwesomeIcon :icon="faReceipt" class="mr-3" />
                        Reservas
                    </Link>
                </li>
                <li class="px-6 py-3 hover:bg-orange-600 flex items-center">
                    <Link :href="route('reservas.rango')"  class="flex items-center w-full">
                        <FontAwesomeIcon :icon="faFileAlt" class="mr-3" />
                        Reportes
                    </Link>
                </li>
            </ul>
        </nav>
        </aside>
        <!--Div para el contenido dinamico-->
        <main class="flex-1 p-10 overflow-auto  bg-white">
            <slot />
        </main>
    </div>
    <!--Div para el contenido dinamico-->
    <!--fin paradiv para sidebar y contenido dinamico-->
   </div>
</template>