<script setup>
import Catalogo from '../Catalogo.vue'
import Card from 'primevue/card'
import Button from 'primevue/button'
import Dialog from 'primevue/dialog'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'

// Recibir los props del controlador (opcional, como fallback)
const props = defineProps({
  tours: {
    type: Array,
    default: () => []
  }
})

// Estados reactivos
const tours = ref([])
const loading = ref(true)
const error = ref(null)

// URLs de la API
const API_BASE_URL = '/api'
// URL de la API
const url = "/api/tours?categoria=nacional"

// Computed properties para estadísticas dinámicas
const estadisticas = computed(() => {
  if (!tours.value || tours.value.length === 0) {
    return {
      totalDestinos: 0,
      totalPaises: 0,
      precioMinimo: 0,
      paisesUnicos: []
    }
  }

  const precios = tours.value.map(tour => parseFloat(tour.precio)).filter(precio => !isNaN(precio))
  // Como no tenemos campo pais aún, usaremos punto_salida como ubicación
  const ubicacionesUnicas = [...new Set(tours.value.map(tour => tour.punto_salida).filter(ubicacion => ubicacion))]
  
  return {
    totalDestinos: tours.value.length,
    totalPaises: ubicacionesUnicas.length,
    precioMinimo: precios.length > 0 ? Math.min(...precios) : 0,
    paisesUnicos: ubicacionesUnicas
  }
})

// Función para obtener tours desde la API
const obtenerTours = async () => {
  try {
    loading.value = true
    error.value = null
    
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    
    if (!response.ok) {
      throw new Error(`Error ${response.status}: ${response.statusText}`)
    }
    
    const data = await response.json()
    tours.value = data.data || data || []
    
  } catch (err) {
    console.error('Error al obtener tours:', err)
    error.value = err.message
    // Usar props como fallback si hay error en la API
    tours.value = props.tours || []
  } finally {
    loading.value = false
  }
}

