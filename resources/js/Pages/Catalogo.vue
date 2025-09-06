<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faList, faUser, faDoorOpen, faShop, faPhone, faEnvelope, faMapMarkerAlt, faSignInAlt, faUserPlus, faChevronDown } from '@fortawesome/free-solid-svg-icons'
import { faFacebook, faInstagram, faTiktok, faWhatsapp } from '@fortawesome/free-brands-svg-icons'

const isSidebarOpen = ref(false)
const paquetesOpen = ref(false)
const paquetesOpenAside = ref(false)
const toursOpen = ref(false)
const toursOpenAside = ref(false)
const userMenuOpen = ref(false)

const togglePaquetes = () => paquetesOpen.value = !paquetesOpen.value
const closePaquetes = e => { if (!e.target.closest('.paquetes-dropdown')) paquetesOpen.value = false }
const toggleTours = () => toursOpen.value = !toursOpen.value
const closeTours = e => { if (!e.target.closest('.tours-dropdown')) toursOpen.value = false }
const toggleUserMenu = () => userMenuOpen.value = !userMenuOpen.value
const closeUserMenu = e => { if (!e.target.closest('.user-menu-dropdown')) userMenuOpen.value = false }

onMounted(() => {
    document.addEventListener('click', closePaquetes)
    document.addEventListener('click', closeTours)
    document.addEventListener('click', closeUserMenu)
})

onBeforeUnmount(() => {
    document.removeEventListener('click', closePaquetes)
    document.removeEventListener('click', closeTours)
    document.removeEventListener('click', closeUserMenu)
})
const logout = () => {
    // Limpiar cualquier información de reserva pendiente al cerrar sesión
    sessionStorage.removeItem('tour_reserva_pendiente')
    
    router.post(route('logout'), {}, {
        onSuccess: () => router.visit('/')
    })
}

// Computed properties para autenticación segura
const page = usePage()
const auth = computed(() => page.props.auth || {})
const user = computed(() => auth.value.user || null)
const isAuthenticated = computed(() => !!user.value)

// Redes sociales
const redes = [
  { href: "https://www.facebook.com/share/1C7tZxDHzh/", icon: faFacebook, label: "Facebook" },
  { href: "https://www.tiktok.com/@vasir_sv?_t=ZM-8wz8jwve57Y&_r=1", icon: faTiktok, label: "TikTok" },
  { href: "https://www.instagram.com/vasir_sv?igsh=MWx3aGFzdnB5Y2l2OA==", icon: faInstagram, label: "Instagram" },
  { href: "https://vasirsv.n1co.shop/?fbclid=PAZXh0bgNhZW0CMTEAAaeNe5ijdUbNvoQJ50Rf5CrJ66ixqACW8axuEShnlyN2_ofiouy166aAzWTzqw_aem_d5k2FX_C98iq0MGK-eaKAw", icon: faShop, label: "Tienda en Línea" }
]
</script>

