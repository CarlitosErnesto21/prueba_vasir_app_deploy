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
    faChevronCircleDown, faHouseChimney, faTableList, faStoreAlt, faReceipt, faUser, faHome} from "@fortawesome/free-solid-svg-icons";
import axios from 'axios';

const page = usePage();
//const user = page.props.auth.user;
const isSidebarOpen = ref(false);
const isSidebarCollapsed = ref(true); // Colapsado por defecto
const anioCurrent = ref(new Date().getFullYear());
const isOpen = ref(false);
const toggleDropdown = () => { isOpen.value = !isOpen.value; };
const toggleSidebar = () => {
    isSidebarCollapsed.value = !isSidebarCollapsed.value;
    if (isSidebarCollapsed.value) {
        isOpen.value = false; // Cierra el menú de catálogos al colapsar el aside
    }
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

function handleResize() {
    if (window.innerWidth < 768) {
        isSidebarCollapsed.value = true; // Siempre colapsado
        isSidebarOpen.value = false;     // Siempre cerrado en móvil
    } else {
        isSidebarCollapsed.value = true; // Siempre colapsado en desktop también
        isSidebarOpen.value = false;
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
                    <li
                        class="px-4 py-3 hover:bg-orange-600 flex items-center cursor-pointer"
                        :class="isSidebarCollapsed ? 'justify-center' : 'justify-start'"
                        @click="$inertia.visit(route('dashboard'))"
                        tabindex="0"
                        @keydown.enter="$inertia.visit(route('dashboard'))"
                        title="Inicio"
                    >
                        <FontAwesomeIcon :icon="faHome" :class="isSidebarCollapsed ? '' : 'mr-3'" class="h-6" />
                        <span v-if="!isSidebarCollapsed">Inicio</span>
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
                                class="absolute left-full top-1/2 -translate-y-1/2 flex flex-col items-center space-y-3 z-50"
                                style="min-width: 120px;">
                                <Link
                                    :href="route('productos')"
                                    title="Categorías"
                                    class="bg-white text-red-500 rounded-full shadow-lg flex items-center justify-center hover:bg-orange-600 hover:text-white transition"
                                    style="min-width: 110px; min-height: 48px; padding: 0 18px;">
                                    <FontAwesomeIcon :icon="faLayerGroup" size="lg"/>
                                    <span class="ml-2 whitespace-nowrap">Productos</span>
                                </Link>
                                <Link
                                    :href="route('dashboard')"
                                    title="Tours"
                                    class="bg-white text-red-500 rounded-full shadow-lg flex items-center justify-center hover:bg-orange-600 hover:text-white transition"
                                    style="min-width: 110px; min-height: 48px; padding: 0 18px;">
                                    <FontAwesomeIcon :icon="faTags" size="lg"/>
                                    <span class="ml-2 whitespace-nowrap">Tours</span>
                                </Link>
                                <Link
                                    :href="route('dashboard')"
                                    title="Otros"
                                    class="bg-white text-red-500 rounded-full shadow-lg flex items-center justify-center hover:bg-orange-600 hover:text-white transition"
                                    style="min-width: 110px; min-height: 48px; padding: 0 18px;">
                                    <FontAwesomeIcon :icon="faTags" size="lg"/>
                                    <span class="ml-2 whitespace-nowrap">Otros</span>
                                </Link>
                            </div>
                            <!-- Menú normal cuando el aside está expandido -->
                            <ul
                                v-else-if="isOpen"
                                class="w-full rounded-md shadow-lg overflow-hidden">
                                <li
                                    class="flex items-center px-5 py-2 hover:bg-orange-600 justify-start cursor-pointer"
                                    @click="$inertia.visit(route('productos'))"
                                    tabindex="0"
                                    @keydown.enter="$inertia.visit(route('productos'))"
                                    title="Categorías">
                                    <FontAwesomeIcon :icon="faLayerGroup" />
                                    <span class="ml-3">Productos</span>
                                </li>
                                <li
                                    class="flex items-center px-5 py-2 hover:bg-orange-600 justify-start cursor-pointer"
                                    @click="$inertia.visit(route('dashboard'))"
                                    tabindex="0"
                                    @keydown.enter="$inertia.visit(route('dashboard'))"
                                    title="Tours">
                                    <FontAwesomeIcon :icon="faTags" />
                                    <span class="ml-3">Tours</span>
                                </li>
                                <li
                                    class="flex items-center px-5 py-2 hover:bg-orange-600 justify-start cursor-pointer"
                                    @click="$inertia.visit(route('dashboard'))"
                                    tabindex="0"
                                    @keydown.enter="$inertia.visit(route('dashboard'))"
                                    title="Otros">
                                    <FontAwesomeIcon :icon="faTags" />
                                    <span class="ml-3">Otros</span>
                                </li>
                            </ul>
                        </transition>
                    </li>
                    <li
                        class="px-4 py-3 hover:bg-orange-600 flex items-center cursor-pointer"
                        :class="isSidebarCollapsed ? 'justify-center' : 'justify-start'"
                        @click="$inertia.visit(route('productos'))"
                        tabindex="0"
                        @keydown.enter="$inertia.visit(route('productos'))"
                        title="Productos">
                        <FontAwesomeIcon :icon="faStoreAlt" :class="isSidebarCollapsed ? '' : 'mr-3'" class="h-6"/>
                        <span v-if="!isSidebarCollapsed">Productos</span>
                    </li>
                    <li
                        class="px-5 py-3 hover:bg-orange-600 flex items-center cursor-pointer"
                        :class="isSidebarCollapsed ? 'justify-center' : 'justify-start'"
                        @click="$inertia.visit(route('reservatours'))"
                        tabindex="0"
                        @keydown.enter="$inertia.visit(route('reservatours'))"
                        title="Reservas">
                        <FontAwesomeIcon :icon="faReceipt" :class="isSidebarCollapsed ? '' : 'mr-3'" class="h-6"/>
                        <span v-if="!isSidebarCollapsed">Reservas</span>
                    </li>
                    <li
                        class="px-5 py-3 hover:bg-orange-600 flex items-center cursor-pointer"
                        :class="isSidebarCollapsed ? 'justify-center' : 'justify-start'"
                        @click="$inertia.visit(route('dashboard'))"
                        tabindex="0"
                        @keydown.enter="$inertia.visit(route('dashboard'))"
                        title="Reportes">
                        <FontAwesomeIcon :icon="faFileAlt" :class="isSidebarCollapsed ? '' : 'mr-3'" class="h-6"/>
                        <span v-if="!isSidebarCollapsed">Reportes</span>
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

<!-- No hay campos de formulario en este layout, pero si agregas alguno, asegúrate de incluir siempre un atributo id o name único en cada input/select/textarea. 
Por ejemplo:
<input id="busqueda" name="busqueda" ... />
<label for="busqueda">Buscar</label>
-->