// Función para formatear la fecha
const formatearFecha = (fecha) => {
  if (!fecha) return ''
  const date = new Date(fecha)
  return date.toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Función para formatear la duración
const calcularDuracion = (fechaSalida, fechaRegreso) => {
  if (!fechaSalida || !fechaRegreso) return '1 día'
  
  const salida = new Date(fechaSalida)
  const regreso = new Date(fechaRegreso)
  
  // Normalizar las fechas para que solo considere el día (sin hora)
  salida.setHours(0, 0, 0, 0)
  regreso.setHours(0, 0, 0, 0)
  
  const diffTime = regreso.getTime() - salida.getTime()
  const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1 // +1 porque incluimos el día de salida
  
  return diffDays === 1 ? '1 día' : `${diffDays} días`
}

// Variables para el carrusel de imágenes
const showImageDialog = ref(false)
const selectedTourImages = ref([])
const currentImageIndex = ref(0)
const galleryIntervalId = ref(null)
const isGalleryAutoPlaying = ref(true)

// Variables para carruseles automáticos en las cards
const cardImageIndices = ref({})
const intervalIds = ref({})

// Función para obtener la primera imagen
const obtenerPrimeraImagen = (imagenes) => {
  if (!imagenes || imagenes.length === 0) {
    return 'https://via.placeholder.com/400x300/ef4444/ffffff?text=Sin+Imagen'
  }
  
  // Si es un string, asumir que es el nombre del archivo
  const primeraImagen = imagenes[0]
  const nombreImagen = typeof primeraImagen === 'string' ? primeraImagen : primeraImagen.nombre
  
  return `/images/tours/${nombreImagen}`
}

// Función para obtener la imagen actual del carrusel automático
const obtenerImagenActual = (tour) => {
  if (!tour.imagenes || tour.imagenes.length === 0) {
    return 'https://via.placeholder.com/400x300/ef4444/ffffff?text=Sin+Imagen'
  }
  
  // Si solo tiene una imagen, mostrar esa
  if (tour.imagenes.length === 1) {
    const nombreImagen = typeof tour.imagenes[0] === 'string' ? tour.imagenes[0] : tour.imagenes[0].nombre
    return `/images/tours/${nombreImagen}`
  }
  
  // Si tiene múltiples imágenes, usar el índice del carrusel
  const currentIndex = cardImageIndices.value[tour.id] || 0
  const imagen = tour.imagenes[currentIndex]
  const nombreImagen = typeof imagen === 'string' ? imagen : imagen.nombre
  
  return `/images/tours/${nombreImagen}`
}

// Función para inicializar carrusel automático
const inicializarCarrusel = (tour) => {
  if (!tour.imagenes || tour.imagenes.length <= 1) return
  
  // Inicializar índice si no existe
  if (!(tour.id in cardImageIndices.value)) {
    cardImageIndices.value[tour.id] = 0
  }
  
  // Crear intervalo para cambiar imágenes automáticamente
  if (!(tour.id in intervalIds.value)) {
    intervalIds.value[tour.id] = setInterval(() => {
      cardImageIndices.value[tour.id] = (cardImageIndices.value[tour.id] + 1) % tour.imagenes.length
    }, 3000) // Cambiar cada 3 segundos
  }
}

// Función para detener carrusel automático
const detenerCarrusel = (tourId) => {
  if (intervalIds.value[tourId]) {
    clearInterval(intervalIds.value[tourId])
    delete intervalIds.value[tourId]
  }
}

// Función para detener todos los carruseles
const detenerTodosLosCarruseles = () => {
  Object.keys(intervalIds.value).forEach(tourId => {
    clearInterval(intervalIds.value[tourId])
  })
  intervalIds.value = {}
  detenerCarruselGaleria() // También detener el carrusel de la galería
}

// Lifecycle hooks
onMounted(async () => {
  // Obtener tours desde la API
  await obtenerTours()
  
  // Inicializar carruseles para todos los tours con múltiples imágenes
  tours.value.forEach(tour => {
    if (tour.imagenes && tour.imagenes.length > 1) {
      inicializarCarrusel(tour)
    }
  })
})

onUnmounted(() => {
  detenerTodosLosCarruseles()
})

// Función para obtener todas las imágenes
const obtenerTodasLasImagenes = (imagenes) => {
  if (!imagenes || imagenes.length === 0) {
    return ['https://via.placeholder.com/400x300/ef4444/ffffff?text=Sin+Imagen']
  }
  
  return imagenes.map(imagen => {
    const nombreImagen = typeof imagen === 'string' ? imagen : imagen.nombre
    return `/images/tours/${nombreImagen}`
  })
}

// Función para mostrar galería de imágenes
const mostrarGaleria = (tour) => {
  selectedTourImages.value = obtenerTodasLasImagenes(tour.imagenes)
  currentImageIndex.value = 0
  showImageDialog.value = true
  isGalleryAutoPlaying.value = true
  
  // Iniciar carrusel automático en la galería si hay múltiples imágenes
  if (selectedTourImages.value.length > 1) {
    iniciarCarruselGaleria()
  }
}

// Función para alternar play/pausa del carrusel
const toggleGalleryAutoPlay = () => {
  if (isGalleryAutoPlaying.value) {
    detenerCarruselGaleria()
    isGalleryAutoPlaying.value = false
  } else {
    iniciarCarruselGaleria()
    isGalleryAutoPlaying.value = true
  }
}

// Función para iniciar carrusel automático en la galería
const iniciarCarruselGaleria = () => {
  detenerCarruselGaleria() // Limpiar cualquier intervalo anterior
  galleryIntervalId.value = setInterval(() => {
    siguienteImagen()
  }, 4000) // Cambiar cada 4 segundos
}

// Función para detener carrusel automático en la galería
const detenerCarruselGaleria = () => {
  if (galleryIntervalId.value) {
    clearInterval(galleryIntervalId.value)
    galleryIntervalId.value = null
  }
}

// Funciones para navegar en el carrusel (modificadas para reiniciar el timer)
const siguienteImagen = () => {
  currentImageIndex.value = (currentImageIndex.value + 1) % selectedTourImages.value.length
  // Reiniciar el timer automático solo si está activado
  if (showImageDialog.value && selectedTourImages.value.length > 1 && isGalleryAutoPlaying.value) {
    iniciarCarruselGaleria()
  }
}

const imagenAnterior = () => {
  currentImageIndex.value = currentImageIndex.value === 0 
    ? selectedTourImages.value.length - 1 
    : currentImageIndex.value - 1
  // Reiniciar el timer automático solo si está activado
  if (showImageDialog.value && selectedTourImages.value.length > 1 && isGalleryAutoPlaying.value) {
    iniciarCarruselGaleria()
  }
}

const irAImagen = (index) => {
  currentImageIndex.value = index
  // Reiniciar el timer automático solo si está activado
  if (showImageDialog.value && selectedTourImages.value.length > 1 && isGalleryAutoPlaying.value) {
    iniciarCarruselGaleria()
  }
}

// Funciones para los botones
const reservarTour = (tour) => {
  alert(`¡Tour "${tour.nombre}" seleccionado para reserva!\nPrecio: $${tour.precio}\nDuración: ${calcularDuracion(tour.fecha_salida, tour.fecha_regreso)}`)
}

const verMasInfo = (tour) => {
  // Navegar a la vista detallada del tour nacional
  router.visit(`/tours-nacionales/${tour.id}`)
}
</script>

<template>
  <Catalogo>
    <div class="p-4 bg-gray-50 min-h-screen">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
          <h1 class="text-4xl font-bold mb-4 text-red-700 flex items-center justify-center gap-3">
            <img src="/images/sv.png" alt="Bandera El Salvador" class="w-8 h-8" />
            Tours Nacionales
          </h1>
          <p class="text-lg text-gray-600 mb-2">Descubre las maravillas de El Salvador</p>
          <p class="text-sm text-gray-500">Experiencias auténticas en nuestro hermoso país</p>
        </div>

        <!-- Estado de carga -->
        <div v-if="loading && tours.length === 0" class="text-center py-12">
          <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-red-600"></div>
          <p class="mt-4 text-gray-600">Cargando tours nacionales...</p>
        </div>

        <!-- Estado de error -->
        <div v-else-if="error && tours.length === 0" class="text-center py-12">
          <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-lg max-w-md mx-auto">
            <div class="text-4xl mb-3">⚠️</div>
            <h3 class="font-semibold mb-2">No se pudieron cargar los tours</h3>
            <p class="text-sm text-red-600">Por favor, intenta recargar la página o contacta con nosotros.</p>
          </div>
        </div>

        <!-- Estado vacío -->
        <div v-else-if="!loading && tours.length === 0" class="text-center py-12">
          <div class="text-6xl mb-4">🏔️</div>
          <h3 class="text-xl font-semibold text-gray-700 mb-2">No hay tours nacionales disponibles</h3>
          <p class="text-gray-500 mb-4">Próximamente tendremos nuevos destinos nacionales.</p>
          <p class="text-sm text-gray-400">Mientras tanto, puedes explorar nuestros tours internacionales.</p>
        </div>

        <!-- Stats -->
        <div v-if="tours.length > 0" class="grid grid-cols-3 gap-2 sm:gap-3 md:gap-4 mb-6 sm:mb-8">
          <div class="bg-white rounded-lg p-3 sm:p-4 md:p-6 shadow-md text-center border border-gray-200">
            <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-red-600">{{ estadisticas.totalDestinos }}</h3>
            <p class="text-xs sm:text-sm md:text-base text-gray-600">Tours Disponibles</p>
          </div>
          <div class="bg-white rounded-lg p-3 sm:p-4 md:p-6 shadow-md text-center border border-gray-200">
            <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-green-600">
              {{ estadisticas.precioMinimo > 0 ? `Desde $${estadisticas.precioMinimo}` : 'Consultar' }}
            </h3>
            <p class="text-xs sm:text-sm md:text-base text-gray-600">Precios Accesibles</p>
          </div>
          <div class="bg-white rounded-lg p-3 sm:p-4 md:p-6 shadow-md text-center border border-gray-200">
            <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-blue-600">{{ estadisticas.totalPaises }}</h3>
            <p class="text-xs sm:text-sm md:text-base text-gray-600">Ubicaciones</p>
          </div>
        </div>

        <!-- Tours Grid -->
        <div v-if="tours.length > 0" class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
          <Card
            v-for="tour in tours"
            :key="tour.id"
            class="border border-gray-300 bg-white shadow-md hover:shadow-lg transition-all duration-300 flex flex-col h-80 sm:h-96 transform hover:-translate-y-1 overflow-hidden"
          >
            <template #header>
              <div class="relative w-full h-24 sm:h-32 bg-gradient-to-br from-gray-100 to-gray-200 rounded-t-lg overflow-hidden group cursor-pointer"
                   @click="mostrarGaleria(tour)">
                <img
                  :src="obtenerImagenActual(tour)"
                  :alt="tour.nombre"
                  class="object-contain h-full w-full bg-gray-50 group-hover:scale-105 transition-transform duration-500"
                  @error="$event.target.src = 'https://via.placeholder.com/300x200/ef4444/ffffff?text=' + encodeURIComponent(tour.nombre.substring(0, 15))"
                />
                <div class="absolute top-2 right-2 bg-green-600 text-white px-2 py-1 rounded-full text-xs font-semibold shadow-lg">
                  ${{ tour.precio }}
                </div>
                <div class="absolute bottom-2 left-2 bg-black/70 text-white px-2 py-1 rounded-full text-xs">
                  {{ calcularDuracion(tour.fecha_salida, tour.fecha_regreso) }}
                </div>
                <!-- Indicador de múltiples imágenes -->
                <div v-if="tour.imagenes && tour.imagenes.length > 1" 
                     class="absolute top-2 left-2 bg-black/70 text-white px-2 py-1 rounded-full text-xs flex items-center">
                  <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                  </svg>
                  {{ tour.imagenes.length }}
                </div>
                <!-- Indicador de carrusel activo -->
                <div v-if="tour.imagenes && tour.imagenes.length > 1" 
                     class="absolute bottom-2 right-2 flex space-x-1">
                  <div 
                    v-for="(_, index) in tour.imagenes" 
                    :key="index"
                    class="w-2 h-2 rounded-full transition-all duration-300"
                    :class="(cardImageIndices[tour.id] || 0) === index ? 'bg-white' : 'bg-white/50'"
                  ></div>
                </div>
              </div>
            </template>
            
            <template #title>
              <div class="h-10 sm:h-12 flex items-start px-3 sm:px-4 pt-2 sm:pt-3">
                <span class="text-xs sm:text-sm font-bold text-gray-800 leading-tight line-clamp-2">{{ tour.nombre }}</span>
              </div>
            </template>
            
            <template #content>
              <div class="flex-1 flex flex-col px-3 sm:px-4 pb-2">
                <div class="flex-1 space-y-1 sm:space-y-2">
                  <div class="flex items-center text-xs text-gray-500 mb-1">
                    <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="truncate">{{ tour.punto_salida }}</span>
                  </div>
                  <div class="space-y-1">
                    <p class="text-xs font-semibold text-gray-700">Salida:</p>
                    <p class="text-xs text-gray-600">{{ formatearFecha(tour.fecha_salida) }}</p>
                  </div>
                  <div v-if="tour.cupo_min && tour.cupo_max" class="text-xs">
                    <p class="font-semibold text-gray-700">Cupo:</p>
                    <p class="text-gray-600">{{ tour.cupo_min }}-{{ tour.cupo_max }} personas</p>
                  </div>
                </div>
                
                <!-- Botones dentro del content para estar dentro del card -->
                <div class="flex gap-1 sm:gap-2 mt-2 sm:mt-3 pt-1 sm:pt-2">
                  <Button
                    label="Reservar"
                    @click="reservarTour(tour)"
                    class="!bg-red-600 !border-none !px-2 sm:!px-3 !py-1 sm:!py-1.5 !text-white !text-xs font-semibold rounded-lg hover:!bg-red-700 transition-all flex-1 shadow-sm"
                  />
                  <Button
                    label="Info"
                    @click="verMasInfo(tour)"
                    outlined
                    class="!border-red-600 !text-red-600 !px-2 sm:!px-3 !py-1 sm:!py-1.5 !text-xs font-semibold rounded-lg hover:!bg-red-50 transition-all"
                  />
                </div>
              </div>
            </template>
          </Card>
        </div>

        <!-- Info adicional -->
        <div class="mt-12 bg-white rounded-xl p-8 shadow-md border border-gray-200">
          <h2 class="text-2xl font-bold text-red-700 mb-6 text-center">¿Por qué elegir nuestros tours nacionales?</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
              <div class="text-4xl mb-3">🏛️</div>
              <h3 class="font-semibold text-red-600 mb-2">Historia y Cultura</h3>
              <p class="text-gray-600 text-sm">Descubre la rica historia y tradiciones de El Salvador con guías locales expertos</p>
            </div>
            <div class="text-center">
              <div class="text-4xl mb-3">🌋</div>
              <h3 class="font-semibold text-red-600 mb-2">Naturaleza Única</h3>
              <p class="text-gray-600 text-sm">Explora volcanes, lagos, playas y bosques en paisajes únicos de Centroamérica</p>
            </div>
            <div class="text-center">
              <div class="text-4xl mb-3">🤝</div>
              <h3 class="font-semibold text-red-600 mb-2">Apoyo Local</h3>
              <p class="text-gray-600 text-sm">Contribuye al desarrollo de las comunidades locales con turismo responsable</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Diálogo para mostrar galería de imágenes -->
    <Dialog 
      v-model:visible="showImageDialog" 
      modal 
      :closable="true"
      class="max-w-4xl w-full mx-4"
      :draggable="false"
    >
      <template #header>
        <div class="flex items-center justify-between w-full">
          <h3 class="text-lg font-semibold">Galería de Imágenes</h3>
          <button 
            v-if="selectedTourImages.length > 1"
            @click="toggleGalleryAutoPlay"
            class="flex items-center gap-2 px-3 py-1 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-all text-sm"
            :title="isGalleryAutoPlaying ? 'Pausar carrusel automático' : 'Reanudar carrusel automático'"
          >
            <svg v-if="isGalleryAutoPlaying" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <svg v-else class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
            </svg>
            <span>{{ isGalleryAutoPlaying ? 'Pausar' : 'Reproducir' }}</span>
          </button>
        </div>
      </template>
      
      <div class="relative">
        <!-- Imagen principal -->
        <div class="relative h-96 bg-gray-100 rounded-lg overflow-hidden mb-4 flex items-center justify-center">
          <img 
            :src="selectedTourImages[currentImageIndex]" 
            alt="Tour imagen"
            class="max-w-full max-h-full object-contain"
          />
          
          <!-- Botones de navegación -->
          <div v-if="selectedTourImages.length > 1" class="absolute inset-0 flex items-center justify-between p-4">
            <button 
              @click="imagenAnterior"
              class="bg-black/50 text-white p-2 rounded-full hover:bg-black/70 transition-all z-10"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
            </button>
            <button 
              @click="siguienteImagen"
              class="bg-black/50 text-white p-2 rounded-full hover:bg-black/70 transition-all z-10"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </button>
          </div>
          
          <!-- Contador de imágenes -->
          <div v-if="selectedTourImages.length > 1" 
               class="absolute bottom-4 right-4 bg-black/70 text-white px-3 py-1 rounded-full text-sm z-10">
            {{ currentImageIndex + 1 }} / {{ selectedTourImages.length }}
          </div>
          
          <!-- Indicador de autoplay -->
          <div v-if="selectedTourImages.length > 1 && isGalleryAutoPlaying" 
               class="absolute top-4 right-4 bg-green-600/80 text-white px-2 py-1 rounded-full text-xs z-10 flex items-center gap-1">
            <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
            <span>Auto</span>
          </div>
        </div>
        
        <!-- Miniaturas -->
        <div v-if="selectedTourImages.length > 1" class="flex gap-2 overflow-x-auto pb-2">
          <button
            v-for="(imagen, index) in selectedTourImages"
            :key="index"
            @click="irAImagen(index)"
            class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 transition-all bg-gray-100 flex items-center justify-center"
            :class="currentImageIndex === index ? 'border-red-500' : 'border-gray-300 hover:border-gray-400'"
          >
            <img 
              :src="imagen" 
              :alt="`Miniatura ${index + 1}`"
              class="max-w-full max-h-full object-contain"
            />
          </button>
        </div>
      </div>
    </Dialog>
  </Catalogo>
</template>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
</style>
