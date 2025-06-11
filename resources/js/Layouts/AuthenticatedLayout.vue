<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import { FontAwesomeIcon, } from "@fortawesome/vue-fontawesome";
import { faList, faTags, faRoute, faDoorOpen, faFileAlt, faLayerGroup, faCircleXmark, faUserCircle, 
    faChevronCircleDown, faUser, faHotel, faPlaneDeparture, faGear, faBoxesStacked, 
    faClipboardList, faBox, faHouseChimneyUser, faBarsProgress,
    faFileInvoice,
    faChartBar,
    faFileLines,
    faChartColumn,
    faChartLine,
    faChartDiagram,
    faChartGantt,
    faChartArea,
    faFileCircleCheck,
    faFileArchive} from "@fortawesome/free-solid-svg-icons";
import axios from 'axios';
import { route } from 'ziggy-js';

const page = usePage();
const user = page.props.auth?.user || { name: 'Usuario', email: 'correo@ejemplo.com' };
const isSidebarOpen = ref(false);
const isSidebarCollapsed = ref(true); // Colapsado por defecto
const anioCurrent = ref(new Date().getFullYear());
const isOpen = ref(false);
const toggleDropdown = () => { isOpen.value = !isOpen.value; };
const toggleSidebar = () => {
    isSidebarCollapsed.value = !isSidebarCollapsed.value;
    isOpen.value = false; // Cierra el dropdown al colapsar/expandir el sidebar
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

// Cierra burbujitas y aside al hacer clic fuera
function handleGlobalClick(e) {
    // Cierra el aside expandido (desktop o móvil) si el clic no fue dentro del aside ni en el botón hamburguesa
    const aside = document.querySelector('aside');
    const hamburger = document.querySelector('button.block.md\\:hidden');
    if (
        (isSidebarOpen.value || !isSidebarCollapsed.value) &&
        aside &&
        !aside.contains(e.target) &&
        (!hamburger || !hamburger.contains(e.target))
    ) {
        isSidebarOpen.value = false;
        isSidebarCollapsed.value = true;
    }
    // Si las burbujitas están abiertas y el clic no fue dentro del aside ni en el botón catálogos, ciérralas
    if (isOpen.value) {
        const catalogBtn = document.querySelector('button[title="Catálogos"]');
        if (
            (!aside || !aside.contains(e.target)) &&
            (!catalogBtn || !catalogBtn.contains(e.target))
        ) {
            isOpen.value = false;
        }
    }
}

const showProfileMenu = ref(false);

const openProfileMenu = (e) => {
    e.stopPropagation();
    showProfileMenu.value = true;
};
const closeProfileMenu = () => {
    showProfileMenu.value = false;
};

function handleProfileMenuClick(e) {
    const menu = document.getElementById('profile-menu');
    if (showProfileMenu.value && menu && !menu.contains(e.target)) {
        showProfileMenu.value = false;
    }
}

onMounted(() => {
    window.addEventListener('resize', handleResize);
    handleResize();
    document.addEventListener('click', handleGlobalClick, true);
    document.addEventListener('click', handleProfileMenuClick, true);
});
onBeforeUnmount(() => {
    window.removeEventListener('resize', handleResize);
    document.removeEventListener('click', handleGlobalClick, true);
    document.removeEventListener('click', handleProfileMenuClick, true);
});
</script>

<template>
    <div class="h-screen flex flex-col">
        <!-- Header principal -->
        <header class="bg-gradient-to-r from-white to-white backdrop-blur-sm text-black shadow-md fixed top-0 left-0 w-full z-50">
            <div class="px-6 py-3 flex justify-between items-center">
                <!-- Botón menú hamburguesa SOLO en móvil -->
                <button @click="isSidebarOpen = !isSidebarOpen"
                    class="block md:hidden mr-3 rounded-full p-2 bg-white/80 shadow-lg border border-red-200 hover:bg-red-600 hover:text-white transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-400 group"
                    aria-label="Abrir menú de navegación">
                    <span class="sr-only">Abrir menú</span>
                    <span class="flex items-center justify-center w-6 h-6">
                        <FontAwesomeIcon :icon="faBarsProgress" class="w-6 h-6 text-red-700 group-hover:text-white transition-colors duration-200" />
                    </span>
                </button>
                <div class="text-xl font-semibold text-black">
                    <Link :href="route('dashboard')" class="text-xl font-semibold text-black flex items-center cursor-pointer select-none">
                        <img src="../../../imagenes/logo.png" class="w-25 h-10 inline-block align-middle" />
                    </Link>
                </div>
                <!--Datos de la sesion-->
                <div class="flex items-center space-x-4">
                    <div class="relative group">
                        <button @click="openProfileMenu" class="text-black text-xl" title="Usuario">
                            <span class="font-semibold text-base text-gray-700 mr-2">Perfil</span>
                            <FontAwesomeIcon :icon="faUser" class="drop-shadow-md" />
                        </button>
                        <!-- Menú de perfil tipo Chrome -->
                        <transition name="fade">
                            <div v-if="showProfileMenu" id="profile-menu" class="absolute right-0 mt-2 w-80 bg-gradient-to-br from-red-200 via-red-100 to-red-200 rounded-xl shadow-2xl border border-gray-200 z-50 overflow-hidden">
                                <div class="bg-gradient-to-br from-red-100 via-red-50 to-red-100 rounded-xl shadow-2xl border border-gray-200 flex flex-col items-center py-4 px-4">
                                    <img :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}`" class="w-16 h-16 rounded-full border mb-2" alt="avatar" />
                                    <div class="font-bold text-lg">{{ user.name }} • Administrador</div>
                                    <div class="text-gray-500 text-sm">{{ user.email }}</div>
                                </div>
                                <div class="divide-y divide-gray-200">
                                    <div class="py-2 px-4 flex flex-col gap-1">
                                        <button class="flex items-center gap-2 text-left w-full hover:bg-red-300 rounded px-2 py-1 text-gray-700">
                                            <FontAwesomeIcon :icon="faUserCircle" class="drop-shadow-md" /> Contraseñas y Autocompletar
                                        </button>
                                        <button class="flex items-center gap-2 text-left w-full hover:bg-red-300 rounded px-2 py-1 text-gray-700">
                                            <FontAwesomeIcon :icon="faTags" class="drop-shadow-md" /> Gestionar tu cuenta
                                        </button>
                                        <button class="flex items-center gap-2 text-left w-full hover:bg-red-300 rounded px-2 py-1 text-gray-700">
                                            <FontAwesomeIcon :icon="faLayerGroup" class="drop-shadow-md" /> Personalizar perfil
                                        </button>
                                        <button class="flex items-center gap-2 text-left w-full hover:bg-red-300 rounded px-2 py-1 text-gray-700">
                                            <FontAwesomeIcon :icon="faCircleXmark" class="drop-shadow-md" /> Sincronización activada
                                        </button>
                                    </div>
                                    <div class="py-2 px-4">
                                        <div class="text-xs text-gray-500 mb-1">Otros perfiles</div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <img src="https://ui-avatars.com/api/?name=ITCHA" class="w-7 h-7 rounded-full border" />
                                            <span class="text-sm">Carlos Ernesto (ITCHA)</span>
                                        </div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="bg-purple-500 w-7 h-7 rounded-full flex items-center justify-center text-white font-bold">F</span>
                                            <span class="text-sm">Familia</span>
                                        </div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="bg-pink-500 w-7 h-7 rounded-full flex items-center justify-center text-white font-bold">L</span>
                                            <span class="text-sm">Lyly</span>
                                        </div>
                                    </div>
                                    <div class="py-2 px-4 flex flex-col gap-1">
                                        <button class="flex items-center gap-2 text-left w-full hover:bg-red-300 rounded px-2 py-1 text-gray-700">
                                            <FontAwesomeIcon :icon="faUserCircle" class="drop-shadow-md" /> Añadir perfil
                                        </button>
                                        <button class="flex items-center gap-2 text-left w-full hover:bg-red-300 rounded px-2 py-1 text-gray-700">
                                            <FontAwesomeIcon :icon="faUser" class="drop-shadow-md" /> Perfil de invitado abierto
                                        </button>
                                        <button class="flex items-center gap-2 text-left w-full hover:bg-red-300 rounded px-2 py-1 text-gray-700">
                                            <FontAwesomeIcon :icon="faTags" class="drop-shadow-md" /> Gestionar perfiles
                                        </button>
                                        <button class="flex items-center gap-2 text-left w-full hover:bg-red-300 rounded px-2 py-1 text-red-600" @click="logout">
                                            <FontAwesomeIcon :icon="faDoorOpen" class="drop-shadow-md" /> Cerrar sesión
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </transition>
                    </div>
                </div>
            </div>
        </header>
        <div class="flex flex-1">
            <!-- Sidebar -->
            <aside
                :class="[
                    isSidebarCollapsed ? 'w-16' : 'w-56',
                    'fixed top-16 left-0 h-[calc(100vh-64px)] bg-gradient-to-b from-red-700 via-red-600 to-red-400 text-white transition-all duration-300 ease-in-out shadow-lg z-40 pt-8 flex flex-col',
                    isSidebarOpen ? 'block' : 'hidden','md:flex']">
                <!-- Botón colapsar/expandir SOLO en desktop -->
                <button
                    @click="toggleSidebar"
                    class="mx-auto mb-6 p-2 rounded-full bg-white text-red-700 hover:bg-red-100 transition hidden md:flex items-center justify-center border border-red-200"
                    :title="isSidebarCollapsed ? 'Expandir Menú' : 'Reducir Menú'">
                    <FontAwesomeIcon :icon="faBarsProgress" :class="[isSidebarCollapsed ? '' : 'mr-0', 'drop-shadow-md']" class="h-5" />
                </button>
                <div class="px-4 text-xl font-bold text-white flex justify-center items-center" v-if="!isSidebarCollapsed">
                    <span>MENÚ</span>
                </div>
                <nav class="mt-4 text-white flex-1">
                    <ul>
                        <li
                            class="px-4 py-3 hover:bg-red-600 flex items-center cursor-pointer"
                            :class="isSidebarCollapsed ? 'justify-center' : 'justify-start'"
                            @click="$inertia.visit(route('dashboard'))"
                            tabindex="0"
                            @keydown.enter="$inertia.visit(route('dashboard'))"
                            title="Inicio"
                        >
                            <FontAwesomeIcon :icon="faHouseChimneyUser" :class="[isSidebarCollapsed ? '' : 'mr-3', 'drop-shadow-md']" class="h-6" />
                            <span v-if="!isSidebarCollapsed">Inicio</span>
                        </li>
                        <li class="px-0 py-3 flex flex-col relative">
                            <!-- Botón Catálogos alineado -->
                            <button @click="toggleDropdown"
                                class="flex items-center px-4 py-2 hover:bg-red-600 focus:outline-none w-full"
                                :class="[
                                    isSidebarCollapsed ? 'justify-center w-auto mx-auto' : 'w-full justify-start',
                                    (isOpen && isSidebarCollapsed) ? 'bg-red-700 text-white' : ''
                                ]"
                                title="Catálogos">
                                <FontAwesomeIcon :icon="faBoxesStacked" :class="[isSidebarCollapsed ? '' : 'mr-3', 'drop-shadow-md']" class="h-6"/>
                                <span v-if="!isSidebarCollapsed">Catálogos</span>
                                <FontAwesomeIcon v-if="!isSidebarCollapsed" :icon="faChevronCircleDown"
                                    class="ml-2 transition-transform h-5 drop-shadow-md" :class="{'rotate-90': isOpen}"/>
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
                                        class="bg-white text-red-700 rounded-full shadow-lg flex items-center justify-center hover:bg-red-600 hover:text-white transition"
                                        style="min-width: 110px; min-height: 48px; padding: 0 18px;">
                                        <FontAwesomeIcon :icon="faBox" size="lg" class="drop-shadow-md"/>
                                        <span class="ml-2 whitespace-nowrap">Productos</span>
                                    </Link>
                                    <Link
                                        :href="route('tours')"
                                        title="Tours"
                                        class="bg-white text-red-700 rounded-full shadow-lg flex items-center justify-center hover:bg-red-600 hover:text-white transition"
                                        style="min-width: 110px; min-height: 48px; padding: 0 18px;">
                                        <FontAwesomeIcon :icon="faRoute" size="lg" class="drop-shadow-md"/>
                                        <span class="ml-2 whitespace-nowrap">Tours</span>
                                    </Link>
                                    <Link
                                        :href="route('hoteles')"
                                        title="Hoteles"
                                        class="bg-white text-red-700 rounded-full shadow-lg flex items-center justify-center hover:bg-red-600 hover:text-white transition"
                                        style="min-width: 110px; min-height: 48px; padding: 0 18px;">
                                        <FontAwesomeIcon :icon="faHotel" size="lg" class="drop-shadow-md"/>
                                        <span class="ml-2 whitespace-nowrap">Hoteles</span>
                                    </Link>
                                    <Link
                                        :href="route('aerolineas')"
                                        title="Aerolineas"
                                        class="bg-white text-red-700 rounded-full shadow-lg flex items-center justify-center hover:bg-red-600 hover:text-white transition"
                                        style="min-width: 110px; min-height: 48px; padding: 0 18px;">
                                        <FontAwesomeIcon :icon="faPlaneDeparture" size="lg" class="drop-shadow-md"/>
                                        <span class="ml-2 whitespace-nowrap">Aerolineas</span>
                                    </Link>
                                </div>
                                <!-- Menú normal cuando el aside está expandido -->
                                <ul
                                    v-else-if="isOpen"
                                    class="w-full rounded-md shadow-lg overflow-hidden bg-white text-red-700">
                                    <li
                                        class="flex items-center px-5 py-2 hover:bg-red-600 hover:text-white justify-start cursor-pointer"
                                        @click="$inertia.visit(route('productos'))"
                                        tabindex="0"
                                        @keydown.enter="$inertia.visit(route('productos'))"
                                        title="Categorías">
                                        <FontAwesomeIcon :icon="faBox" class="drop-shadow-md" />
                                        <span class="ml-3">Productos</span>
                                    </li>
                                    <li
                                        class="flex items-center px-5 py-2 hover:bg-red-600 hover:text-white justify-start cursor-pointer"
                                        @click="$inertia.visit(route('tours'))"
                                        tabindex="0"
                                        @keydown.enter="$inertia.visit(route('tours'))"
                                        title="Tours">
                                        <FontAwesomeIcon :icon="faRoute" class="drop-shadow-md"/>
                                        <span class="ml-3">Tours</span>
                                    </li>
                                    <li
                                        class="flex items-center px-5 py-2 hover:bg-red-600 hover:text-white justify-start cursor-pointer"
                                        @click="$inertia.visit(route('hoteles'))"
                                        tabindex="0"
                                        @keydown.enter="$inertia.visit(route('hoteles'))"
                                        title="Otros">
                                        <FontAwesomeIcon :icon="faHotel" class="drop-shadow-md"/>
                                        <span class="ml-3">Hoteles</span>
                                    </li>
                                    <li
                                        class="flex items-center px-5 py-2 hover:bg-red-600 hover:text-white justify-start cursor-pointer"
                                        @click="$inertia.visit(route('aerolineas'))"
                                        tabindex="0"
                                        @keydown.enter="$inertia.visit(route('aerolineas'))"
                                        title="Aerolineas">
                                        <FontAwesomeIcon :icon="faPlaneDeparture" class="drop-shadow-md"/>
                                        <span class="ml-3">Aerolineas</span>
                                    </li>
                                </ul>
                            </transition>
                        </li>
                        <li
                            class="px-4 py-3 hover:bg-red-600 flex items-center cursor-pointer"
                            :class="isSidebarCollapsed ? 'justify-center' : 'justify-start'"
                            @click="$inertia.visit(route('dashboard'))"
                            tabindex="0"
                            @keydown.enter="$inertia.visit(route('dashboard'))"
                            title="Otros">
                            <FontAwesomeIcon :icon="faTags" :class="[isSidebarCollapsed ? '' : 'mr-3', 'drop-shadow-md']" class="h-6"/>
                            <span v-if="!isSidebarCollapsed">Otros</span>
                        </li>
                        <li
                            class="px-5 py-3 hover:bg-red-600 flex items-center cursor-pointer"
                            :class="isSidebarCollapsed ? 'justify-center' : 'justify-start'"
                            @click="$inertia.visit(route('reservatours'))"
                            tabindex="0"
                            @keydown.enter="$inertia.visit(route('reservatours'))"
                            title="Gestionar reservas">
                            <FontAwesomeIcon :icon="faClipboardList" :class="[isSidebarCollapsed ? '' : 'mr-3', 'drop-shadow-md']" class="h-6"/>
                            <span v-if="!isSidebarCollapsed">Reservaciones</span>
                        </li>
                        <li
                            class="px-5 py-3 hover:bg-red-600 flex items-center cursor-pointer"
                            :class="isSidebarCollapsed ? 'justify-center' : 'justify-start'"
                            @click="$inertia.visit(route('dashboard'))"
                            tabindex="0"
                            @keydown.enter="$inertia.visit(route('dashboard'))"
                            title="Modificar datos">
                            <FontAwesomeIcon :icon="faFileAlt" :class="[isSidebarCollapsed ? '' : 'mr-3', 'drop-shadow-md']" class="h-6"/>
                            <span v-if="!isSidebarCollapsed">Modificar</span>
                        </li>
                        <li
                            class="px-5 py-3 hover:bg-red-600 flex items-center cursor-pointer"
                            :class="isSidebarCollapsed ? 'justify-center' : 'justify-start'"
                            @click="$inertia.visit(route('dashboard'))"
                            tabindex="0"
                            @keydown.enter="$inertia.visit(route('dashboard'))"
                            title="Modificar datos">
                            <FontAwesomeIcon :icon="faFileAlt" :class="[isSidebarCollapsed ? '' : 'mr-3', 'drop-shadow-md']" class="h-6"/>
                            <span v-if="!isSidebarCollapsed">Modificar</span>
                        </li>
                        <li
                            class="px-5 py-3 hover:bg-red-600 flex items-center cursor-pointer"
                            :class="isSidebarCollapsed ? 'justify-center' : 'justify-start'"
                            @click="$inertia.visit(route('informes'))"
                            tabindex="0"
                            @keydown.enter="$inertia.visit(route('informes'))"
                            title="Generar informes">
                            <FontAwesomeIcon :icon="faFileInvoice" :class="[isSidebarCollapsed ? '' : 'mr-3', 'drop-shadow-md']" class="h-6"/>
                            <span v-if="!isSidebarCollapsed">Informes</span>
                        </li>
                    </ul>
                </nav>
                <!-- Configuración al pie del aside expandido -->
                <ul v-if="!isSidebarCollapsed" class="absolute bottom-0 left-0 w-full">
                    <li
                        class="px-5 py-3 hover:bg-red-600 flex items-center cursor-pointer"
                        :class="isSidebarCollapsed ? 'justify-center' : 'justify-start'"
                        @click="$inertia.visit(route('dashboard'))"
                        tabindex="0"
                        @keydown.enter="$inertia.visit(route('dashboard'))"
                        title="Modificar datos">
                        <FontAwesomeIcon :icon="faGear" :class="[isSidebarCollapsed ? '' : 'mr-3', 'drop-shadow-md']" class="h-6"/>
                        <span v-if="!isSidebarCollapsed">Configuración</span>
                    </li>
                </ul>
                <!-- Configuración al pie del aside colapsado -->
                <ul v-else class="absolute bottom-0 left-0 w-full flex flex-col items-center">
                    <li
                        class="w-full flex justify-center py-4 cursor-pointer hover:bg-red-600"
                        @click="$inertia.visit(route('dashboard'))"
                        tabindex="0"
                        @keydown.enter="$inertia.visit(route('dashboard'))"
                        title="Configuración"
                    >
                        <FontAwesomeIcon :icon="faGear" class="h-6 drop-shadow-md" />
                    </li>
                </ul>
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
                <header class="bg-gradient-to-r from-red-700 via-red-600 to-red-400 shadow px-4 py-2 flex items-center 
                    z-30 relative md:fixed top-16 md:top-16 lg:16 md:right-0 md:px-8
                    md:left-16">
                    <h2 class="text-lg font-semibold text-white">Dashboard</h2>
                </header>

                <!-- Contenido principal con padding para no quedar debajo de los headers -->
                <main class="flex-1 p-4 pt-16 md:p-10 md:pt-20 overflow-auto bg-white">
                    <slot /> <!-- Aquí se renderizará el contenido dinámico -->
                </main>

            </div>
        </div>
   </div>
</template>