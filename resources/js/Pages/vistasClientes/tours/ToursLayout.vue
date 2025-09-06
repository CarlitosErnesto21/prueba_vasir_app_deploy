<template>
  <Catalogo>
    <!-- Contenido principal -->
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-50">
      <!-- Header con título y estadísticas -->
      <div class="bg-white shadow-sm border-b border-blue-100 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center">
            <h1 :class="[
              'text-3xl sm:text-4xl font-bold mb-4',
              tipoTour === 'nacional' ? 'text-red-600' : 'text-blue-600'
            ]">
              {{ titulo }}
            </h1>
            <p class="text-gray-600 text-lg mb-6">{{ descripcion }}</p>

            <!-- Estadísticas -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-2xl mx-auto">
              <div class="bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg p-4">
                <div class="text-2xl font-bold text-blue-800">{{ estadisticas.totalDestinos }}</div>
                <div class="text-blue-600 text-sm">Destinos</div>
              </div>
              <div class="bg-gradient-to-br from-green-100 to-green-200 rounded-lg p-4">
                <div class="text-2xl font-bold text-green-800">{{ estadisticas.totalPaises }}</div>
                <div class="text-green-600 text-sm">Ubicaciones</div>
              </div>
              <div class="bg-gradient-to-br from-purple-100 to-purple-200 rounded-lg p-4">
                <div class="text-2xl font-bold text-purple-800">
                  ${{ estadisticas.precioMinimo.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                </div>
                <div class="text-purple-600 text-sm">Desde</div>
              </div>
              <div class="bg-gradient-to-br from-orange-100 to-orange-200 rounded-lg p-4">
                <div class="text-2xl font-bold text-orange-800">
                  {{ tours.filter(t => esTourReservable(t)).length }}
                </div>
                <div class="text-orange-600 text-sm">Disponibles</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Contenido principal -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Estados de carga y error -->
        <div v-if="loading" class="flex justify-center items-center py-20">
          <div class="text-center">
            <i class="pi pi-spin pi-spinner text-4xl text-blue-600 mb-4"></i>
            <p class="text-gray-600">Cargando tours...</p>
          </div>
        </div>

        <div v-else-if="error" class="text-center py-20">
          <i class="pi pi-exclamation-triangle text-4xl text-red-500 mb-4"></i>
          <h3 class="text-xl font-semibold text-gray-800 mb-2">Error al cargar los tours</h3>
          <p class="text-gray-600 mb-4">{{ error }}</p>
          <Button 
            label="Reintentar" 
            @click="recargarTours"
            :class="[
              '!text-white !border-none',
              tipoTour === 'nacional' ? '!bg-red-600 hover:!bg-red-700' : '!bg-blue-600 hover:!bg-blue-700'
            ]"
          />
        </div>

        <!-- Grid de tours -->
        <div v-else-if="tours.length > 0">
          <div class="mb-4 text-center">
            <p class="text-sm text-gray-600">Mostrando {{ tours.length }} tours disponibles</p>
          </div>
          
          <!-- Grid mejorado funcional -->
          <ToursGridMejorado 
            :tours="tours" 
            :tipo-tour="tipoTour"
            :reservar-tour="reservarTour"
          />
          
          <!-- Grid original (comentado para debugging) -->
          <!--
          <ToursGrid
            :tours="tours"
            :tipo-tour="tipoTour"
            :obtener-estado-info="obtenerEstadoInfo"
            :es-tour-reservable="esTourReservable"
            :obtener-texto-boton="obtenerTextoBoton"
            :formatear-fecha="formatearFecha"
            :calcular-duracion="calcularDuracion"
            :obtener-imagen-actual="obtenerImagenActual"
            :reservar-tour="reservarTour"
            :ver-galeria="verGaleria"
            :inicializar-carrusel="inicializarCarrusel"
            :detener-todos-los-carruseles="detenerTodosLosCarruseles"
            :card-image-indices="cardImageIndices"
          />
          -->
        </div>

        <!-- Estado vacío -->
        <div v-else class="text-center py-20">
          <i class="pi pi-compass text-6xl text-gray-400 mb-4"></i>
          <h3 class="text-xl font-semibold text-gray-800 mb-2">No hay tours disponibles</h3>
          <p class="text-gray-600">Por el momento no tenemos tours {{ tipoTour === 'nacional' ? 'nacionales' : 'internacionales' }} disponibles.</p>
        </div>
      </div>
    </div>

    <!-- Galería de imágenes -->
    <ImageGallery
      :visible="showImageDialog"
      :imagenes="selectedTourImages"
      :current-image-index="currentImageIndex"
      :is-auto-playing="isGalleryAutoPlaying"
      :cerrar-galeria="cerrarGaleria"
      :siguiente-imagen="siguienteImagen"
      :imagen-anterior="imagenAnterior"
      :ir-a-imagen="irAImagen"
      :toggle-auto-play="toggleAutoPlay"
    />

    <!-- Modal de reserva de tour -->
    <Toast />
    <ModalReservaTour
      v-if="tourSeleccionado"
      :visible="showReservaDialog"
      :tour="tourSeleccionado"
      @update:visible="showReservaDialog = $event"
      @reserva-confirmada="manejarConfirmacionReserva"
    />

    <!-- Modal de autenticación requerida -->
    <ModalAuthRequerido
      :visible="showAuthDialog"
      :tour="tourSeleccionado"
      @update:visible="showAuthDialog = $event"
    />
  </Catalogo>
</template>

<script setup>
import { onMounted, onUnmounted, watch } from 'vue'
import Catalogo from '../../Catalogo.vue'
import ToursGrid from './components/ToursGrid.vue'
import ToursGridSimple from './components/ToursGridSimple.vue'
import ToursGridMejorado from './components/ToursGridMejorado.vue'
import ImageGallery from './components/ImageGallery.vue'
import ModalReservaTour from '../Modales/ModalReservaTour.vue'
import ModalAuthRequerido from '../Modales/ModalAuthRequerido.vue'
import Button from 'primevue/button'
import Toast from 'primevue/toast'

const props = defineProps({
  // Props principales
  titulo: {
    type: String,
    required: true
  },
  descripcion: {
    type: String,
    required: true
  },
  tipoTour: {
    type: String,
    required: true,
    validator: value => ['nacional', 'internacional'].includes(value)
  },
  urlApi: {
    type: String,
    required: true
  },
  
  // Props del composable
  tours: {
    type: Array,
    required: true
  },
  loading: {
    type: Boolean,
    required: true
  },
  error: {
    type: [String, null],
    default: null
  },
  showReservaDialog: {
    type: Boolean,
    required: true
  },
  showAuthDialog: {
    type: Boolean,
    required: true
  },
  tourSeleccionado: {
    type: [Object, null],
    default: null
  },
  showImageDialog: {
    type: Boolean,
    required: true
  },
  selectedTourImages: {
    type: Array,
    required: true
  },
  currentImageIndex: {
    type: Number,
    required: true
  },
  isGalleryAutoPlaying: {
    type: Boolean,
    required: true
  },
  cardImageIndices: {
    type: Object,
    required: true
  },
  estadisticas: {
    type: Object,
    required: true
  },
  
  // Funciones del composable
  obtenerTours: {
    type: Function,
    required: true
  },
  obtenerEstadoInfo: {
    type: Function,
    required: true
  },
  esTourReservable: {
    type: Function,
    required: true
  },
  obtenerTextoBoton: {
    type: Function,
    required: true
  },
  formatearFecha: {
    type: Function,
    required: true
  },
  calcularDuracion: {
    type: Function,
    required: true
  },
  obtenerImagenActual: {
    type: Function,
    required: true
  },
  reservarTour: {
    type: Function,
    required: true
  },
  manejarConfirmacionReserva: {
    type: Function,
    required: true
  },
  verificarReservaPendiente: {
    type: Function,
    required: true
  },
  inicializarCarrusel: {
    type: Function,
    required: true
  },
  detenerTodosLosCarruseles: {
    type: Function,
    required: true
  },
  verGaleria: {
    type: Function,
    required: true
  },
  cerrarGaleria: {
    type: Function,
    required: true
  },
  siguienteImagen: {
    type: Function,
    required: true
  },
  imagenAnterior: {
    type: Function,
    required: true
  },
  irAImagen: {
    type: Function,
    required: true
  },
  toggleAutoPlay: {
    type: Function,
    required: true
  }
})

// Función para recargar tours
const recargarTours = async () => {
  await props.obtenerTours(props.urlApi)
}

// Watcher para debuggear cambios en tours
watch(() => props.tours, (newTours) => {
  console.log('🔄 Tours cambiaron en ToursLayout:', newTours.length)
  console.log('📝 Tours recibidos:', newTours)
}, { immediate: true })

// Lifecycle hooks
onMounted(async () => {
  // Obtener tours desde la API
  await props.obtenerTours(props.urlApi)
  
  // Verificar reserva pendiente después del login
  await props.verificarReservaPendiente()
  
  // Verificación adicional con delay para problemas de timing
  setTimeout(async () => {
    await props.verificarReservaPendiente()
  }, 500)
  
  // Inicializar carruseles para todos los tours con múltiples imágenes
  props.tours.forEach(tour => {
    if (tour.imagenes && tour.imagenes.length > 1) {
      props.inicializarCarrusel(tour)
    }
  })
})

onUnmounted(() => {
  // Limpiar todos los intervalos al desmontar el componente
  props.detenerTodosLosCarruseles()
})
</script>
