<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { usePage, Link } from '@inertiajs/vue3'; //link para que no recargue toda la pagina
import Toast from 'primevue/toast';
import { FontAwesomeIcon, } from "@fortawesome/vue-fontawesome";
import { faList, faTags, faDoorOpen, faFileAlt, faLayerGroup, faCircleXmark, faUserCircle, 
    faChevronCircleDown, faHouseChimney, faTableList, faStoreAlt, faReceipt, faUser, faHome, faHotel} from "@fortawesome/free-solid-svg-icons";
import axios from 'axios';

const page = usePage();
//const user = page.props.auth.user;
const isSidebarOpen = ref(false);
const isSidebarCollapsed = ref(true); // Colapsado por defecto
const anioCurrent = ref(new Date().getFullYear());
const isOpen = ref(false);
const toggleDropdown = () => { isOpen.value = !isOpen.value; };
const toggleSidebar = () => { isSidebarCollapsed.value = !isSidebarCollapsed.value; };
//FUNCIONES PARA LA LOGICA DEL COMPONENTE
const logout = async () => {
    try{
        await axios.post('/logout');
        window.location.href = '/login';
    }catch(err){
        console.error('Error al cerrar la sesion', err);
    }
}

function handleResize() {
    if (window.innerWidth < 768) {
        isSidebarCollapsed.value = false;
    }
}

onMounted(() => {
    window.addEventListener('resize', handleResize);
    handleResize();
});
onBeforeUnmount(() => {
    window.removeEventListener('resize', handleResize);
});
</script>

