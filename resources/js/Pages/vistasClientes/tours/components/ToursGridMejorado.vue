<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
    <div 
      v-for="tour in tours" 
      :key="tour.id"
      class="relative overflow-hidden shadow-lg border-0 rounded-lg transform transition-all duration-300 hover:scale-105 hover:shadow-xl cursor-pointer group h-[360px] flex flex-col bg-white"
      @click="verTour(tour)"
    >
      <!-- Badge de estado en esquina superior derecha -->
      <div 
        class="absolute bottom-2 right-2 z-10 px-2 py-1 rounded-full text-xs font-semibold shadow-lg bg-yellow-500 text-black"
      >
        {{ tour.estado || 'DISPONIBLE' }}
      </div>

      <!-- Badge "destacado" si aplica -->
      <div 
        v-if="tour.destacado"
        class="absolute top-2 left-2 z-10 bg-gradient-to-r from-yellow-400 to-orange-500 text-black px-2 py-1 rounded-full text-xs font-bold shadow-lg"
      >
        ⭐ DESTACADO
      </div>

      <!-- Imagen principal -->
      <div class="relative h-48 overflow-hidden group">
        <!-- Imagen simple sin funciones complejas -->
        <img 
          :src="obtenerImagenSimple(tour)" 
          :alt="tour.nombre"
          class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
          @error="(e) => { e.target.src = 'https://via.placeholder.com/400x300/2563eb/ffffff?text=Sin+Imagen' }"
        />

        <!-- Indicadores del carrusel (solo si hay múltiples imágenes) -->
        <div 
          v-if="tour.imagenes && tour.imagenes.length > 1"
          class="absolute bottom-2 left-1/2 transform -translate-x-1/2 flex space-x-1 z-10"
        >
          <div
            v-for="(_, index) in tour.imagenes"
            :key="index"
            class="w-2 h-2 rounded-full bg-white/50"
          ></div>
        </div>
      </div>

      <!-- Contenido de la card -->
      <div class="p-4 flex flex-col flex-grow">
        <!-- Título -->
        <h3 class="text-lg font-bold text-gray-800 mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors">
          {{ tour.nombre }}
        </h3>

        <!-- Información del tour -->
        <div class="space-y-2 mb-4 flex-grow">
          <div class="flex items-center text-gray-600 text-sm">
            <i class="pi pi-calendar mr-2 text-blue-600"></i>
            <span>{{ formatearFechaSimple(tour.fecha_salida) }}</span>
          </div>
          <div class="flex items-center text-gray-600 text-sm">
            <i class="pi pi-clock mr-2 text-blue-600"></i>
            <span>{{ calcularDuracionSimple(tour.fecha_salida, tour.fecha_regreso) }}</span>
          </div>
          <div class="flex items-center text-gray-600 text-sm">
            <i class="pi pi-map-marker mr-2 text-blue-600"></i>
            <span class="truncate">{{ tour.punto_salida }}</span>
          </div>
          <div class="flex items-center text-gray-600 text-sm">
            <i class="pi pi-users mr-2 text-blue-600"></i>
            <span>{{ tour.cupos_disponibles }} cupos disponibles</span>
          </div>
        </div>

        <!-- Precio y botón -->
        <div class="mt-auto">
          <!-- Precio -->
          <div class="mb-3">
            <span class="text-2xl font-bold text-gray-800">
              ${{ parseFloat(tour.precio).toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
            </span>
            <span class="text-gray-600 text-sm ml-1">por persona</span>
          </div>

          <!-- Botones -->
          <div class="flex gap-2">
            <button
              @click.stop="reservarTourSimple(tour)"
              :disabled="tour.cupos_disponibles <= 0"
              :class="[
                'flex-1 px-3 py-2 text-sm font-semibold rounded transition-all shadow-sm',
                tour.cupos_disponibles > 0
                  ? tipoTour === 'nacional' 
                    ? 'bg-red-600 text-white hover:bg-red-700' 
                    : 'bg-blue-600 text-white hover:bg-blue-700'
                  : 'bg-gray-400 text-white cursor-not-allowed'
              ]"
            >
              {{ tour.cupos_disponibles > 0 ? 'Reservar' : 'Agotado' }}
            </button>
            <button
              @click.stop="verTour(tour)"
              :class="[
                'px-3 py-2 text-sm font-semibold rounded hover:bg-gray-50 transition-all border-2',
                tipoTour === 'nacional' 
                  ? 'border-red-600 text-red-600' 
                  : 'border-blue-600 text-blue-600'
              ]"
            >
              Info
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'

const props = defineProps({
  tours: {
    type: Array,
    required: true
  },
  tipoTour: {
    type: String,
    required: true,
    validator: value => ['nacional', 'internacional'].includes(value)
  },
  reservarTour: {
    type: Function,
    required: true
  }
})

// Función para obtener imagen simple
const obtenerImagenSimple = (tour) => {
  if (!tour.imagenes || tour.imagenes.length === 0) {
    return 'https://via.placeholder.com/400x300/2563eb/ffffff?text=Sin+Imagen'
  }
  
  const imagen = tour.imagenes[0]
  const nombreImagen = typeof imagen === 'string' ? imagen : imagen.nombre
  
  return `/images/tours/${nombreImagen}`
}

// Función para formatear fecha simple
const formatearFechaSimple = (fecha) => {
  if (!fecha) return ''
  const date = new Date(fecha)
  return date.toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Función para calcular duración simple
const calcularDuracionSimple = (fechaSalida, fechaRegreso) => {
  if (!fechaSalida || !fechaRegreso) return '1 día'
  
  const salida = new Date(fechaSalida)
  const regreso = new Date(fechaRegreso)
  
  salida.setHours(0, 0, 0, 0)
  regreso.setHours(0, 0, 0, 0)
  
  const diffTime = regreso.getTime() - salida.getTime()
  const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1
  
  return diffDays === 1 ? '1 día' : `${diffDays} días`
}

// Función para reservar tour simple
const reservarTourSimple = (tour) => {
  console.log('Reservar tour:', tour.nombre)
  props.reservarTour(tour)
}

// Función para ver tour completo
const verTour = (tour) => {
  const ruta = props.tipoTour === 'nacional' 
    ? `/tours-nacionales/${tour.id}` 
    : `/tours-internacionales/${tour.id}`
  router.visit(ruta)
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
