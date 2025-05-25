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
    faChevronDown, faHouseChimney, faTableList, faStoreAlt, faReceipt } from "@fortawesome/free-solid-svg-icons";
import axios from 'axios';
import Avatar from 'primevue/avatar';


const page = usePage();
const user = page.props.auth.user;
const isSidebarOpen = ref(false);
const isSidebarCollapsed = ref(true); // Colapsado por defecto
const anioActual = ref(new Date().getFullYear());
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
                VAS<img src="../../../imagenes/logovasir.png" class="w-5 h-5 inline-block align-middle">R
            </div>
            <!--Datos de la sesion-->
            <div class="flex items-center space-x-4">
                <div class="relative group">
                            <Avatar image="/images/vasir.ico" class="!w-5 !h-5" size="small" shape="circle" />
                    <!-- Tooltip -->
                    <div class="absolute mt-5 w-80 bg-white text-black text-sm rounded-xl shadow-lg 
                        p-3 justify-center text-center opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-opacity duration-300"
                        style="margin-left: -215px;">
                        <p><strong>Usuario:</strong>&nbsp;{{ user.name }}</p>
                        <p><strong>Correo:</strong>&nbsp;{{ user.email }}</p>
                    </div>
                </div>
                <div class="relative group">
                        <button @click="logout" class="text-black text-xl">
                            <FontAwesomeIcon :icon="faDoorOpen"/>
                        </button>
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
        <!-- Sidebar -->
        <aside
            :class="[
                isSidebarCollapsed ? 'w-16' : 'w-36',
                'fixed top-12 left-0 h-[calc(100vh-64px)] bg-gradient-to-b from-red-500 to-red-400 text-black transition-all duration-300 ease-in-out shadow-lg z-40 pt-8 flex flex-col',
                isSidebarOpen ? 'block' : 'hidden',
                'md:flex'
            ]"
        >
            <!-- Botón colapsar/expandir SOLO en desktop -->
            <button
                @click="toggleSidebar"
                class="mx-auto mb-6 p-2 rounded-full bg-white text-red-500 hover:bg-red-100 transition hidden md:flex items-center justify-center"
                :title="isSidebarCollapsed ? 'Expandir menú' : 'Colapsar menú'"
            >
                <FontAwesomeIcon :icon="faChevronDown" :class="isSidebarCollapsed ? 'rotate-90' : '-rotate-90'" />
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
                            :class="isSidebarCollapsed ? 'justify-center w-auto' : 'w-full justify-start'">
                            <FontAwesomeIcon :icon="faHouseChimney" :class="isSidebarCollapsed ? '' : 'mr-3'" />
                            <span v-if="!isSidebarCollapsed">Inicio</span>
                        </Link>
                    </li>
                    <li class="px-0 py-3 flex flex-col">
                        <!-- Botón Catálogos alineado -->
                        <button @click="toggleDropdown"
                            class="flex items-center px-4 py-2 hover:bg-orange-600 focus:outline-none w-full justify-start"
                            :class="isSidebarCollapsed ? 'justify-center w-auto mx-auto' : 'w-full justify-start'">
                            <FontAwesomeIcon :icon="faTableList" :class="isSidebarCollapsed ? '' : 'mr-3'" />
                            <span v-if="!isSidebarCollapsed">Catálogos</span>
                            <FontAwesomeIcon v-if="!isSidebarCollapsed" :icon="faChevronDown"
                                class="ml-2 transition-transform" :class="{'rotate-90': isOpen}" />
                        </button>
                        <!-- Menú desplegable alineado -->
                        <transition name="expand">
                            <ul v-if="isOpen && !isSidebarCollapsed" class="w-full rounded-md shadow-lg overflow-hidden">
                                <li class="flex items-center px-5 py-2 hover:bg-orange-600"
                                    :class="isSidebarCollapsed ? 'justify-center' : 'justify-start'">
                                    <Link href="route('welcome')"
                                        class="flex items-center"
                                        :class="isSidebarCollapsed ? 'justify-center w-auto' : 'w-full justify-start'">
                                        <FontAwesomeIcon :icon="faLayerGroup" :class="isSidebarCollapsed ? '' : 'mr-3'" />
                                        <span v-if="!isSidebarCollapsed">Categorías</span>
                                    </Link>
                                </li>
                                <li class="flex items-center px-5 py-2 hover:bg-orange-600"
                                    :class="isSidebarCollapsed ? 'justify-center' : 'justify-start'">
                                    <Link :href="route('tours')"
                                        class="flex items-center"
                                        :class="isSidebarCollapsed ? 'justify-center w-auto' : 'w-full justify-start'">
                                        <FontAwesomeIcon :icon="faTags" :class="isSidebarCollapsed ? '' : 'mr-3'" />
                                        <span v-if="!isSidebarCollapsed">Tours</span>
                                    </Link>
                                </li>
                            </ul>
                        </transition>
                    </li>
                    <li class="px-4 py-3 hover:bg-orange-600 flex items-center"
                        :class="isSidebarCollapsed ? 'justify-center' : 'justify-start'">
                        <Link href="route('productos')"
                            class="flex items-center"
                            :class="isSidebarCollapsed ? 'justify-center w-auto' : 'w-full justify-start'">
                            <FontAwesomeIcon :icon="faStoreAlt" :class="isSidebarCollapsed ? '' : 'mr-3'" />
                            <span v-if="!isSidebarCollapsed">Productos</span>
                        </Link>
                    </li>
                    <li class="px-5 py-3 hover:bg-orange-600 flex items-center"
                        :class="isSidebarCollapsed ? 'justify-center' : 'justify-start'">
                        <Link href="route('reservas')"
                            class="flex items-center"
                            :class="isSidebarCollapsed ? 'justify-center w-auto' : 'w-full justify-start'">
                            <FontAwesomeIcon :icon="faReceipt" :class="isSidebarCollapsed ? '' : 'mr-3'" />
                            <span v-if="!isSidebarCollapsed">Reservas</span>
                        </Link>
                    </li>
                    <li class="px-5 py-3 hover:bg-orange-600 flex items-center"
                        :class="isSidebarCollapsed ? 'justify-center' : 'justify-start'">
                        <Link href="route('reservas.rango')"
                            class="flex items-center"
                            :class="isSidebarCollapsed ? 'justify-center w-auto' : 'w-full justify-start'">
                            <FontAwesomeIcon :icon="faFileAlt" :class="isSidebarCollapsed ? '' : 'mr-3'" />
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
            @click="isSidebarOpen = false"
        ></div>
        <!-- Contenedor para header secundario y contenido -->
        <div :class="[isSidebarCollapsed ? 'md:ml-16' : 'md:ml-36', 'flex-1 flex flex-col']">
            <!-- Header secundario -->
            <header
    class="bg-gradient-to-r from-red-500 to-red-300 shadow px-4 py-2 flex items-center z-30 relative md:fixed top-14 md:top-12 lg:14 md:right-0 md:px-8"
    :class="isSidebarCollapsed ? 'md:left-16' : 'md:left-36'"
>
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