<template>
    <!-- Header de redes sociales (solo una vez) -->
    <header class="bg-gradient-to-r from-red-700 via-red-600 to-red-400 text-white fixed top-0 left-0 w-full z-50 h-8 flex items-center">
        <div class="flex items-center h-full px-4 text-sm space-x-6 w-full">
            <div class="flex items-center space-x-2 ml-4">
                <template v-for="(r, i) in redes" :key="i">
                  <a :href="r.href" class="ml-2 hover:text-blue-300" target="_blank">
                    <FontAwesomeIcon :icon="r.icon" class="mr-1" /> {{ r.label }}
                  </a>
                </template>
            </div>
        </div>
    </header>

    <!-- Header principal -->
    <header class="bg-gradient-to-r from-white/60 to-white/60 backdrop-blur-sm text-black shadow-md fixed top-8 left-0 w-full z-50">
        <div class="px-6 py-3 flex justify-between items-center">
            <!-- Botón menú hamburguesa SOLO en móvil -->
            <button
                @click="isSidebarOpen = !isSidebarOpen"
                class="block md:hidden mr-3 rounded-full p-2 bg-white/80 shadow-lg border border-red-200 hover:bg-red-600 hover:text-white transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-400 group"
                aria-label="Abrir menú de navegación"
            >
                <span class="sr-only">Abrir menú</span>
                <span class="flex items-center justify-center w-6 h-6">
                    <FontAwesomeIcon :icon="faList" class="w-6 h-6 text-red-700 group-hover:text-white transition-colors duration-200" />
                </span>
            </button>
            <div class="flex items-center space-x-8">
                <Link :href="route('inicio')" class="flex items-center cursor-pointer select-none">
                    <img src="../../../imagenes/logo.png" class="w-25 h-10 inline-block align-middle" />
                </Link>
            </div>

            <!-- Menú de navegación con desplegable (solo desktop) -->
            <nav class="hidden md:flex items-center font-semibold space-x-2">
                <Link
                    :href="route('inicio')"
                    :class=" [
                        'px-5 py-2 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-200 tracking-wide hover:scale-105 transition-transform duration-300',
                        route().current('inicio')
                            ? 'bg-gradient-to-r from-red-600 to-red-400 text-white font-bold scale-105'
                            : 'hover:bg-gradient-to-r hover:from-red-600 hover:to-red-400 hover:text-white'
                    ]"
                >Inicio</Link>

                <!--Desplegable de Paquetes-->
                <div class="relative paquetes-dropdown" @click.stop="togglePaquetes">
                    <button
                        class="px-5 py-2 rounded-xl hover:scale-105 transition-transform duration-300 flex items-center focus:outline-none focus:ring-2 focus:ring-red-200 tracking-wide"
                        :class=" [
                            (paquetesOpen || route().current('paquetes') || route().current('reservaciones'))
                                ? 'bg-gradient-to-r from-red-600 to-red-400 text-white font-bold scale-105'
                                : 'hover:bg-gradient-to-r hover:from-red-600 hover:to-red-400 hover:text-white'
                        ]"
                        type="button">
                        Paquetes
                        <FontAwesomeIcon :icon="faChevronDown" class="ml-2 w-4 h-4" />
                    </button>
                    <div
                        class="absolute left-0 mt-2 w-56 bg-white/95 border border-red-100 rounded-2xl z-50 transition-all"
                        v-show="paquetesOpen"
                    >
                        <Link
                            :href="route('paquetes')"
                            :class=" [
                                'block px-6 py-3 text-black rounded-xl hover:scale-105 transition-transform duration-300',
                                route().current('paquetes')
                                    ? 'bg-gradient-to-r from-red-600 to-red-400 text-white font-bold scale-105'
                                    : 'hover:bg-gradient-to-r hover:from-red-600 hover:to-red-400 hover:text-white'
                            ]"
                        >Paquetes turísticos</Link>
                        <Link
                            :href="route('reservaciones')"
                            :class=" [
                                'block px-6 py-3 text-black rounded-xl hover:scale-105 transition-transform duration-300',
                                route().current('reservaciones')
                                    ? 'bg-gradient-to-r from-red-600 to-red-400 text-white font-bold scale-105'
                                    : 'hover:bg-gradient-to-r hover:from-red-600 hover:to-red-400 hover:text-white'
                            ]"
                        >Reservaciones</Link>
                    </div>
                </div>
                <!--Termina el desplegable de Paquetes-->

                <!--Desplegable de Tours-->
                <div class="relative tours-dropdown" @click.stop="toggleTours">
                    <button
                        class="px-5 py-2 rounded-xl hover:scale-105 transition-transform duration-300 flex items-center focus:outline-none focus:ring-2 focus:ring-red-200 tracking-wide"
                        :class=" [
                            (toursOpen || route().current('tours-nacionales') || route().current('tours-internacionales'))
                                ? 'bg-gradient-to-r from-red-600 to-red-400 text-white font-bold scale-105'
                                : 'hover:bg-gradient-to-r hover:from-red-600 hover:to-red-400 hover:text-white'
                        ]"
                        type="button">
                        Tours
                        <FontAwesomeIcon :icon="faChevronDown" class="ml-2 w-4 h-4" />
                    </button>
                    <div
                        class="absolute left-0 mt-2 w-56 bg-white/95 border border-red-100 rounded-2xl z-50 transition-all"
                        v-show="toursOpen">
                        <Link
                            :href="route('tours-nacionales')"
                            :class=" [
                                'block px-6 py-3 text-black rounded-xl hover:scale-105 transition-transform duration-300',
                                route().current('tours-nacionales')
                                    ? 'bg-gradient-to-r from-red-600 to-red-400 text-white font-bold scale-105'
                                    : 'hover:bg-gradient-to-r hover:from-red-600 hover:to-red-400 hover:text-white'
                            ]"
                        >Tours Nacionales</Link>
                        <Link
                            :href="route('tours-internacionales')"
                            :class=" [
                                'block px-6 py-3 text-black rounded-xl hover:scale-105 transition-transform duration-300',
                                route().current('tours-internacionales')
                                    ? 'bg-gradient-to-r from-red-600 to-red-400 text-white font-bold scale-105'
                                    : 'hover:bg-gradient-to-r hover:from-red-600 hover:to-red-400 hover:text-white'
                            ]"
                        >Tours Internacionales</Link>
                    </div>
                </div>
                <!--Termina el desplegable de Tours-->  

                <Link
                    :href="route('tienda')"
                    :class=" [
                        'px-5 py-2 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-200 tracking-wide hover:scale-105 transition-transform duration-300',
                        route().current('tienda')
                            ? 'bg-gradient-to-r from-red-600 to-red-400 text-white font-bold scale-105'
                            : 'hover:bg-gradient-to-r hover:from-red-600 hover:to-red-400 hover:text-white'
                    ]"
                >Tienda</Link>
                <Link
                    :href="route('sobre-nosotros')"
                    :class=" [
                        'px-5 py-2 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-200 tracking-wide hover:scale-105 transition-transform duration-300',
                        route().current('sobre-nosotros')
                            ? 'bg-gradient-to-r from-red-600 to-red-400 text-white font-bold scale-105'
                            : 'hover:bg-gradient-to-r hover:from-red-600 hover:to-red-400 hover:text-white'
                    ]"
                >Sobre Nosotros</Link>
                <Link
                    :href="route('contactos')"
                    :class=" [
                        'px-5 py-2 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-200 tracking-wide hover:scale-105 transition-transform duration-300',
                        route().current('contactos')
                            ? 'bg-gradient-to-r from-red-600 to-red-400 text-white font-bold scale-105'
                            : 'hover:bg-gradient-to-r hover:from-red-600 hover:to-red-400 hover:text-white'
                    ]"
                >Contactos</Link>
            </nav>
            
            <!--Datos de la sesion-->
            <div class="flex items-center space-x-2 md:space-x-4">
                <template v-if="!isAuthenticated">
                    <Link
                        :href="route('login')"
                        class="px-3 py-1.5 sm:px-4 sm:py-2 md:px-5 md:py-2.5 rounded-lg bg-gradient-to-r from-red-600 to-red-500 text-white font-medium shadow-md border border-red-700 hover:from-red-700 hover:to-red-600 hover:scale-105 hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-200 text-sm md:text-base"
                    >
                        <FontAwesomeIcon :icon="faSignInAlt" class="mr-1 md:mr-2" />
                        <span class="hidden sm:inline">Iniciar Sesión</span>
                        <span class="inline sm:hidden">Entrar</span>
                    </Link>
                    <Link
                        :href="route('register')"
                        class="px-3 py-1.5 sm:px-4 sm:py-2 md:px-5 md:py-2.5 rounded-lg text-red-700 font-medium shadow-md border border-red-700 bg-white hover:bg-gradient-to-r hover:from-red-600 hover:to-red-500 hover:text-white hover:scale-105 hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-200 text-sm md:text-base"
                    >
                        <FontAwesomeIcon :icon="faUserPlus" class="mr-1 md:mr-2" />
                        <span class="hidden sm:inline">Registrarse</span>
                        <span class="inline sm:hidden">Registro</span>
                    </Link>
                </template>
                <template v-else>
                    <div class="relative user-menu-dropdown">
                        <button @click="toggleUserMenu"
                            class="flex items-center space-x-2 px-3 py-2 rounded-full bg-white/90 hover:bg-red-50 border border-gray-300 hover:border-red-200 shadow-sm hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-200"
                            :title="user?.email"
                        >
                            <span class="w-8 h-8 rounded-full bg-gradient-to-r from-red-600 to-red-500 flex items-center justify-center text-white font-bold text-sm shadow-md">
                                {{ (user?.name || user?.email)?.charAt(0).toUpperCase() }}
                            </span>
                            <span class="text-sm text-gray-800 font-medium max-w-[100px] md:max-w-[120px] truncate hidden sm:block">
                                {{ user?.name || user?.email }}
                            </span>
                            <FontAwesomeIcon :icon="faChevronDown" class="w-4 h-4 text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': userMenuOpen }" />
                        </button>
                        
                        <div v-show="userMenuOpen"
                            class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-lg shadow-xl z-50 transition-all duration-200 transform origin-top-right"
                            :class="userMenuOpen ? 'opacity-100 scale-100' : 'opacity-0 scale-95'"
                        >
                            <!-- Header del usuario -->
                            <div class="px-4 py-3 border-b border-gray-100 bg-gray-50 rounded-t-lg">
                                <div class="flex items-center space-x-3">
                                    <span class="w-10 h-10 rounded-full bg-gradient-to-r from-red-600 to-red-500 flex items-center justify-center text-white font-bold text-lg shadow-md">
                                        {{ (user?.name || user?.email)?.charAt(0).toUpperCase() }}
                                    </span>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-semibold text-gray-900 truncate">{{ user?.name || 'Usuario' }}</div>
                                        <div class="text-xs text-gray-500 truncate">{{ user?.email }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Opciones del menú -->
                            <div class="py-2">
                                <Link 
                                    :href="route('profile.edit')"
                                    class="flex items-center gap-3 px-4 py-2.5 text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors duration-200"
                                    @click="userMenuOpen = false"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span class="font-medium">Mi Perfil</span>
                                </Link>

                                <div class="border-t border-gray-100 mt-2 pt-2">
                                    <button
                                        @click="logout"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-red-700 hover:bg-red-50 transition-colors duration-200 font-medium"
                                    >
                                        <FontAwesomeIcon :icon="faDoorOpen" class="w-5 h-5" />
                                        <span>Cerrar Sesión</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </header>

    <!-- Drawer lateral para móvil -->
    <transition
      enter-active-class="transition-opacity duration-200"
      leave-active-class="transition-opacity duration-200"
      enter-from-class="opacity-0"
      leave-to-class="opacity-0"
    >
      <div v-if="isSidebarOpen" class="fixed inset-0 z-[9998] bg-black/40 md:hidden" @click="isSidebarOpen = false"></div>
    </transition>
    <transition
      enter-active-class="transition-transform duration-200"
      leave-active-class="transition-transform duration-200"
      enter-from-class="-translate-x-full"
      leave-to-class="-translate-x-full"
    >
      <aside v-if="isSidebarOpen" class="fixed top-0 left-0 h-full w-64 bg-white shadow-lg z-[9999] flex flex-col md:hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-red-100">
          <Link :href="route('inicio')" class="flex items-center" @click="isSidebarOpen = false">
            <img src="../../../imagenes/logo.png" class="w-24 h-8" />
          </Link>
          <button @click="isSidebarOpen = false" class="text-red-700 text-2xl font-bold">&times;</button>
        </div>
        <nav class="flex flex-col space-y-1 px-4 py-4 font-semibold bg-white/80 backdrop-blur-md rounded-xl shadow border border-red-100 mx-2 mt-4">
          <Link
            :href="route('inicio')"
            :class=" [
              'py-2 px-3 rounded-lg transition-all duration-200',
              route().current('inicio') ? 'bg-gradient-to-r from-red-600 to-red-500 text-white shadow' : 'hover:bg-red-50 hover:text-red-700'
            ]"
            @click="isSidebarOpen = false"
          >Inicio</Link>

          <!--Desplegable de Paquetes-->
          <div>
            <button
              @click="paquetesOpenAside = !paquetesOpenAside"
              class="w-full flex items-center justify-between py-2 px-3 rounded-lg transition-all duration-200 focus:outline-none hover:bg-red-50"
              :class="{ 'bg-gradient-to-r from-red-600 to-red-500 text-white': paquetesOpenAside || route().current('paquetes') || route().current('reservaciones') }"
            >
              Paquetes
              <FontAwesomeIcon :icon="faChevronDown" class="ml-2 w-4 h-4" />
            </button>
            <div v-show="paquetesOpenAside" class="ml-2 mt-1 flex flex-col space-y-1">
              <Link
                :href="route('paquetes')"
                :class=" [
                  'py-2 px-3 rounded-lg transition-all duration-200',
                  route().current('paquetes') ? 'bg-gradient-to-r from-red-600 to-red-500 text-white shadow' : 'hover:bg-red-50 hover:text-red-700'
                ]"
                @click="isSidebarOpen = false"
              >Paquetes turísticos</Link>
              <Link
                :href="route('reservaciones')"
                :class=" [
                  'py-2 px-3 rounded-lg transition-all duration-200',
                  route().current('reservaciones') ? 'bg-gradient-to-r from-red-600 to-red-500 text-white shadow' : 'hover:bg-red-50 hover:text-red-700'
                ]"
                @click="isSidebarOpen = false"
              >Reservaciones</Link>
            </div>
          </div>
          <!--Termina el desplegable de Paquetes-->

          <!--Desplegable de Tours-->
          <div>
            <button
              @click="toursOpenAside = !toursOpenAside"
              class="w-full flex items-center justify-between py-2 px-3 rounded-lg transition-all duration-200 focus:outline-none hover:bg-red-50"
              :class="{ 'bg-gradient-to-r from-red-600 to-red-500 text-white': toursOpenAside || route().current('tours-nacionales') || route().current('tours-internacionales') }"
            >
              Tours
              <FontAwesomeIcon :icon="faChevronDown" class="ml-2 w-4 h-4" />
            </button>
            <div v-show="toursOpenAside" class="ml-2 mt-1 flex flex-col space-y-1">
              <Link
                :href="route('tours-nacionales')"
                :class=" [
                  'py-2 px-3 rounded-lg transition-all duration-200',
                  route().current('tours-nacionales') ? 'bg-gradient-to-r from-red-600 to-red-500 text-white shadow' : 'hover:bg-red-50 hover:text-red-700'
                ]"
                @click="isSidebarOpen = false"
              >Tours Nacionales</Link>
              <Link
                :href="route('tours-internacionales')"
                :class=" [
                  'py-2 px-3 rounded-lg transition-all duration-200',
                  route().current('tours-internacionales') ? 'bg-gradient-to-r from-red-600 to-red-500 text-white shadow' : 'hover:bg-red-50 hover:text-red-700'
                ]"
                @click="isSidebarOpen = false"
              >Tours Internacionales</Link>
            </div>
          </div>
          <!--Termina el desplegable de Tours-->

          <Link
            :href="route('tienda')"
            :class=" [
              'py-2 px-3 rounded-lg transition-all duration-200',
              route().current('tienda') ? 'bg-gradient-to-r from-red-600 to-red-500 text-white shadow' : 'hover:bg-red-50 hover:text-red-700'
            ]"
            @click="isSidebarOpen = false"
          >Tienda</Link>
          <Link
            :href="route('sobre-nosotros')"
            :class=" [
              'py-2 px-3 rounded-lg transition-all duration-200',
              route().current('sobre-nosotros') ? 'bg-gradient-to-r from-red-600 to-red-500 text-white shadow' : 'hover:bg-red-50 hover:text-red-700'
            ]"
            @click="isSidebarOpen = false"
          >Sobre Nosotros</Link>
          <Link
            :href="route('contactos')"
            :class=" [
              'py-2 px-3 rounded-lg transition-all duration-200',
              route().current('contactos') ? 'bg-gradient-to-r from-red-600 to-red-500 text-white shadow' : 'hover:bg-red-50 hover:text-red-700'
            ]"
            @click="isSidebarOpen = false"
          >Contactos</Link>
        </nav>
        <div class="mt-auto px-6 pb-6">
          <div class="border-t border-red-100 mb-4"></div>
          <template v-if="!isAuthenticated">
            <Link
                :href="route('login')"
                class="block w-full mb-2 py-1 px-2 sm:py-2 sm:px-2 rounded-lg bg-gradient-to-r from-red-600 to-red-500 text-white font-semibold shadow-md border border-red-700 hover:bg-white hover:text-white hover:border-red-700 hover:scale-105 hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-200 animate-fade-in text-sm sm:text-base text-left"
                @click="isSidebarOpen = false"
            >
                <FontAwesomeIcon :icon="faUser" class="mr-1 sm:mr-2" />
                <span class="hidden sm:inline">Iniciar Sesión</span>
                <span class="inline sm:hidden">Entrar</span>
            </Link>
            <Link
                :href="route('register')"
                class="block w-full py-1 px-2 sm:py-2 sm:px-2 rounded-lg bg-white text-red-700 font-semibold shadow-md border border-red-700 hover:bg-gradient-to-r from-red-600 to-red-500 hover:text-white hover:scale-105 hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-200 animate-fade-in text-sm sm:text-base text-left"
                @click="isSidebarOpen = false"
            >
                <FontAwesomeIcon :icon="faUserPlus" class="mr-1 sm:mr-2" />
                <span class="hidden sm:inline">Registrarse</span>
                <span class="inline sm:hidden">Registro</span>
            </Link>
          </template>
          <template v-else>
            <div class="flex items-center space-x-3 py-2 px-2 rounded bg-gray-50 mb-2 border border-gray-100">
              <span class="w-10 h-10 rounded-full bg-gradient-to-r from-red-600 to-red-500 flex items-center justify-center text-white font-bold text-xl shadow-md">
                {{ (user?.name || user?.email)?.charAt(0).toUpperCase() }}
              </span>
              <div class="flex flex-col min-w-0">
                <span class="text-md text-black font-medium truncate">{{ user?.name || 'Usuario' }}</span>
                <span class="text-xs text-gray-500 truncate">{{ user?.email }}</span>
              </div>
            </div>
            
            <!-- Opciones de usuario en móvil -->
            <Link
              :href="route('profile.edit')"
              class="w-full flex items-center gap-2 py-2 px-2 mb-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors duration-200"
              @click="isSidebarOpen = false"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
              <span class="font-medium">Mi Perfil</span>
            </Link>
            
            <button
              @click="logout(); isSidebarOpen = false"
              class="w-full flex items-center gap-2 py-2 px-2 rounded-lg bg-gradient-to-r from-red-600 to-red-500 text-white font-semibold shadow-md hover:scale-105 hover:from-red-700 hover:to-red-600 hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-200"
            >
              <span class="animate-pulse">
                <FontAwesomeIcon :icon="faDoorOpen" class="h-5" />
              </span>
              <span>Cerrar sesión</span>
            </button>
          </template>
        </div>
      </aside>
    </transition>

    <!-- AQUÍ VA EL CONTENIDO DINÁMICO -->
    <main class="mt-32">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-red-700 text-white mt-16">
      <div class="max-w-7xl mx-auto px-6 py-10 grid grid-cols-1 md:grid-cols-3 gap-8">
        <div>
          <h3 class="font-bold text-lg mb-2">Vasir</h3>
          <p class="text-sm">
            Experiencias auténticas en El Salvador y más allá.<br>
            Turismo cultural, sostenible y creativo para viajeros de todas las edades<br><br>
            <span class="font-bold">Ofrecemos:</span><br>
            - Paquetes vacacionales. <br>
            - Boletos aéreos y traslados. <br>
            - Estadías en hoteles.<br>
            - 🇺🇲Trámite de visas.<br><br>
            <span class="font-bold">!Viajá con propósito!!.</span><br>
          </p>
        </div>
        <div>
          <h3 class="font-bold text-lg mb-2">Contacto</h3>
          <ul class="text-sm space-y-1">
            <li>
              <FontAwesomeIcon :icon="faWhatsapp" class="mr-2 text-red-200" />
              <a
                href="tel:+50379858777"
                class="underline hover:text-blue-200">+503 7985 8777
              </a>
              <span>&nbsp;o al&nbsp;</span>
              <FontAwesomeIcon :icon="faPhone" class="mr-2 text-red-200" />
              <a
                href="tel:+50323279199"
                class="underline hover:text-blue-200">+503 2327 9199
              </a>
            </li>
            <li>
              <FontAwesomeIcon :icon="faEnvelope" class="mr-2 text-red-200" />
              <span class="font-semibold">Email:&nbsp;</span>
              <a
                href="https://mail.google.com/mail/?view=cm&fs=1&to=vasirtours19@gmail.com"
                class="underline hover:text-blue-200"
                target="_blank"
                rel="noopener">vasirtours19@gmail.com
              </a>
            </li>
            <li>
              <FontAwesomeIcon :icon="faMapMarkerAlt" class="mr-2 text-red-200" />
              <span class="font-semibold">Dirección:&nbsp;</span>
              <a
                href="https://maps.app.goo.gl/Se61cWuQWd39rNkSA"
                class="underline hover:text-blue-200"
                target="_blank"
                rel="noopener">2a Calle Oriente casa #12, Chalatenango, El Salvador
              </a>
            </li>
          </ul>
        </div>
        <div>
          <h3 class="font-bold text-lg mb-2">Síguenos</h3>
          <div class="flex space-x-4 mt-2">
            <template v-for="(r, i) in redes.slice(0, 3)" :key="i">
              <a :href="r.href" target="_blank" class="hover:text-blue-300 text-sm">
                <FontAwesomeIcon :icon="r.icon" class="mr-1" /> {{ r.label }}
              </a>
            </template>
          </div><br>
          <div>
            <h3 class="font-bold text-lg mb-2">Categoría</h3>
            <div class="flex space-x-4 mt-2">
              <h1 class="text-sm">Agencia de turismo</h1>
            </div>
          </div>
        </div>
      </div>
      <div class="bg-red-900 text-center text-xs py-3">
        &copy; {{ new Date().getFullYear() }} Vasir. Todos los derechos reservados.
      </div>
    </footer>
</template>