<template>
    <div class="h-screen flex flex-col">
    <!-- Header principal -->
    <header class="bg-gradient-to-r from-white to-white text-black shadow-md fixed top-0 left-0 w-full z-50">
        <div class="px-6 py-3 flex justify-between items-center">
            <!-- Botón menú hamburguesa SOLO en móvil -->
            <button @click="isSidebarOpen = !isSidebarOpen"
                class="block md:hidden text-black mr-3"
                style="font-size: 150%;">
                <FontAwesomeIcon :icon="faList"/>
            </button>
            <div class="text-xl font-semibold text-black">
                <Link :href="route('dashboard')" class="text-xl font-semibold text-black flex items-center cursor-pointer select-none">
                    <img src="../../../imagenes/logo.jpg" class="w-22 h-7 inline-block align-middle" />
                </Link>
            </div>
            <!--Datos de la sesion-->
            <div class="flex items-center space-x-4">
                <div class="relative group">
                    <button @click="login" class="text-black text-xl" title="Usuario">
                        <FontAwesomeIcon :icon="faUser"/>
                    </button>
                </div>
                <div class="relative group">
                    <button @click="logout" class="text-black text-xl" title="Cerrar sesión">
                        <FontAwesomeIcon :icon="faDoorOpen"/>
                    </button>
                </div>
            </div>
        </div>
    </header>
    <div class="flex flex-1">
        <!-- Sidebar -->
        <aside
            :class="[
                isSidebarCollapsed ? 'w-16' : 'w-40',
                'fixed top-12 left-0 h-[calc(100vh-64px)] bg-gradient-to-b from-red-500 to-red-400 text-black transition-all duration-300 ease-in-out shadow-lg z-40 pt-8 flex flex-col',
                isSidebarOpen ? 'block' : 'hidden','md:flex']">
            <!-- Botón colapsar/expandir SOLO en desktop -->
            <button
                @click="toggleSidebar"
                class="mx-auto mb-6 p-2 rounded-full bg-white text-red-500 hover:bg-red-100 transition hidden md:flex items-center justify-center"
                :title="isSidebarCollapsed ? 'Expandir Menú' : 'Reducir Menú'">
                <FontAwesomeIcon :icon="faTableList" :class="isSidebarCollapsed" class="h-5"/>
            </button>
            <div class="px-4 text-xl font-bold text-white flex justify-center items-center" v-if="!isSidebarCollapsed">
                <span>MENÚ</span>
            </div>
            <nav class="mt-4 text-white flex-1">
                <ul>
                    <li class="px-4 py-3 hover:bg-orange-600 flex items-center"
                        :class="isSidebarCollapsed ? 'justify-center' : 'justify-start'">
                        <Link
                            :href="route('dashboard')"
                            class="flex items-center"
                            :class="isSidebarCollapsed ? 'justify-center w-auto' : 'w-full justify-start'" title="Inicio">
                            <FontAwesomeIcon :icon="faHome" :class="isSidebarCollapsed ? '' : 'mr-3'" class="h-6" />
                            <span v-if="!isSidebarCollapsed">Inicio</span>
                        </Link>
                    </li>
                    <li class="px-0 py-3 flex flex-col relative">
                        <!-- Botón Catálogos alineado -->
                        <button @click="toggleDropdown"
                            class="flex items-center px-4 py-2 hover:bg-orange-600 focus:outline-none w-full"
                            :class="[
                                isSidebarCollapsed ? 'justify-center w-auto mx-auto' : 'w-full justify-start',
                                (isOpen && isSidebarCollapsed) ? 'bg-orange-700 text-white' : ''
                            ]"
                            title="Catálogos">
                            <FontAwesomeIcon :icon="faList" :class="isSidebarCollapsed ? '' : 'mr-3'" class="h-6"/>
                            <span v-if="!isSidebarCollapsed">Catálogos</span>
                            <FontAwesomeIcon v-if="!isSidebarCollapsed" :icon="faChevronCircleDown"
                                class="ml-2 transition-transform h-5" :class="{'rotate-90': isOpen}"/>
                        </button>
                        <!-- Menú desplegable como burbujas fuera del aside -->
                        <transition name="fade">
                            <div
                                v-if="isOpen && isSidebarCollapsed"
                                class="absolute left-full top-1/2 -translate-y-1/2 flex flex-col space-y-2 z-50">
                                <Link :href="route('productos')" title="Categorías"
                                    class="bg-white text-red-500 rounded-full shadow-lg w-20 h-12 flex items-center justify-center hover:bg-orange-600 hover:text-white transition">
                                    <FontAwesomeIcon :icon="faLayerGroup" size="lg"/>
                                    <h1>&nbsp;Productos</h1>
                                </Link>
                                <Link :href="route('dashboard')" title="Tours"
                                    class="bg-white text-red-500 rounded-full shadow-lg w-20 h-12 flex items-center justify-center hover:bg-orange-600 hover:text-white transition">
                                    <FontAwesomeIcon :icon="faTags" size="lg"/>
                                    <h1>&nbsp;Tours</h1>
                                </Link>
                                <Link :href="route('dashboard')" title="Tours"
                                    class="bg-white text-red-500 rounded-full shadow-lg w-20 h-12 flex items-center justify-center hover:bg-orange-600 hover:text-white transition">
                                    <FontAwesomeIcon :icon="faTags" size="lg"/>
                                    <h1>&nbsp;Tours</h1>
                                </Link>
                            </div>
                            <!-- Menú normal cuando el aside está expandido -->
                            <ul
                                v-else-if="isOpen"
                                class="w-full rounded-md shadow-lg overflow-hidden">
                                <li class="flex items-center px-5 py-2 hover:bg-orange-600 justify-start">
                                    <Link :href="route('productos')" class="flex items-center" title="Categorías">
                                        <FontAwesomeIcon :icon="faLayerGroup" />
                                        <span class="ml-3">Productos</span>
                                    </Link>
                                </li>
                                <li class="flex items-center px-5 py-2 hover:bg-orange-600 justify-start">
                                    <Link :href="route('dashboard')" class="flex items-center" title="Tours">
                                        <FontAwesomeIcon :icon="faTags" />
                                        <span class="ml-3">Tours</span>
                                    </Link>
                                </li>
                                <li class="flex items-center px-5 py-2 hover:bg-orange-600 justify-start">
                                    <Link :href="route('dashboard')" class="flex items-center" title="Tours">
                                        <FontAwesomeIcon :icon="faTags" />
                                        <span class="ml-3">Otros</span>
                                    </Link>
                                </li>
                            </ul>
                        </transition>
                    </li>
                    <li class="px-4 py-3 hover:bg-orange-600 flex items-center"
                        :class="isSidebarCollapsed ? 'justify-center' : 'justify-start'">
                        <Link :href="route('productos')"
                            class="flex items-center" title="Productos"
                            :class="isSidebarCollapsed ? 'justify-center w-auto' : 'w-full justify-start'">
                            <FontAwesomeIcon :icon="faStoreAlt" :class="isSidebarCollapsed ? '' : 'mr-3'" class="h-6"/>
                            <span v-if="!isSidebarCollapsed">Productos</span>
                        </Link>
                    </li>
                    <li class="px-5 py-3 hover:bg-orange-600 flex items-center"
                        :class="isSidebarCollapsed ? 'justify-center' : 'justify-start'">
                        <Link :href="route('reservatours')"
                            class="flex items-center" title="Reservas"
                            :class="isSidebarCollapsed ? 'justify-center w-auto' : 'w-full justify-start'">
                            <FontAwesomeIcon :icon="faReceipt" :class="isSidebarCollapsed ? '' : 'mr-3'" class="h-6"/>
                            <span v-if="!isSidebarCollapsed">Reservas</span>
                        </Link>
                    </li>
                    <li class="px-5 py-3 hover:bg-orange-600 flex items-center"
                        :class="isSidebarCollapsed ? 'justify-center' : 'justify-start'">
                        <Link :href="route('hoteles')"
                            class="flex items-center" title="Hoteles"
                            :class="isSidebarCollapsed ? 'justify-center w-auto' : 'w-full justify-start'">
                            <FontAwesomeIcon :icon="faHotel" :class="isSidebarCollapsed ? '' : 'mr-3'" class="h-6"/>
                            <span v-if="!isSidebarCollapsed">Hoteles</span>
                        </Link>
                    </li>
                    <li class="px-5 py-3 hover:bg-orange-600 flex items-center"
                        :class="isSidebarCollapsed ? 'justify-center' : 'justify-start'">
                        <!--<Link :href="route('reservas.rango')"-->
                        <Link :href="route('dashboard')"
                            class="flex items-center" title="Reportes"
                            :class="isSidebarCollapsed ? 'justify-center w-auto' : 'w-full justify-start'">
                            <FontAwesomeIcon :icon="faFileAlt" :class="isSidebarCollapsed ? '' : 'mr-3'" class="h-6"/>
                            <span v-if="!isSidebarCollapsed">Reportes</span>
                        </Link>
                    </li>
                </ul>
            </nav>
        </aside>
        <!-- Overlay para cerrar el menú en móvil -->
        <div
            v-if="isSidebarOpen"
            class="fixed inset-0 bg-black bg-opacity-40 z-30 md:hidden"
            @click="isSidebarOpen = false">
        </div>
        <!-- Contenedor para header secundario y contenido -->
        <div class="md:ml-16 flex-1 flex flex-col">
            <!-- Header secundario -->
            <header class="bg-gradient-to-r from-red-500 to-red-300 shadow px-4 py-2 flex items-center 
                z-30 relative md:fixed top-14 md:top-12 lg:14 md:right-0 md:px-8
                md:left-16">
                <h2 class="text-lg font-semibold text-white">Dashboard</h2>
            </header>
            <!-- Contenido principal con padding para no quedar debajo de los headers -->
            <main class="flex-1 p-4 pt-16 md:p-10 md:pt-20 overflow-auto bg-white">
                <slot />
            </main>
        </div>
    </div>
    <!--Div para el contenido dinamico-->
    <!--fin paradiv para sidebar y contenido dinamico-->
   </div>
</template>
