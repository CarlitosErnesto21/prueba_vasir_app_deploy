<template>
  <Catalogo>
    <div class="min-h-screen bg-gray-50 py-4 sm:py-8">
      <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="mb-4 sm:mb-8">
          <ol class="flex items-center space-x-1 sm:space-x-2 text-xs sm:text-sm">
            <li>
              <Link 
                href="/" 
                :class="tipo === 'nacional' ? 'text-red-600 hover:text-red-800' : 'text-blue-600 hover:text-blue-800'"
              >
                Inicio
              </Link>
            </li>
            <li class="text-gray-500">/</li>
            <li>
              <Link 
                :href="tipo === 'nacional' ? '/tours-nacionales' : '/tours-internacionales'"
                :class="tipo === 'nacional' ? 'text-red-600 hover:text-red-800' : 'text-blue-600 hover:text-blue-800'"
              >
                {{ tipo === 'nacional' ? 'Tours Nacionales' : 'Tours Internacionales' }}
              </Link>
            </li>
            <li class="text-gray-500">/</li>
            <li class="text-gray-900 font-medium truncate">{{ tour.nombre }}</li>
          </ol>
        </nav>

        <!-- Contenido principal -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
          <!-- Galería de imágenes -->
          <div 
            class="relative w-full h-64 sm:h-72 md:h-80 lg:h-96 bg-gray-100 rounded-t-lg overflow-hidden flex items-center justify-center"
            @mouseenter="pausarCarrusel"
            @mouseleave="reanudarCarrusel"
          >
            <div v-if="tour.imagenes && tour.imagenes.length > 0" class="relative w-full h-full flex items-center justify-center">
              <img
                :src="obtenerImagenActual()"
                :alt="tour.nombre"
                class="max-w-full max-h-full object-contain transition-opacity duration-500"
              />
              
              <!-- Controles de navegación de imágenes -->
              <div v-if="tour.imagenes.length > 1" class="absolute inset-0 flex items-center justify-between p-2 sm:p-4 pointer-events-none">
                <button
                  @click="imagenAnterior"
                  class="bg-black/50 text-white hover:bg-black/70 rounded-full p-2 sm:p-3 transition-all duration-200 pointer-events-auto flex items-center justify-center"
                >
                  <i class="pi pi-chevron-left text-sm sm:text-lg"></i>
                </button>
                <button
                  @click="imagenSiguiente"
                  class="bg-black/50 text-white hover:bg-black/70 rounded-full p-2 sm:p-3 transition-all duration-200 pointer-events-auto flex items-center justify-center"
                >
                  <i class="pi pi-chevron-right text-sm sm:text-lg"></i>
                </button>
              </div>

              <!-- Indicadores de imagen -->
              <div v-if="tour.imagenes.length > 1" class="absolute bottom-4 left-1/2 transform -translate-x-1/2 z-10">
                <div class="flex space-x-2">
                  <button
                    v-for="(imagen, index) in tour.imagenes"
                    :key="index"
                    @click="cambiarImagen(index)"
                    :class="[
                      'w-3 h-3 rounded-full transition-all duration-200 hover:scale-110',
                      currentImageIndex === index ? 'bg-white' : 'bg-white/50 hover:bg-white/70'
                    ]"
                  />
                </div>
              </div>
            </div>
            <div v-else class="w-full h-full bg-gray-300 flex items-center justify-center rounded-t-lg">
              <span class="text-gray-500 text-lg">Sin imagen disponible</span>
            </div>
          </div>

          <!-- Información del tour -->
          <div class="p-4 sm:p-6 md:p-8">
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 lg:gap-8">
              <!-- Información principal -->
              <div class="order-2 xl:order-1">
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-3 sm:mb-4">{{ tour.nombre }}</h1>
                
                <div class="mb-4 sm:mb-6">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs sm:text-sm font-medium bg-blue-100 text-blue-800">
                    {{ tour.categoria?.nombre }}
                  </span>
                </div>

                <div class="space-y-3 sm:space-y-4 mb-4 sm:mb-6">
                  <div class="flex items-start text-gray-600 text-sm sm:text-base">
                    <i class="pi pi-calendar mr-2 sm:mr-3 text-blue-600 mt-0.5 text-sm sm:text-base"></i>
                    <span><strong>Fecha de salida:</strong> {{ formatearFecha(tour.fecha_salida) }}</span>
                  </div>
                  <div class="flex items-start text-gray-600 text-sm sm:text-base">
                    <i class="pi pi-calendar mr-2 sm:mr-3 text-blue-600 mt-0.5 text-sm sm:text-base"></i>
                    <span><strong>Fecha de regreso:</strong> {{ formatearFecha(tour.fecha_regreso) }}</span>
                  </div>
                  <div class="flex items-start text-gray-600 text-sm sm:text-base">
                    <i class="pi pi-clock mr-2 sm:mr-3 text-blue-600 mt-0.5 text-sm sm:text-base"></i>
                    <span><strong>Duración:</strong> {{ calcularDuracion(tour.fecha_salida, tour.fecha_regreso) }}</span>
                  </div>
                  <div class="flex items-start text-gray-600 text-sm sm:text-base">
                    <i class="pi pi-map-marker mr-2 sm:mr-3 text-blue-600 mt-0.5 text-sm sm:text-base"></i>
                    <span><strong>Punto de salida:</strong> {{ tour.punto_salida }}</span>
                  </div>
                  <div v-if="tour.transporte" class="flex items-start text-gray-600 text-sm sm:text-base">
                    <i class="pi pi-car mr-2 sm:mr-3 text-blue-600 mt-0.5 text-sm sm:text-base"></i>
                    <span><strong>Transporte:</strong> {{ tour.transporte?.nombre }}</span>
                  </div>
                  <div v-if="tour.cupo_min && tour.cupo_max" class="flex items-start text-gray-600 text-sm sm:text-base">
                    <i class="pi pi-users mr-2 sm:mr-3 text-blue-600 mt-0.5 text-sm sm:text-base"></i>
                    <span><strong>Cupo:</strong> {{ tour.cupo_min }} - {{ tour.cupo_max }} personas</span>
                  </div>
                </div>

                <!-- Precio -->
                <div class="mb-4 sm:mb-6 order-1 xl:order-none">
                  <div class="text-2xl sm:text-3xl font-bold text-red-600">
                    ${{ parseFloat(tour.precio).toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                  </div>
                  <div class="text-gray-600 text-sm sm:text-base">Por persona</div>
                </div>

                <!-- Botón de reserva -->
                <button
                  :class="[
                    'w-full text-white font-semibold py-2 sm:py-3 px-4 sm:px-6 rounded-lg transition-colors duration-200 text-sm sm:text-base',
                    tipo === 'nacional' 
                      ? 'bg-red-400 hover:bg-red-500' 
                      : 'bg-blue-400 hover:bg-blue-500'
                  ]"
                >
                  Reservar Tour
                </button>
              </div>

              <!-- Detalles adicionales -->
              <div class="order-3 xl:order-2">
                <!-- Sección Lo que incluye / No incluye -->
                <div class="mb-4 sm:mb-6">
                  <!-- Layout móvil: lado a lado -->
                  <div class="sm:hidden">
                    <div class="grid grid-cols-2 gap-2">
                      <!-- Lo que incluye -->
                      <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-2 flex items-center">
                          <i class="pi pi-check-circle mr-1 text-green-600 text-xs"></i>
                          <span class="truncate">Lo que incluye</span>
                        </h3>
                        <div class="bg-green-50 rounded-lg p-2">
                          <ul v-if="textoALista(tour.incluye).length > 0" class="space-y-1">
                            <li 
                              v-for="(item, index) in textoALista(tour.incluye)" 
                              :key="index"
                              class="text-gray-700 text-xs leading-tight flex items-start"
                            >
                              <span class="text-green-600 mr-1 mt-0.5 text-xs">▶</span>
                              <span>{{ item }}</span>
                            </li>
                          </ul>
                          <p v-else class="text-gray-500 text-xs italic">No hay información disponible</p>
                        </div>
                      </div>
                      
                      <!-- Lo que NO incluye -->
                      <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-2 flex items-center">
                          <i class="pi pi-times-circle mr-1 text-red-600 text-xs"></i>
                          <span class="truncate">No incluye</span>
                        </h3>
                        <div class="bg-red-50 rounded-lg p-2">
                          <ul v-if="textoALista(tour.no_incluye).length > 0" class="space-y-1">
                            <li 
                              v-for="(item, index) in textoALista(tour.no_incluye)" 
                              :key="index"
                              class="text-gray-700 text-xs leading-tight flex items-start"
                            >
                              <span class="text-red-600 mr-1 mt-0.5 text-xs">▶</span>
                              <span>{{ item }}</span>
                            </li>
                          </ul>
                          <p v-else class="text-gray-500 text-xs italic">No hay información disponible</p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Layout desktop: vertical -->
                  <div class="hidden sm:block">
                    <!-- Lo que incluye -->
                    <div class="mb-4 sm:mb-6">
                      <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2 sm:mb-3 flex items-center">
                        <i class="pi pi-check-circle mr-2 text-green-600 text-sm sm:text-base"></i>
                        Lo que incluye
                      </h3>
                      <div class="bg-green-50 rounded-lg p-3 sm:p-4">
                        <ul v-if="textoALista(tour.incluye).length > 0" class="space-y-2">
                          <li 
                            v-for="(item, index) in textoALista(tour.incluye)" 
                            :key="index"
                            class="text-gray-700 text-sm sm:text-base flex items-start"
                          >
                            <span class="text-green-600 mr-2 mt-1 text-sm">▶</span>
                            <span>{{ item }}</span>
                          </li>
                        </ul>
                        <p v-else class="text-gray-500 text-sm sm:text-base italic">No hay información disponible</p>
                      </div>
                    </div>

                    <!-- Lo que NO incluye -->
                    <div class="mb-4 sm:mb-6">
                      <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2 sm:mb-3 flex items-center">
                        <i class="pi pi-times-circle mr-2 text-red-600 text-sm sm:text-base"></i>
                        Lo que NO incluye
                      </h3>
                      <div class="bg-red-50 rounded-lg p-3 sm:p-4">
                        <ul v-if="textoALista(tour.no_incluye).length > 0" class="space-y-2">
                          <li 
                            v-for="(item, index) in textoALista(tour.no_incluye)" 
                            :key="index"
                            class="text-gray-700 text-sm sm:text-base flex items-start"
                          >
                            <span class="text-red-600 mr-2 mt-1 text-sm">▶</span>
                            <span>{{ item }}</span>
                          </li>
                        </ul>
                        <p v-else class="text-gray-500 text-sm sm:text-base italic">No hay información disponible</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Información adicional -->
                <div class="bg-blue-50 rounded-lg p-3 sm:p-4">
                  <h3 class="text-base sm:text-lg font-semibold text-blue-900 mb-2">
                    <i class="pi pi-info-circle mr-2 text-sm sm:text-base"></i>
                    Información importante
                  </h3>
                  <ul class="text-blue-800 space-y-1 text-xs sm:text-sm">
                    <li>• Confirmar disponibilidad antes de realizar el pago</li>
                    <li>• Los precios pueden variar según la temporada</li>
                    <li>• Se requiere documento de identidad vigente</li>
                    <li>• Cancelaciones con 48 horas de anticipación</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Botón para regresar -->
        <div class="mt-4 sm:mt-8 text-center px-4">
          <button
            @click="regresar"
            :class="[
              'inline-flex items-center text-white text-sm sm:text-base py-2 sm:py-3 px-4 sm:px-6 rounded-lg transition-colors duration-200',
              tipo === 'nacional' 
                ? 'bg-red-400 hover:bg-red-500' 
                : 'bg-blue-400 hover:bg-blue-500'
            ]"
          >
            <i class="pi pi-arrow-left mr-2"></i>
            Regresar a Tours
          </button>
        </div>
      </div>
    </div>
  </Catalogo>
