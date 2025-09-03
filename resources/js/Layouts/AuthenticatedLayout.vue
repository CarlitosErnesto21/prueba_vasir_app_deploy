<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import { FontAwesomeIcon, } from "@fortawesome/vue-fontawesome";
import  { faRoute, faDoorOpen, faCircleXmark, faUserCircle, 
    faChevronCircleDown, faUser, faHotel, faPlaneDeparture, faGear, faBoxesStacked, 
    faClipboardList, faBox, faHouseChimneyUser, faBarsProgress,faListCheck,
    faFileInvoice, 
    faUserPen} from "@fortawesome/free-solid-svg-icons";   

import axios from "axios";
import { route } from "ziggy-js";

const page = usePage();
const user = page.props.auth?.user;
const isSidebarOpen = ref(false);
const isSidebarCollapsed = ref(true);
const isOpen = ref(false);
const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
};
const toggleSidebar = () => {
    isSidebarCollapsed.value = !isSidebarCollapsed.value;
    isOpen.value = false;
};

const logout = async () => {
    try {
        await axios.post("/logout");
        window.location.href = "/";
    } catch (err) {
        console.error("Error al cerrar la sesion", err);
    }
};

function handleResize() {
    if (window.innerWidth < 768) {
        isSidebarCollapsed.value = true;
        isSidebarOpen.value = false;
    } else {
        // En pantallas grandes, permitir que se mantenga el estado actual del sidebar
        isSidebarOpen.value = false; // Solo cerrar el overlay móvil
    }
}

function handleGlobalClick(e) {
    const aside = document.querySelector("aside");
    const hamburger = document.querySelector("button.block.md\\:hidden");
    if (
        (isSidebarOpen.value || !isSidebarCollapsed.value) &&
        aside &&
        !aside.contains(e.target) &&
        (!hamburger || !hamburger.contains(e.target))
    ) {
        isSidebarOpen.value = false;
        isSidebarCollapsed.value = true;
    }
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
    const menu = document.getElementById("profile-menu");
    if (showProfileMenu.value && menu && !menu.contains(e.target)) {
        showProfileMenu.value = false;
    }
}

const isConfigDropdownOpen = ref(false);
const configDropdownTrigger = ref(null);
const configDropdownMenu = ref(null);

function toggleConfigDropdown() {
    isConfigDropdownOpen.value = !isConfigDropdownOpen.value;
}
function closeConfigDropdown() {
    isConfigDropdownOpen.value = false;
}

watch(isSidebarCollapsed, (newVal) => {
    if (newVal) isConfigDropdownOpen.value = false;
});

function handleConfigDropdownClickOutside(e) {
    if (
        isConfigDropdownOpen.value &&
        configDropdownTrigger.value &&
        !configDropdownTrigger.value.contains(e.target) &&
        configDropdownMenu.value &&
        !configDropdownMenu.value.contains(e.target)
    ) {
        isConfigDropdownOpen.value = false;
    }
}

onMounted(() => {
    window.addEventListener("resize", handleResize);
    handleResize();
    document.addEventListener("click", handleGlobalClick, true);
    document.addEventListener("click", handleProfileMenuClick, true);
    document.addEventListener("click", handleConfigDropdownClickOutside, true);
});
onBeforeUnmount(() => {
    window.removeEventListener("resize", handleResize);
    document.removeEventListener("click", handleGlobalClick, true);
    document.removeEventListener("click", handleProfileMenuClick, true);
    document.removeEventListener(
        "click",
        handleConfigDropdownClickOutside,
        true
    );
});

const assignRolesRef = ref(null);
const systemSettingsRef = ref(null);
const manageClientesRef = ref(null);

function assignRoles() {
    isConfigDropdownOpen.value = false;
    isSidebarCollapsed.value = true;
    isSidebarOpen.value = false;
    if (assignRolesRef.value) {
        assignRolesRef.value.click();
    }
}
function systemSettings() {
    isConfigDropdownOpen.value = false;
    isSidebarCollapsed.value = true;
    isSidebarOpen.value = false;
    if (systemSettingsRef.value) {
        systemSettingsRef.value.click();
    }
}
function manageClientes() {
    isConfigDropdownOpen.value = false;
    isSidebarCollapsed.value = true;
    isSidebarOpen.value = false;
    if (manageClientesRef.value) {
        manageClientesRef.value.click();
    }
}
</script>

