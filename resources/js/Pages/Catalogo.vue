<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { ref, onMounted, onBeforeUnmount, computed, watch } from 'vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faList, faUser, faDoorOpen, faPhone, faEnvelope, faMapMarkerAlt, faSignInAlt, faUserPlus, faChevronDown, faHome, faUsers, faShoppingCart, faBoxOpen, faGlobe, faCalendarCheck, faMap, faLocationDot, faGlobeAmericas, faStore } from '@fortawesome/free-solid-svg-icons'
import { faFacebook, faInstagram, faTiktok, faWhatsapp } from '@fortawesome/free-brands-svg-icons'
import { useCarritoStore } from '@/stores/carrito'

const carrito = useCarritoStore()
const page = usePage()

const isSidebarOpen = ref(false)
const paquetesOpen = ref(false)
const paquetesOpenAside = ref(false)
const toursOpen = ref(false)
const toursOpenAside = ref(false)
const userMenuOpen = ref(false)

// Funciones para bloquear/desbloquear scroll del body
const lockBodyScroll = () => {
  // Obtener la posición actual del scroll
  const scrollY = window.scrollY
  
  document.body.style.position = 'fixed'
  document.body.style.top = `-${scrollY}px`
  document.body.style.left = '0'
  document.body.style.right = '0'
  document.body.style.overflow = 'hidden'
  
  // Guardar la posición para restaurarla después
  document.body.setAttribute('data-scroll-y', scrollY.toString())
}

const unlockBodyScroll = () => {
  const scrollY = document.body.getAttribute('data-scroll-y')
  
  document.body.style.position = ''
  document.body.style.top = ''
  document.body.style.left = ''
  document.body.style.right = ''
  document.body.style.overflow = ''
  
  if (scrollY) {
    window.scrollTo(0, parseInt(scrollY))
    document.body.removeAttribute('data-scroll-y')
  }
}

// Watcher para manejar el scroll cuando se abre/cierra el sidebar
watch(isSidebarOpen, (newValue) => {
  if (newValue) {
    lockBodyScroll()
  } else {
    unlockBodyScroll()
  }
})

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
    // Asegurar que el scroll se desbloquee cuando se desmonte el componente
    unlockBodyScroll()
})
const logout = () => {
    // Limpiar carrito al cerrar sesión
    carrito.limpiarCarritoAlCerrarSesion()
    
    // Limpiar cualquier información de reserva pendiente al cerrar sesión
    sessionStorage.removeItem('tour_reserva_pendiente')
    sessionStorage.removeItem('reserva_session_activa')
    sessionStorage.removeItem('producto_compra_pendiente')
    sessionStorage.removeItem('compra_session_activa')
    
    router.post(route('logout'), {}, {
        onSuccess: () => router.visit('/')
    })
}

// Computed properties para autenticación segura
const auth = computed(() => page.props.auth || {})
const user = computed(() => auth.value.user || null)
const isAuthenticated = computed(() => !!user.value)

// Watcher para detectar cambios en el estado de autenticación
watch(isAuthenticated, (newValue, oldValue) => {
  // Si cambia de autenticado a no autenticado (logout)
  if (oldValue === true && newValue === false) {
    console.log('🚪 Logout detectado - Limpiando carrito')
    carrito.limpiarCarritoAlCerrarSesion()
  }
}, { immediate: false })
</script>