</template>

<script setup>
import Catalogo from '../Catalogo.vue'
import { Link } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'

// Props
const props = defineProps({
  tour: {
    type: Object,
    required: true
  },
  tipo: {
    type: String,
    required: true,
    validator: value => ['nacional', 'internacional'].includes(value)
  }
})

// Variables para la galería de imágenes
const currentImageIndex = ref(0)
const carruselInterval = ref(null)

// Función para formatear fecha
const formatearFecha = (fecha) => {
  if (!fecha) return ''
  const date = new Date(fecha)
  return date.toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Función para calcular duración
const calcularDuracion = (fechaSalida, fechaRegreso) => {
  if (!fechaSalida || !fechaRegreso) return '1 día'
  
  const salida = new Date(fechaSalida)
  const regreso = new Date(fechaRegreso)
  const diffTime = Math.abs(regreso - salida)
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  
  return diffDays === 1 ? '1 día' : `${diffDays} días`
}

// Función para obtener la imagen actual
const obtenerImagenActual = () => {
  if (!props.tour.imagenes || props.tour.imagenes.length === 0) {
    return 'https://via.placeholder.com/800x500/2563eb/ffffff?text=Sin+Imagen'
  }
  
  const imagen = props.tour.imagenes[currentImageIndex.value]
  const nombreImagen = typeof imagen === 'string' ? imagen : imagen.nombre
  
  return `/images/tours/${nombreImagen}`
}

// Funciones de navegación de imágenes
const imagenAnterior = () => {
  if (props.tour.imagenes && props.tour.imagenes.length > 1) {
    currentImageIndex.value = currentImageIndex.value === 0 
      ? props.tour.imagenes.length - 1 
      : currentImageIndex.value - 1
    reiniciarCarrusel() // Reiniciar el carrusel después de navegación manual
  }
}

const imagenSiguiente = () => {
  if (props.tour.imagenes && props.tour.imagenes.length > 1) {
    currentImageIndex.value = (currentImageIndex.value + 1) % props.tour.imagenes.length
    reiniciarCarrusel() // Reiniciar el carrusel después de navegación manual
  }
}

// Función para cambiar a una imagen específica
const cambiarImagen = (index) => {
  currentImageIndex.value = index
  reiniciarCarrusel() // Reiniciar el carrusel después de navegación manual
}

// Función para convertir texto a lista
const textoALista = (texto) => {
  if (!texto) return []
  return texto.split('|').filter(item => item.trim()).map(item => item.trim())
}

// Funciones del carrusel automático
const iniciarCarrusel = () => {
  if (props.tour.imagenes && props.tour.imagenes.length > 1) {
    carruselInterval.value = setInterval(() => {
      currentImageIndex.value = (currentImageIndex.value + 1) % props.tour.imagenes.length
    }, 3000) // Cambiar cada 3 segundos
  }
}

const detenerCarrusel = () => {
  if (carruselInterval.value) {
    clearInterval(carruselInterval.value)
    carruselInterval.value = null
  }
}

const reiniciarCarrusel = () => {
  detenerCarrusel()
  setTimeout(() => {
    iniciarCarrusel()
  }, 1000) // Esperar 1 segundo antes de reiniciar
}

const pausarCarrusel = () => {
  detenerCarrusel()
}

const reanudarCarrusel = () => {
  iniciarCarrusel()
}

// Lifecycle hooks
onMounted(() => {
  iniciarCarrusel()
})

onUnmounted(() => {
  detenerCarrusel()
})

// Función para regresar
const regresar = () => {
  const ruta = props.tipo === 'nacional' ? '/tours-nacionales' : '/tours-internacionales'
  router.visit(ruta)
}
</script>