<template>
    <div class="h-screen flex flex-col">
        <header
            class="bg-gradient-to-r from-white to-white backdrop-blur-sm text-black shadow-md fixed top-0 left-0 w-full z-50"
        >
            <div class="px-6 py-3 flex justify-between items-center">
                <button
                    @click="isSidebarOpen = !isSidebarOpen"
                    class="block md:hidden mr-3 rounded-full p-2 bg-white/80 shadow-lg border border-red-200 hover:bg-red-600 hover:text-white transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-400 group"
                    aria-label="Abrir menú de navegación"
                >
                    <span class="sr-only">Abrir menú</span>
                    <span class="flex items-center justify-center w-6 h-6">
                        <FontAwesomeIcon
                            :icon="faBarsProgress"
                            class="w-6 h-6 text-red-700 group-hover:text-white transition-colors duration-200"
                        />
                    </span>
                </button>
                <div class="text-xl font-semibold text-black">
                    <Link
                        :href="route('dashboard')"
                        class="text-xl font-semibold text-black flex items-center cursor-pointer select-none"
                    >
                        <img
                            src="../../../imagenes/logo.png"
                            class="w-25 h-10 inline-block align-middle"
                        />
                    </Link>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative group">
                        <button
                            @click="openProfileMenu"
                            class="text-black text-xl"
                            title="Usuario"
                        >
                            <span
                                class="font-semibold text-base text-gray-700 mr-2"
                                >Perfil</span
                            >
                            <FontAwesomeIcon
                                :icon="faUser"
                                class="drop-shadow-md"
                            />
                        </button>
                        <transition name="fade">
                            <div
                                v-if="showProfileMenu"
                                id="profile-menu"
                                class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 z-50 overflow-hidden"
                            >
                                <!-- Header del perfil -->
                                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                                    <div class="flex items-center space-x-3">
                                        <img
                                            :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(
                                                user.name
                                            )}&background=1f2937&color=fff&size=64`"
                                            class="w-10 h-10 rounded-full border-2 border-gray-300"
                                            alt="avatar"
                                        />
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ user.name }}
                                            </p>
                                            <p class="text-xs text-gray-500 truncate">
                                                {{ user.email }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ user.role ? user.role.charAt(0).toUpperCase() + user.role.slice(1) : "Invitado" }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Opciones del menú -->
                                <div class="py-1">
                                    <Link
                                        :href="route('profile.edit')"
                                        class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors duration-150"
                                    >
                                        <FontAwesomeIcon
                                            :icon="faUserPen"
                                            class="mr-3 h-4 w-4 text-gray-400"
                                        />
                                        Editar Perfil
                                    </Link>
                                    
                                    <div class="border-t border-gray-100 my-1"></div>
                                    
                                    <button
                                        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors duration-150"
                                        @click="logout"
                                    >
                                        <FontAwesomeIcon
                                            :icon="faDoorOpen"
                                            class="mr-3 h-4 w-4 text-gray-400"
                                        />
                                        Cerrar Sesión
                                    </button>
                                </div>
                            </div>
                        </transition>
                    </div>
                </div>
            </div>
        </header>
        <div class="flex flex-1">
            <aside
                :class="[
                    isSidebarCollapsed ? 'w-16' : 'w-56',
                    'fixed top-16 left-0 h-[calc(100vh-64px)] bg-gradient-to-b from-red-700 via-red-600 to-red-400 text-white transition-all duration-300 ease-in-out shadow-lg z-40 pt-8 flex flex-col',
                    isSidebarOpen ? 'block' : 'hidden',
                    'md:flex',
                ]"
            >
                <button
                    @click="toggleSidebar"
                    class="mx-auto mb-6 p-2 rounded-full bg-white text-red-700 hover:bg-red-100 transition hidden md:flex items-center justify-center border border-red-200"
                    :title="
                        isSidebarCollapsed ? 'Expandir Menú' : 'Reducir Menú'
                    "
                >
                    <FontAwesomeIcon
                        :icon="faBarsProgress"
                        :class="[
                            isSidebarCollapsed ? '' : 'mr-0',
                            'drop-shadow-md',
                        ]"
                        class="h-5"
                    />
                </button>
                <div
                    class="px-4 text-xl font-bold text-white flex justify-center items-center"
                    v-if="!isSidebarCollapsed"
                >
                    <span>MENÚ</span>
                </div>
                <nav class="mt-4 text-white flex-1">
                    <ul>
                        <Link
                            :href="route('dashboard')"
                            class="px-4 py-3 hover:bg-red-600 flex items-center cursor-pointer w-full"
                            :class="
                                isSidebarCollapsed
                                    ? 'justify-center'
                                    : 'justify-start'
                            "
                            title="Inicio"
                        >
                            <FontAwesomeIcon
                                :icon="faHouseChimneyUser"
                                :class="[
                                    isSidebarCollapsed ? '' : 'mr-3',
                                    'drop-shadow-md',
                                ]"
                                class="h-6"
                            />
                            <span v-if="!isSidebarCollapsed">Inicio</span>
                        </Link>
                        <li class="px-0 py-3 flex flex-col relative">
                            <button
                                @click="toggleDropdown"
                                class="flex items-center px-4 py-2 hover:bg-red-600 focus:outline-none w-full"
                                :class="[
                                    isSidebarCollapsed
                                        ? 'justify-center w-auto mx-auto'
                                        : 'w-full justify-start',
                                    isOpen && isSidebarCollapsed
                                        ? 'bg-red-700 text-white'
                                        : '',
                                ]"
                                title="Catálogos"
                            >
                                <FontAwesomeIcon
                                    :icon="faBoxesStacked"
                                    :class="[
                                        isSidebarCollapsed ? '' : 'mr-3',
                                        'drop-shadow-md',
                                    ]"
                                    class="h-6"
                                />
                                <span v-if="!isSidebarCollapsed"
                                    >Catálogos</span
                                >
                                <FontAwesomeIcon
                                    v-if="!isSidebarCollapsed"
                                    :icon="faChevronCircleDown"
                                    class="ml-2 transition-transform h-5 drop-shadow-md"
                                    :class="{ 'rotate-90': isOpen }"
                                />
                            </button>
                            <transition name="fade">
                                <div
                                    v-if="isOpen && isSidebarCollapsed"
                                    class="absolute left-full top-1/2 -translate-y-1/2 flex flex-col items-center space-y-3 z-50"
                                    style="min-width: 120px"
                                >
                                    <Link
                                        :href="route('productos')"
                                        title="Categorías"
                                        class="bg-white text-red-700 rounded-full shadow-lg flex items-center justify-center hover:bg-red-600 hover:text-white transition"
                                        style="
                                            min-width: 110px;
                                            min-height: 48px;
                                            padding: 0 18px;
                                        "
                                    >
                                        <FontAwesomeIcon
                                            :icon="faBox"
                                            size="lg"
                                            class="drop-shadow-md"
                                        />
                                        <span class="ml-2 whitespace-nowrap"
                                            >Productos</span
                                        >
                                    </Link>
                                    <Link
                                        :href="route('tours')"
                                        title="Tours"
                                        class="bg-white text-red-700 rounded-full shadow-lg flex items-center justify-center hover:bg-red-600 hover:text-white transition"
                                        style="
                                            min-width: 110px;
                                            min-height: 48px;
                                            padding: 0 18px;
                                        "
                                    >
                                        <FontAwesomeIcon
                                            :icon="faRoute"
                                            size="lg"
                                            class="drop-shadow-md"
                                        />
                                        <span class="ml-2 whitespace-nowrap"
                                            >Tours</span
                                        >
                                    </Link>
                                    <Link
                                        :href="route('hoteles')"
                                        title="Hoteles"
                                        class="bg-white text-red-700 rounded-full shadow-lg flex items-center justify-center hover:bg-red-600 hover:text-white transition"
                                        style="
                                            min-width: 110px;
                                            min-height: 48px;
                                            padding: 0 18px;
                                        "
                                    >
                                        <FontAwesomeIcon
                                            :icon="faHotel"
                                            size="lg"
                                            class="drop-shadow-md"
                                        />
                                        <span class="ml-2 whitespace-nowrap"
                                            >Hoteles</span
                                        >
                                    </Link>
                                    <Link
                                        :href="route('aerolineas')"
                                        title="Aerolineas"
                                        class="bg-white text-red-700 rounded-full shadow-lg flex items-center justify-center hover:bg-red-600 hover:text-white transition"
                                        style="
                                            min-width: 110px;
                                            min-height: 48px;
                                            padding: 0 18px;
                                        "
                                    >
                                        <FontAwesomeIcon
                                            :icon="faPlaneDeparture"
                                            size="lg"
                                            class="drop-shadow-md"
                                        />
                                        <span class="ml-2 whitespace-nowrap"
                                            >Aerolineas</span
                                        >
                                    </Link>
                                </div>
                                    <ul
                                        v-else-if="isOpen"
                                        class="w-full rounded-md shadow-lg overflow-hidden bg-white text-red-700"
                                    >
                                        <li>
                                            <Link
                                                :href="route('productos')"
                                                class="flex items-center px-5 py-2 hover:bg-red-600 hover:text-white justify-start cursor-pointer w-full"
                                                title="Productos"
                                            >
                                                <FontAwesomeIcon
                                                    :icon="faBox"
                                                    class="drop-shadow-md"
                                                />
                                                <span class="ml-3">Productos</span>
                                            </Link>
                                        </li>
                                        <li>
                                            <Link
                                                :href="route('tours')"
                                                class="flex items-center px-5 py-2 hover:bg-red-600 hover:text-white justify-start cursor-pointer w-full"
                                                title="Tours"
                                            >
                                                <FontAwesomeIcon
                                                    :icon="faRoute"
                                                    class="drop-shadow-md"
                                                />
                                                <span class="ml-3">Tours</span>
                                            </Link>
                                        </li>
                                        <li>
                                            <Link
                                                :href="route('hoteles')"
                                                class="flex items-center px-5 py-2 hover:bg-red-600 hover:text-white justify-start cursor-pointer w-full"
                                                title="Hoteles"
                                            >
                                                <FontAwesomeIcon
                                                    :icon="faHotel"
                                                    class="drop-shadow-md"
                                                />
                                                <span class="ml-3">Hoteles</span>
                                            </Link>
                                        </li>
                                        <li>
                                            <Link
                                                :href="route('aerolineas')"
                                                class="flex items-center px-5 py-2 hover:bg-red-600 hover:text-white justify-start cursor-pointer w-full"
                                                title="Aerolineas"
                                            >
                                                <FontAwesomeIcon
                                                    :icon="faPlaneDeparture"
                                                    class="drop-shadow-md"
                                                />
                                                <span class="ml-3">Aerolineas</span>
                                            </Link>
                                        </li>
                                    </ul>
                            </transition>
                        </li>
                        <Link
                            :href="route('reservatours')"
                            class="px-5 py-3 hover:bg-red-600 flex items-center cursor-pointer w-full"
                            :class="
                                isSidebarCollapsed
                                    ? 'justify-center'
                                    : 'justify-start'
                            "
                            title="Gestionar reservas"
                        >
                            <FontAwesomeIcon
                                :icon="faClipboardList"
                                :class="[
                                    isSidebarCollapsed ? '' : 'mr-3',
                                    'drop-shadow-md',
                                ]"
                                class="h-6"
                            />
                            <span v-if="!isSidebarCollapsed"
                                >Reservaciones</span
                            >
                        </Link>
                        <Link
                            :href="route('informes')"
                            class="px-5 py-3 hover:bg-red-600 flex items-center cursor-pointer w-full"
                            :class="
                                isSidebarCollapsed
                                    ? 'justify-center'
                                    : 'justify-start'
                            "
                            title="Generar informes"
                        >
                            <FontAwesomeIcon
                                :icon="faFileInvoice"
                                :class="[
                                    isSidebarCollapsed ? '' : 'mr-3',
                                    'drop-shadow-md',
                                ]"
                                class="h-6"
                            />
                            <span v-if="!isSidebarCollapsed">Informes</span>
                        </Link>
                        <Link
                            :href="route('catPTH')"
                            class="px-5 py-3 hover:bg-red-600 flex items-center cursor-pointer w-full"
                            :class="
                                isSidebarCollapsed
                                    ? 'justify-center'
                                    : 'justify-start'
                            "
                            title="Control de categorias">
                            <FontAwesomeIcon :icon="faListCheck" :class="[isSidebarCollapsed ? '' : 'mr-3', 'drop-shadow-md']" class="h-6"/>
                            <span v-if="!isSidebarCollapsed">Gestión categorías</span>
                        </Link>
                    </ul>
                </nav>
                <ul
                    class="absolute bottom-0 left-0 w-full flex flex-col items-center"
                >
                    <li
                        class="w-full flex cursor-pointer hover:bg-red-600 relative"
                        :class="[
                            isSidebarCollapsed
                                ? 'justify-center py-4'
                                : 'justify-start px-5 py-3',
                        ]"
                        @click="toggleConfigDropdown"
                        tabindex="0"
                        @keydown.enter="toggleConfigDropdown"
                        title="Configuración"
                        ref="configDropdownTrigger"
                    >
                        <FontAwesomeIcon
                            :icon="faGear"
                            :class="[
                                isSidebarCollapsed ? '' : 'mr-3',
                                'drop-shadow-md',
                            ]"
                            class="h-6"
                        />
                        <span v-if="!isSidebarCollapsed">Configuración</span>

                        <transition name="fade">
                            <div
                                v-if="isConfigDropdownOpen"
                                class="absolute left-full top-0 bg-white rounded-lg shadow-xl border border-gray-200 z-50 overflow-hidden"
                                ref="configDropdownMenu"
                                style="
                                    min-width: 280px;
                                    transform: translateY(-100%);
                                "
                            >
                                <div class="py-2">
                                    <button
                                        v-if="user?.role === 'admin'"
                                        @click.stop="assignRoles"
                                        class="w-full flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 transition-colors duration-200 text-left"
                                    >
                                        <FontAwesomeIcon
                                            :icon="faUserCircle"
                                            class="text-red-600 mr-3 w-5 h-5"
                                        />
                                        <span class="text-sm font-medium"
                                            >Gestión de usuarios internos</span
                                        >
                                    </button>
                                    <button
                                        @click.stop="manageClientes"
                                        class="w-full flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 transition-colors duration-200 text-left"
                                    >
                                        <FontAwesomeIcon
                                            :icon="faUser"
                                            class="text-red-600 mr-3 w-5 h-5"
                                        />
                                        <span class="text-sm font-medium"
                                            >Gestión de clientes</span
                                        >
                                    </button>
                                    <button
                                        @click.stop="systemSettings"
                                        class="w-full flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 transition-colors duration-200 text-left"
                                    >
                                        <FontAwesomeIcon
                                            :icon="faGear"
                                            class="text-red-600 mr-3 w-5 h-5"
                                        />
                                        <span class="text-sm font-medium"
                                            >Configuración del sistema</span
                                        >
                                    </button>
                                </div>
                            </div>
                        </transition>
                    </li>
                </ul>
                
                <!-- Enlaces invisibles para el menú de configuración -->
                <Link ref="assignRolesRef" :href="route('roles')" class="hidden"></Link>
                <Link ref="systemSettingsRef" :href="route('settings')" class="hidden"></Link>
                <Link ref="manageClientesRef" :href="route('clientes')" class="hidden"></Link>
            </aside>
            <div
                v-if="isSidebarOpen"
                class="fixed inset-0 bg-black bg-opacity-40 z-30 md:hidden"
                @click="isSidebarOpen = false"
            ></div>
            <div class="md:ml-16 flex-1 flex flex-col">
                <header
                    class="bg-gradient-to-r from-red-700 via-red-600 to-red-400 shadow px-4 py-2 flex items-center z-30 relative md:fixed top-16 md:top-16 lg:16 md:right-0 md:px-8 md:left-16"
                >
                    <h2 class="text-lg font-semibold text-white">Dashboard</h2>
                </header>

                <main
                    class="flex-1 p-4 pt-16 md:p-10 md:pt-20 overflow-auto bg-white"
                >
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>