<template>
    <!-- Header principal -->
    <header class="bg-gradient-to-r from-white/98 via-blue-50/95 to-red-50/95 backdrop-blur-xl text-black shadow-2xl fixed top-0 left-0 w-full z-[9998] border-b-2 border-red-100/40 overflow-visible">
        <!-- Elementos decorativos de fondo -->
        <div class="absolute inset-0 bg-gradient-to-r from-blue-100/30 via-transparent to-red-100/30"></div>
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-red-500 via-blue-500 to-red-500 opacity-70"></div>
        
        <div class="relative px-4 sm:px-6 md:px-6 lg:px-8 py-3 sm:py-4 md:py-4 lg:py-5 flex justify-between items-center">
            <!-- Contenedor izquierdo: Menú + Logo -->
            <div class="flex items-center space-x-3 sm:space-x-4">
                <!-- Botón menú hamburguesa SOLO en móvil -->
                <button
                    @click="isSidebarOpen = !isSidebarOpen"
                    title="Abrir menú de navegación"
                    class="block md:hidden rounded-xl p-2.5 sm:p-3 bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-300 focus:ring-offset-2 group backdrop-blur-sm transform hover:scale-105"
                    aria-label="Abrir menú de navegación"
                >
                    <span class="sr-only">Abrir menú</span>
                    <FontAwesomeIcon :icon="faList" class="w-5 h-5 sm:w-6 sm:h-6 group-hover:scale-110 transition-transform duration-200" />
                </button>
                
                <!-- Logo con efecto -->
                <Link :href="route('inicio')" title="Ir a la página de inicio" class="flex items-center cursor-pointer select-none group">
                    <img src="../../../imagenes/logo.png" alt="Logo VASIR" class="w-20 h-7 sm:w-24 sm:h-8 md:w-28 md:h-10 lg:w-32 lg:h-11 xl:w-36 xl:h-12 2xl:w-40 2xl:h-14 inline-block align-middle group-hover:scale-105 transition-transform duration-300 drop-shadow-sm" />
                </Link>
            </div>

            <!-- Menú de navegación con desplegable (solo desktop) -->
            <nav class="hidden md:flex items-center space-x-1 lg:space-x-2 xl:space-x-3 bg-white/80 backdrop-blur-sm rounded-full px-2 md:px-3 lg:px-4 xl:px-6 py-1.5 md:py-2 lg:py-2.5 shadow-xl border border-red-100/50">
                <!-- Indicador decorativo -->
                <div class="hidden lg:block w-1 h-1 xl:w-1.5 xl:h-1.5 rounded-full bg-gradient-to-r from-red-500 to-blue-500 animate-pulse"></div>
                
                <Link
                    :href="route('inicio')"
                    :class=" [
                        'px-2 md:px-3 lg:px-4 xl:px-5 py-1.5 md:py-2 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-300/50 tracking-wide hover:scale-105 transition-all duration-300 font-medium text-xs md:text-sm lg:text-base xl:text-lg flex items-center group',
                        route().current('inicio')
                            ? 'bg-gradient-to-r from-red-600 to-red-400 text-white font-bold scale-105 shadow-lg'
                            : 'text-red-700 hover:bg-gradient-to-r hover:from-red-600 hover:to-red-400 hover:text-white hover:shadow-lg'
                    ]"
                >
                    <FontAwesomeIcon :icon="faHome" class="w-3 h-3 lg:w-4 lg:h-4 xl:w-5 xl:h-5 mr-1 md:mr-1.5 lg:mr-2 group-hover:scale-110 transition-transform duration-300" />
                    <span class="text-center">Inicio</span>
                </Link>

                <!-- Separador visual -->
                <div class="w-px h-6 bg-red-200/40"></div>

                <!--Desplegable de Paquetes-->
                <div class="relative paquetes-dropdown" @click.stop="togglePaquetes" style="z-index: 9999;">
                    <button
                        class="px-3 lg:px-4 py-2 rounded-xl hover:scale-105 transition-all duration-300 flex items-center focus:outline-none focus:ring-2 focus:ring-red-300/50 tracking-wide font-medium text-sm lg:text-base group"
                        :class=" [
                            (paquetesOpen || route().current('paquetes') || route().current('reservaciones'))
                                ? 'bg-gradient-to-r from-red-600 to-red-400 text-white font-bold scale-105 shadow-lg'
                                : 'text-red-700 hover:bg-gradient-to-r hover:from-red-600 hover:to-red-400 hover:text-white hover:shadow-lg'
                        ]"
                        type="button">
                        <FontAwesomeIcon :icon="faBoxOpen" class="w-3 h-3 lg:w-4 lg:h-4 mr-1.5 lg:mr-2 group-hover:scale-110 transition-transform duration-300" />
                        Paquetes
                        <FontAwesomeIcon
                            :icon="faChevronDown"
                            :class="['ml-1.5 lg:ml-2 w-2.5 h-2.5 lg:w-3 lg:h-3 transition-all duration-300 group-hover:scale-110', paquetesOpen ? 'rotate-180' : '']"
                        />
                    </button>
                    
                    <!-- Desplegable de paquetes con efecto glassmorphism -->
                    <div
                        class="absolute left-0 mt-2 w-64 bg-white/95 backdrop-blur-xl shadow-2xl border border-red-100/50 rounded-2xl z-[9999] transition-all overflow-hidden"
                        v-show="paquetesOpen"
                        style="z-index: 9999;"
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-red-50/30 to-blue-50/30"></div>
                        <div class="relative p-2">
                            <Link
                                :href="route('paquetes')"
                                :class=" [
                                    'flex items-center px-4 py-3 rounded-xl hover:scale-105 transition-all duration-300 font-medium group',
                                    route().current('paquetes')
                                        ? 'bg-gradient-to-r from-red-600 to-red-400 text-white font-bold scale-105 shadow-lg'
                                        : 'text-red-700 hover:bg-gradient-to-r hover:from-red-600 hover:to-red-400 hover:text-white hover:shadow-lg'
                                ]"
                            >
                                <FontAwesomeIcon :icon="faGlobe" class="w-4 h-4 mr-3 group-hover:scale-110 transition-transform duration-300" />
                                Paquetes turísticos
                            </Link>
                            <Link
                                :href="route('reservaciones')"
                                :class=" [
                                    'flex items-center px-4 py-3 rounded-xl hover:scale-105 transition-all duration-300 font-medium group',
                                    route().current('reservaciones')
                                        ? 'bg-gradient-to-r from-red-600 to-red-400 text-white font-bold scale-105 shadow-lg'
                                        : 'text-red-700 hover:bg-gradient-to-r hover:from-red-600 hover:to-red-400 hover:text-white hover:shadow-lg'
                                ]"
                            >
                                <FontAwesomeIcon :icon="faCalendarCheck" class="w-4 h-4 mr-3 group-hover:scale-110 transition-transform duration-300" />
                                Mis Reservaciones
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Separador visual -->
                <div class="w-px h-6 bg-red-200/40"></div>

                <!--Desplegable de Tours-->
                <div class="relative tours-dropdown" @click.stop="toggleTours" style="z-index: 9999;">
                    <button
                        class="px-3 lg:px-4 py-2 rounded-xl hover:scale-105 transition-all duration-300 flex items-center focus:outline-none focus:ring-2 focus:ring-red-300/50 tracking-wide font-medium text-sm lg:text-base group"
                        :class=" [
                            (toursOpen || route().current('tours-nacionales') || route().current('tours-internacionales'))
                                ? 'bg-gradient-to-r from-red-600 to-red-400 text-white font-bold scale-105 shadow-lg'
                                : 'text-red-700 hover:bg-gradient-to-r hover:from-red-600 hover:to-red-400 hover:text-white hover:shadow-lg'
                        ]"
                        type="button">
                        <FontAwesomeIcon :icon="faMap" class="w-3 h-3 lg:w-4 lg:h-4 mr-1.5 lg:mr-2 group-hover:scale-110 transition-transform duration-300" />
                        Tours
                        <FontAwesomeIcon
                            :icon="faChevronDown"
                            :class="['ml-1.5 lg:ml-2 w-2.5 h-2.5 lg:w-3 lg:h-3 transition-all duration-300 group-hover:scale-110', toursOpen ? 'rotate-180' : '']"
                        />
                    </button>
                    
                    <!-- Desplegable de tours con efecto glassmorphism -->
                    <div
                        class="absolute left-0 mt-2 w-64 bg-white/95 backdrop-blur-xl shadow-2xl border border-red-100/50 rounded-2xl z-[9999] transition-all overflow-hidden"
                        v-show="toursOpen"
                        style="z-index: 9999;"
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-red-50/30 to-blue-50/30"></div>
                        <div class="relative p-2">
                            <Link
                                :href="route('tours-nacionales')"
                                :class=" [
                                    'flex items-center px-4 py-3 rounded-xl hover:scale-105 transition-all duration-300 font-medium group',
                                    route().current('tours-nacionales')
                                        ? 'bg-gradient-to-r from-red-600 to-red-400 text-white font-bold scale-105 shadow-lg'
                                        : 'text-red-700 hover:bg-gradient-to-r hover:from-red-600 hover:to-red-400 hover:text-white hover:shadow-lg'
                                ]"
                            >
                                <FontAwesomeIcon :icon="faLocationDot" class="w-4 h-4 mr-3 group-hover:scale-110 transition-transform duration-300" />
                                Tours Nacionales
                            </Link>
                            <Link
                                :href="route('tours-internacionales')"
                                :class=" [
                                    'flex items-center px-4 py-3 rounded-xl hover:scale-105 transition-all duration-300 font-medium group',
                                    route().current('tours-internacionales')
                                        ? 'bg-gradient-to-r from-red-600 to-red-400 text-white font-bold scale-105 shadow-lg'
                                        : 'text-red-700 hover:bg-gradient-to-r hover:from-red-600 hover:to-red-400 hover:text-white hover:shadow-lg'
                                ]"
                            >
                                <FontAwesomeIcon :icon="faGlobeAmericas" class="w-4 h-4 mr-3 group-hover:scale-110 transition-transform duration-300" />
                                Tours Internacionales
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Separador visual -->
                <div class="w-px h-6 bg-red-200/40"></div>

                <Link
                    :href="route('tienda')"
                    :class=" [
                        'px-3 lg:px-4 py-2 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-300/50 tracking-wide hover:scale-105 transition-all duration-300 font-medium text-sm lg:text-base flex items-center justify-center group',
                        route().current('tienda')
                            ? 'bg-gradient-to-r from-red-600 to-red-400 text-white font-bold scale-105 shadow-lg'
                            : 'text-red-700 hover:bg-gradient-to-r hover:from-red-600 hover:to-red-400 hover:text-white hover:shadow-lg'
                    ]"
                >
                    <FontAwesomeIcon :icon="faStore" class="w-3 h-3 lg:w-4 lg:h-4 mr-1.5 group-hover:scale-110 transition-transform duration-300" />
                    <span class="text-center">Tienda</span>
                </Link>
                
                <!-- Separador visual -->
                <div class="w-px h-6 bg-red-200/40"></div>
                
                <Link
                    :href="route('sobre-nosotros')"
                    :class=" [
                        'px-3 lg:px-4 py-2 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-300/50 tracking-wide hover:scale-105 transition-all duration-300 font-medium text-sm lg:text-base flex items-center justify-center group',
                        route().current('sobre-nosotros')
                            ? 'bg-gradient-to-r from-red-600 to-red-400 text-white font-bold scale-105 shadow-lg'
                            : 'text-red-700 hover:bg-gradient-to-r hover:from-red-600 hover:to-red-400 hover:text-white hover:shadow-lg'
                    ]"
                >
                    <FontAwesomeIcon :icon="faUsers" class="w-3 h-3 lg:w-4 lg:h-4 mr-1.5 group-hover:scale-110 transition-transform duration-300" />
                    <span class="text-center">Sobre Nosotros</span>
                </Link>
                
                <!-- Separador visual -->
                <div class="w-px h-6 bg-red-200/40"></div>
                
                <Link
                    :href="route('contactos')"
                    :class=" [
                        'px-3 lg:px-4 py-2 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-300/50 tracking-wide hover:scale-105 transition-all duration-300 font-medium text-sm lg:text-base flex items-center justify-center group',
                        route().current('contactos')
                            ? 'bg-gradient-to-r from-red-600 to-red-400 text-white font-bold scale-105 shadow-lg'
                            : 'text-red-700 hover:bg-gradient-to-r hover:from-red-600 hover:to-red-400 hover:text-white hover:shadow-lg'
                    ]"
                >
                    <FontAwesomeIcon :icon="faEnvelope" class="w-3 h-3 lg:w-4 lg:h-4 mr-1.5 group-hover:scale-110 transition-transform duration-300" />
                    <span class="text-center">Contactos</span>
                </Link>
                
                <!-- Indicador decorativo final -->
                <div class="hidden lg:block w-1 h-1 rounded-full bg-gradient-to-r from-blue-500 to-red-500 animate-pulse"></div>
            </nav>
            
            <!-- Contenedor derecho: Auth -->
            <div class="flex items-center space-x-2 sm:space-x-3 md:space-x-4">
                <template v-if="!isAuthenticated">
                    <Link
                        :href="route('login')"
                        title="Iniciar sesión en tu cuenta"
                        class="flex items-center justify-center px-3 py-2 sm:px-4 sm:py-2.5 md:px-5 md:py-3 rounded-xl bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-semibold shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-300 focus:ring-offset-2 text-sm md:text-base transform hover:scale-105"
                    >
                        <FontAwesomeIcon :icon="faSignInAlt" class="w-4 h-4 sm:w-5 sm:h-5" />
                        <span class="hidden xs:inline ml-1.5 sm:ml-2">Entrar</span>
                    </Link>
                    <Link
                        :href="route('register')"
                        title="Crear una nueva cuenta"
                        class="flex items-center justify-center px-3 py-2 sm:px-4 sm:py-2.5 md:px-5 md:py-3 rounded-xl text-red-700 hover:text-white font-semibold shadow-lg hover:shadow-xl border-2 border-red-600 bg-white/90 hover:bg-gradient-to-r hover:from-red-600 hover:to-red-500 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-300 focus:ring-offset-2 text-sm md:text-base transform hover:scale-105"
                    >
                        <FontAwesomeIcon :icon="faUserPlus" class="w-4 h-4 sm:w-5 sm:h-5" />
                        <span class="hidden xs:inline ml-1.5 sm:ml-2">Registro</span>
                    </Link>
                </template>
                <template v-else>
                    <div class="relative user-menu-dropdown">
                        <button @click="toggleUserMenu"
                            class="flex items-center space-x-2 sm:space-x-3 px-3 py-2 sm:px-4 sm:py-2.5 rounded-xl bg-white/95 hover:bg-red-50 border-2 border-red-200 hover:border-red-300 shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-300 focus:ring-offset-2 transform hover:scale-105"
                            :title="user?.email"
                        >
                            <span class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-gradient-to-r from-red-600 to-red-500 flex items-center justify-center text-white font-bold text-sm sm:text-base shadow-lg">
                                {{ (user?.name || user?.email)?.charAt(0).toUpperCase() }}
                            </span>
                            <span class="text-sm sm:text-base text-gray-800 font-semibold max-w-[80px] sm:max-w-[100px] md:max-w-[120px] truncate hidden xs:block">
                                {{ user?.name || user?.email }}
                            </span>
                            <FontAwesomeIcon :icon="faChevronDown" class="w-4 h-4 sm:w-5 sm:h-5 text-gray-600 transition-transform duration-200" :class="{ 'rotate-180': userMenuOpen }" />
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
      <!-- Overlay con glassmorphism mejorado -->
      <div 
        v-if="isSidebarOpen" 
        class="fixed inset-0 z-[9998] bg-gradient-to-br from-black/50 via-red-900/20 to-blue-900/30 backdrop-blur-sm md:hidden" 
        @click="isSidebarOpen = false"
        @touchmove.prevent
        @scroll.prevent
      ></div>
    </transition>
    <transition
      enter-active-class="transition-all duration-300"
      leave-active-class="transition-all duration-300"
      enter-from-class="-translate-x-full opacity-0"
      leave-to-class="-translate-x-full opacity-0"
    >
      <aside 
        v-if="isSidebarOpen" 
        class="fixed top-0 left-0 h-full w-80 bg-gradient-to-b from-white/95 via-blue-50/90 to-red-50/95 backdrop-blur-xl shadow-2xl z-[9999] flex flex-col md:hidden border-r border-white/30 overflow-hidden"
        @touchmove.stop
      >
        <!-- Elementos decorativos de fondo -->
        <div class="absolute inset-0 bg-gradient-to-br from-red-100/20 via-transparent to-blue-100/20"></div>
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-red-400 via-blue-400 to-red-400"></div>
        
        <!-- Header del sidebar -->
        <div class="relative flex items-center justify-between px-4 sm:px-6 py-4 sm:py-6 border-b border-red-100/50 bg-white/80 backdrop-blur-sm">
          <Link :href="route('inicio')" class="flex items-center group" @click="isSidebarOpen = false">
            <img src="../../../imagenes/logo.png" class="w-20 h-7 sm:w-24 sm:h-8 group-hover:scale-105 transition-transform duration-300" />
          </Link>
          <button 
            @click="isSidebarOpen = false" 
            class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-red-600 text-white hover:bg-red-700 hover:scale-110 transition-all duration-200 shadow-lg focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2"
            aria-label="Cerrar menú"
          >
            <span class="text-lg sm:text-xl font-bold">&times;</span>
          </button>
        </div>
        <!-- Navegación principal con glassmorphism -->
        <nav class="relative flex flex-col space-y-2 px-3 py-4 sm:py-6 font-medium bg-white/60 backdrop-blur-lg rounded-xl shadow-xl border border-white/50 mx-2 mt-4 overflow-x-hidden overflow-y-auto flex-1 min-h-0">
          <!-- Área scrollable -->
          <div class="flex-1 overflow-y-auto overflow-x-hidden overscroll-contain scroll-smooth">
            <!-- Elementos decorativos internos -->
            <div class="absolute inset-0 bg-gradient-to-br from-white/30 via-transparent to-blue-50/30 pointer-events-none"></div>
            <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-red-300/40 to-transparent pointer-events-none"></div>
          
          <!-- Inicio -->
          <Link
            :href="route('inicio')"
            :class=" [
              'relative flex items-center py-3 px-3 rounded-xl transition-all duration-300 group',
              route().current('inicio') 
                ? 'bg-gradient-to-r from-red-600 to-red-400 text-white font-bold shadow-lg' 
                : 'text-red-700 hover:bg-gradient-to-r hover:from-red-600 hover:to-red-400 hover:text-white hover:shadow-lg'
            ]"
            @click="isSidebarOpen = false"
          >
            <FontAwesomeIcon :icon="faHome" class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-300" />
            <span class="font-semibold">Inicio</span>
          </Link>

          <!-- Separador visual -->
          <div class="relative w-full h-px bg-gradient-to-r from-transparent via-red-200/50 to-transparent my-1"></div>

          <!-- Desplegable de Paquetes -->
          <div class="relative">
            <button
              @click="paquetesOpenAside = !paquetesOpenAside"
              class="relative w-full flex items-center justify-between py-3 px-3 rounded-xl transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-red-300/50 group"
              :class="{ 
                'bg-gradient-to-r from-red-600 to-red-400 text-white shadow-lg font-bold': paquetesOpenAside || route().current('paquetes') || route().current('reservaciones'),
                'text-red-700 hover:bg-gradient-to-r hover:from-red-600 hover:to-red-400 hover:text-white hover:shadow-lg': !(paquetesOpenAside || route().current('paquetes') || route().current('reservaciones'))
              }"
            >
              <div class="flex items-center">
                <FontAwesomeIcon :icon="faBoxOpen" class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-300" />
                <span class="font-semibold">Paquetes</span>
              </div>
              <FontAwesomeIcon 
                :icon="faChevronDown" 
                :class="['w-4 h-4 transition-all duration-300 group-hover:scale-110', paquetesOpenAside ? 'rotate-180' : '']" 
              />
            </button>
            
            <!-- Submenu de Paquetes -->
            <div v-show="paquetesOpenAside" class="ml-6 mr-2 mt-2 flex flex-col space-y-1 bg-white/40 backdrop-blur-sm rounded-lg p-2 border border-white/30">
              <Link
                :href="route('paquetes')"
                :class=" [
                  'flex items-center py-2.5 px-3 rounded-lg transition-all duration-300 group',
                  route().current('paquetes') 
                    ? 'bg-gradient-to-r from-red-600 to-red-400 text-white shadow-lg font-bold' 
                    : 'text-red-600 hover:bg-red-600 hover:text-white hover:shadow-md'
                ]"
                @click="isSidebarOpen = false"
              >
                <FontAwesomeIcon :icon="faGlobe" class="w-4 h-4 mr-3 group-hover:scale-110 transition-transform duration-300" />
                <span class="font-medium">Paquetes turísticos</span>
              </Link>
              <Link
                :href="route('reservaciones')"
                :class=" [
                  'flex items-center py-2.5 px-3 rounded-lg transition-all duration-300 group',
                  route().current('reservaciones') 
                    ? 'bg-gradient-to-r from-red-600 to-red-400 text-white shadow-lg font-bold' 
                    : 'text-red-600 hover:bg-red-600 hover:text-white hover:shadow-md'
                ]"
                @click="isSidebarOpen = false"
              >
                <FontAwesomeIcon :icon="faCalendarCheck" class="w-4 h-4 mr-3 group-hover:scale-110 transition-transform duration-300" />
                <span class="font-medium">Reservaciones</span>
              </Link>
            </div>
          </div>

          <!-- Separador visual -->
          <div class="relative w-full h-px bg-gradient-to-r from-transparent via-red-200/50 to-transparent my-1"></div>

          <!-- Desplegable de Tours -->
          <div class="relative">
            <button
              @click="toursOpenAside = !toursOpenAside"
              class="relative w-full flex items-center justify-between py-3 px-3 rounded-xl transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-red-300/50 group"
              :class="{ 
                'bg-gradient-to-r from-red-600 to-red-400 text-white shadow-lg font-bold': toursOpenAside || route().current('tours-nacionales') || route().current('tours-internacionales'),
                'text-red-700 hover:bg-gradient-to-r hover:from-red-600 hover:to-red-400 hover:text-white hover:shadow-lg': !(toursOpenAside || route().current('tours-nacionales') || route().current('tours-internacionales'))
              }"
            >
              <div class="flex items-center">
                <FontAwesomeIcon :icon="faMap" class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-300" />
                <span class="font-semibold">Tours</span>
              </div>
              <FontAwesomeIcon 
                :icon="faChevronDown" 
                :class="['w-4 h-4 transition-all duration-300 group-hover:scale-110', toursOpenAside ? 'rotate-180' : '']" 
              />
            </button>
            
            <!-- Submenu de Tours -->
            <div v-show="toursOpenAside" class="ml-6 mr-2 mt-2 flex flex-col space-y-1 bg-white/40 backdrop-blur-sm rounded-lg p-2 border border-white/30">
              <Link
                :href="route('tours-nacionales')"
                :class=" [
                  'flex items-center py-2.5 px-3 rounded-lg transition-all duration-300 group',
                  route().current('tours-nacionales') 
                    ? 'bg-gradient-to-r from-red-600 to-red-400 text-white shadow-lg font-bold' 
                    : 'text-red-600 hover:bg-red-600 hover:text-white hover:shadow-md'
                ]"
                @click="isSidebarOpen = false"
              >
                <FontAwesomeIcon :icon="faLocationDot" class="w-4 h-4 mr-3 group-hover:scale-110 transition-transform duration-300" />
                <span class="font-medium">Tours Nacionales</span>
              </Link>
              <Link
                :href="route('tours-internacionales')"
                :class=" [
                  'flex items-center py-2.5 px-3 rounded-lg transition-all duration-300 group',
                  route().current('tours-internacionales') 
                    ? 'bg-gradient-to-r from-red-600 to-red-400 text-white shadow-lg font-bold' 
                    : 'text-red-600 hover:bg-red-600 hover:text-white hover:shadow-md'
                ]"
                @click="isSidebarOpen = false"
              >
                <FontAwesomeIcon :icon="faGlobeAmericas" class="w-4 h-4 mr-3 group-hover:scale-110 transition-transform duration-300" />
                <span class="font-medium">Tours Internacionales</span>
              </Link>
            </div>
          </div>

          <!-- Separador visual -->
          <div class="relative w-full h-px bg-gradient-to-r from-transparent via-red-200/50 to-transparent my-1"></div>

          <!-- Tienda -->
          <Link
            :href="route('tienda')"
            :class=" [
              'relative flex items-center py-3 px-3 rounded-xl transition-all duration-300 group',
              route().current('tienda') 
                ? 'bg-gradient-to-r from-red-600 to-red-400 text-white font-bold shadow-lg' 
                : 'text-red-700 hover:bg-gradient-to-r hover:from-red-600 hover:to-red-400 hover:text-white hover:shadow-lg'
            ]"
            @click="isSidebarOpen = false"
          >
            <FontAwesomeIcon :icon="faStore" class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-300" />
            <span class="font-semibold">Tienda</span>
          </Link>
          
          <!-- Separador visual -->
          <div class="relative w-full h-px bg-gradient-to-r from-transparent via-red-200/50 to-transparent my-1"></div>
          
          <!-- Sobre Nosotros -->
          <Link
            :href="route('sobre-nosotros')"
            :class=" [
              'relative flex items-center py-3 px-3 rounded-xl transition-all duration-300 group',
              route().current('sobre-nosotros') 
                ? 'bg-gradient-to-r from-red-600 to-red-400 text-white font-bold shadow-lg' 
                : 'text-red-700 hover:bg-gradient-to-r hover:from-red-600 hover:to-red-400 hover:text-white hover:shadow-lg'
            ]"
            @click="isSidebarOpen = false"
          >
            <FontAwesomeIcon :icon="faUsers" class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-300" />
            <span class="font-semibold">Sobre Nosotros</span>
          </Link>
          
          <!-- Separador visual -->
          <div class="relative w-full h-px bg-gradient-to-r from-transparent via-red-200/50 to-transparent my-1"></div>
          
          <!-- Contactos -->
          <Link
            :href="route('contactos')"
            :class=" [
              'relative flex items-center py-3 px-3 rounded-xl transition-all duration-300 group',
              route().current('contactos') 
                ? 'bg-gradient-to-r from-red-600 to-red-400 text-white font-bold shadow-lg' 
                : 'text-red-700 hover:bg-gradient-to-r hover:from-red-600 hover:to-red-400 hover:text-white hover:shadow-lg'
            ]"
            @click="isSidebarOpen = false"
          >
            <FontAwesomeIcon :icon="faEnvelope" class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-300" />
            <span class="font-semibold">Contactos</span>
          </Link>
          
          <!-- Indicador decorativo final -->
          <div class="flex justify-center mt-4">
            <div class="w-16 h-1 rounded-full bg-gradient-to-r from-red-400 via-blue-400 to-red-400"></div>
          </div>
          </div> <!-- Fin del área scrollable -->
        </nav>
        <!-- Sección de usuario con glassmorphism -->
        <div class="relative mt-auto px-3 sm:px-4 pb-4 sm:pb-6">
          <div class="relative bg-white/60 backdrop-blur-lg rounded-xl shadow-xl border border-white/50 p-4 overflow-hidden">
            <!-- Elementos decorativos -->
            <div class="absolute inset-0 bg-gradient-to-br from-white/30 via-transparent to-red-50/30"></div>
            <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-red-300/40 to-transparent"></div>
            
            <div class="relative">
              <template v-if="!isAuthenticated">
                <!-- Botones de autenticación mejorados -->
                <Link
                    :href="route('login')"
                    class="relative flex items-center justify-center w-full mb-3 py-3 px-3 rounded-xl bg-gradient-to-r from-red-600 to-red-400 text-white font-bold shadow-lg hover:shadow-xl transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-red-300 focus:ring-offset-2 group"
                    @click="isSidebarOpen = false"
                >
                    <FontAwesomeIcon :icon="faSignInAlt" class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-300" />
                    <span class="font-semibold">Iniciar Sesión</span>
                </Link>
                <Link
                    :href="route('register')"
                    class="relative flex items-center justify-center w-full py-3 px-3 rounded-xl bg-white/90 text-red-700 font-bold shadow-lg border-2 border-red-200 hover:bg-gradient-to-r hover:from-red-600 hover:to-red-400 hover:text-white hover:border-transparent hover:shadow-xl transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-red-300 focus:ring-offset-2 group"
                    @click="isSidebarOpen = false"
                >
                    <FontAwesomeIcon :icon="faUserPlus" class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-300" />
                    <span class="font-semibold">Registrarse</span>
                </Link>
              </template>
              <template v-else>
                <!-- Información del usuario -->
                <div class="flex items-center space-x-3 py-3 px-3 rounded-xl bg-white/70 backdrop-blur-sm border border-white/50 mb-3 shadow-md">
                  <span class="w-12 h-12 rounded-full bg-gradient-to-r from-red-600 to-red-400 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                    {{ (user?.name || user?.email)?.charAt(0).toUpperCase() }}
                  </span>
                  <div class="flex flex-col min-w-0 flex-1">
                    <span class="text-sm font-bold text-gray-800 truncate">{{ user?.name || 'Usuario' }}</span>
                    <span class="text-xs text-gray-600 truncate">{{ user?.email }}</span>
                  </div>
                </div>
                
                <!-- Opciones de usuario -->
                <Link
                  :href="route('profile.edit')"
                  class="relative flex items-center w-full py-3 px-4 mb-3 rounded-xl bg-white/70 text-gray-700 font-semibold shadow-md hover:bg-gradient-to-r hover:from-blue-600 hover:to-blue-400 hover:text-white hover:scale-105 hover:shadow-lg transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2 group"
                  @click="isSidebarOpen = false"
                >
                  <FontAwesomeIcon :icon="faUser" class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-300" />
                  <span>Mi Perfil</span>
                </Link>
                
                <button
                  @click="logout(); isSidebarOpen = false"
                  class="relative flex items-center justify-center w-full py-3 px-3 rounded-xl bg-gradient-to-r from-red-600 to-red-400 text-white font-bold shadow-lg hover:from-red-700 hover:to-red-500 hover:shadow-xl transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-red-300 focus:ring-offset-2 group"
                >
                  <FontAwesomeIcon :icon="faDoorOpen" class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-300" />
                  <span>Cerrar sesión</span>
                </button>
              </template>
            </div>
          </div>
        </div>
      </aside>
    </transition>

    <!-- AQUÍ VA EL CONTENIDO DINÁMICO -->
    <main class="mt-20 sm:mt-24 md:mt-28 bg-gradient-to-br from-gray-50 via-blue-50/30 to-red-50/30 min-h-screen">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-gradient-to-br from-red-700 via-red-600 to-red-800 text-white mt-16 relative overflow-hidden">
      <!-- Elementos decorativos de fondo -->
      <div class="absolute inset-0 bg-gradient-to-r from-red-900/20 via-transparent to-red-900/20"></div>
      <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-red-400 via-red-300 to-red-400"></div>
      
      <div class="relative max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="space-y-4">
          <div class="flex items-center space-x-3 mb-4">
            <div class="w-10 h-10 bg-gradient-to-br from-white to-red-100 rounded-full flex items-center justify-center shadow-lg">
              <span class="text-red-700 font-bold text-lg">V</span>
            </div>
            <h3 class="font-bold text-2xl bg-gradient-to-r from-white to-red-100 bg-clip-text text-transparent">Vasir</h3>
          </div>
          <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
            <p class="text-sm leading-relaxed text-red-50">
              Experiencias auténticas en El Salvador y más allá.<br>
              Turismo cultural, sostenible y creativo para viajeros de todas las edades<br><br>
              <span class="font-bold text-white">Ofrecemos:</span><br>
              <span class="text-red-100">
                - Paquetes vacacionales<br>
                - Boletos aéreos y traslados<br>
                - Estadías en hoteles<br>
                - 🇺� Trámite de visas
              </span><br><br>
              <span class="font-bold text-yellow-300 text-lg">¡Viajá con propósito!</span>
            </p>
          </div>
        </div>
        
        <div class="space-y-4">
          <div class="flex items-center space-x-3 mb-4">
            <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center shadow-lg">
              <FontAwesomeIcon :icon="faPhone" class="w-4 h-4 text-white" />
            </div>
            <h3 class="font-bold text-xl text-white">Contacto</h3>
          </div>
          <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20 space-y-3">
            <div class="flex items-start space-x-3 p-2 rounded-lg hover:bg-white/10 transition-all duration-200">
              <FontAwesomeIcon :icon="faWhatsapp" class="w-5 h-5 text-green-300 mt-1 flex-shrink-0" />
              <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                  <a href="tel:+50379858777" class="text-red-100 hover:text-white underline decoration-dotted transition-colors">
                    +503 7985 8777
                  </a>
                  <span class="text-red-200">o al</span>
                  <a href="tel:+50323279199" class="text-red-100 hover:text-white underline decoration-dotted transition-colors">
                    +503 2327 9199
                  </a>
                </div>
              </div>
            </div>
            
            <div class="flex items-start space-x-3 p-2 rounded-lg hover:bg-white/10 transition-all duration-200">
              <FontAwesomeIcon :icon="faEnvelope" class="w-5 h-5 text-blue-300 mt-1 flex-shrink-0" />
              <div class="flex-1 min-w-0">
                <span class="text-red-200 text-sm">Email:</span>
                <a href="https://mail.google.com/mail/?view=cm&fs=1&to=vasirtours19@gmail.com"
                   class="block text-red-100 hover:text-white underline decoration-dotted transition-colors break-words"
                   target="_blank" rel="noopener">
                  vasirtours19@gmail.com
                </a>
              </div>
            </div>
            
            <div class="flex items-start space-x-3 p-2 rounded-lg hover:bg-white/10 transition-all duration-200">
              <FontAwesomeIcon :icon="faMapMarkerAlt" class="w-5 h-5 text-red-300 mt-1 flex-shrink-0" />
              <div class="flex-1 min-w-0">
                <span class="text-red-200 text-sm">Dirección:</span>
                <a href="https://maps.app.goo.gl/Se61cWuQWd39rNkSA"
                   class="block text-red-100 hover:text-white underline decoration-dotted transition-colors"
                   target="_blank" rel="noopener">
                  2a Calle Oriente casa #12, Chalatenango, El Salvador
                </a>
              </div>
            </div>
          </div>
        </div>
        
        <div class="space-y-4">
          <div class="flex items-center space-x-3 mb-4">
            <div class="w-8 h-8 bg-gradient-to-br from-pink-400 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
              <FontAwesomeIcon :icon="faInstagram" class="w-4 h-4 text-white" />
            </div>
            <h3 class="font-bold text-xl text-white">Síguenos</h3>
          </div>
          <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20 space-y-3">
            <div class="grid grid-cols-1 gap-3">
              <a href="https://www.facebook.com/share/1C7tZxDHzh/" target="_blank" 
                 class="flex items-center space-x-3 p-2 rounded-lg hover:bg-white/10 transition-all duration-200 group">
                <div class="w-8 h-8 bg-gradient-to-br from-white/20 to-white/10 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                  <FontAwesomeIcon :icon="faFacebook" class="w-4 h-4 text-white group-hover:text-yellow-300 transition-colors" />
                </div>
                <span class="text-red-100 group-hover:text-white transition-colors font-medium">Facebook</span>
              </a>
              <a href="https://www.tiktok.com/@vasir_sv?_t=ZM-8wz8jwve57Y&_r=1" target="_blank" 
                 class="flex items-center space-x-3 p-2 rounded-lg hover:bg-white/10 transition-all duration-200 group">
                <div class="w-8 h-8 bg-gradient-to-br from-white/20 to-white/10 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                  <FontAwesomeIcon :icon="faTiktok" class="w-4 h-4 text-white group-hover:text-yellow-300 transition-colors" />
                </div>
                <span class="text-red-100 group-hover:text-white transition-colors font-medium">TikTok</span>
              </a>
              <a href="https://www.instagram.com/vasir_sv?igsh=MWx3aGFzdnB5Y2l2OA==" target="_blank" 
                 class="flex items-center space-x-3 p-2 rounded-lg hover:bg-white/10 transition-all duration-200 group">
                <div class="w-8 h-8 bg-gradient-to-br from-white/20 to-white/10 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                  <FontAwesomeIcon :icon="faInstagram" class="w-4 h-4 text-white group-hover:text-yellow-300 transition-colors" />
                </div>
                <span class="text-red-100 group-hover:text-white transition-colors font-medium">Instagram</span>
              </a>
            </div>
          </div>
          
          <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
            <div class="flex items-center space-x-3 mb-3">
              <div class="w-6 h-6 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center">
                <span class="text-white text-xs font-bold">★</span>
              </div>
              <h3 class="font-bold text-lg text-white">Categoría</h3>
            </div>
            <p class="text-red-100 font-medium">Agencia de turismo profesional</p>
          </div>
        </div>
      </div>
      
      <div class="relative bg-gradient-to-r from-red-900 via-red-800 to-red-900 text-center py-4 border-t border-red-600/30">
        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent"></div>
        <p class="relative text-red-100 text-sm font-medium">
          &copy; {{ new Date().getFullYear() }} Vasir. Todos los derechos reservados.
        </p>
      </div>
    </footer>
</template>