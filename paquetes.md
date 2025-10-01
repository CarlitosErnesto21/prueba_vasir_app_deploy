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
                                <FontAwesomeIcon :icon="faBus" class="w-4 h-4 mr-3 group-hover:scale-110 transition-transform duration-300" />
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
                                <FontAwesomeIcon :icon="faHotel" class="w-4 h-4 mr-3 group-hover:scale-110 transition-transform duration-300" />
                                Mis Reservaciones
                            </Link>
                        </div>
                    </div>
                </div>










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



const showLogoTooltip = ref(false)
//// Watcher para mostrar/ocultar tooltip del logo con retraso
let logoTooltipTimeout = null
let logoTooltipHideTimeout = null
watch(isSidebarOpen, (open) => {
  if (open) {
    // Oculta el tooltip por si acaso
    showLogoTooltip.value = false
    // Espera 1.5 segundos antes de mostrar el tooltip
    logoTooltipTimeout = setTimeout(() => {
      showLogoTooltip.value = true
      // Oculta el tooltip después de 3 segundos
      logoTooltipHideTimeout = setTimeout(() => {
        showLogoTooltip.value = false
      }, 3000)
    }, 500)
  } else {
    showLogoTooltip.value = false
    // Limpia los timeouts si el aside se cierra antes
    if (logoTooltipTimeout) {
      clearTimeout(logoTooltipTimeout)
      logoTooltipTimeout = null
    }
    if (logoTooltipHideTimeout) {
      clearTimeout(logoTooltipHideTimeout)
      logoTooltipHideTimeout = null
    }
  }
})

            <div class="relative flex items-center">
              <transition name="fade">
                <span v-if="showLogoTooltip" class="absolute left-full top-1/2 -translate-y-1/2 ml-2 px-2 py-1 bg-gray-500/80 text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                  <FontAwesomeIcon :icon="faArrowLeft" class="w-4 h-4 mr-1" />Clic para ir a Inicio
                </span>
              </transition>
            </div>