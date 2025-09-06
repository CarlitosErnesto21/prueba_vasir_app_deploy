<script setup>
import { Head } from '@inertiajs/vue3'
import ToursLayout from './ToursLayout.vue'
import { useToursCommon } from './composables/useToursCommon.js'

// Props recibidos del controlador (opcional, como fallback)
const props = defineProps({
  tours: {
    type: Array,
    default: () => []
  }
})

// Usar el composable común
const {
  // Estados
  tours: toursComposable,
  loading,
  error,
  showReservaDialog,
  showAuthDialog,
  tourSeleccionado,
  showImageDialog,
  selectedTourImages,
  currentImageIndex,
  isGalleryAutoPlaying,
  cardImageIndices,
  estadisticas,
  
  // Funciones principales
  obtenerTours,
  reservarTour,
  verificarReservaPendiente,
  manejarConfirmacionReserva,
  
  // Funciones de utilidad
  obtenerEstadoInfo,
  esTourReservable,
  obtenerTextoBoton,
  formatearFecha,
  calcularDuracion,
  obtenerImagenActual,
  
  // Funciones de carrusel
  inicializarCarrusel,
  detenerTodosLosCarruseles,
  verGaleria,
  cerrarGaleria,
  siguienteImagen,
  imagenAnterior,
  irAImagen,
  toggleAutoPlay
} = useToursCommon()

// URL específica para tours nacionales
const URL_API = "/api/tours?categoria=nacional"

// Configuración específica para tours nacionales
const TITULO = "Tours Nacionales"
const DESCRIPCION = "Descubre los destinos más hermosos de nuestro país"
const TIPO_TOUR = "nacional"
</script>

<template>
  <Head title="Tours Nacionales" />
  
  <ToursLayout
    :titulo="TITULO"
    :descripcion="DESCRIPCION"
    :tipo-tour="TIPO_TOUR"
    :url-api="URL_API"
    :tours="toursComposable"
    :loading="loading"
    :error="error"
    :show-reserva-dialog="showReservaDialog"
    :show-auth-dialog="showAuthDialog"
    :tour-seleccionado="tourSeleccionado"
    :show-image-dialog="showImageDialog"
    :selected-tour-images="selectedTourImages"
    :current-image-index="currentImageIndex"
    :is-gallery-auto-playing="isGalleryAutoPlaying"
    :card-image-indices="cardImageIndices"
    :estadisticas="estadisticas"
    :obtener-tours="obtenerTours"
    :obtener-estado-info="obtenerEstadoInfo"
    :es-tour-reservable="esTourReservable"
    :obtener-texto-boton="obtenerTextoBoton"
    :formatear-fecha="formatearFecha"
    :calcular-duracion="calcularDuracion"
    :obtener-imagen-actual="obtenerImagenActual"
    :reservar-tour="reservarTour"
    :manejar-confirmacion-reserva="manejarConfirmacionReserva"
    :verificar-reserva-pendiente="verificarReservaPendiente"
    :inicializar-carrusel="inicializarCarrusel"
    :detener-todos-los-carruseles="detenerTodosLosCarruseles"
    :ver-galeria="verGaleria"
    :cerrar-galeria="cerrarGaleria"
    :siguiente-imagen="siguienteImagen"
    :imagen-anterior="imagenAnterior"
    :ir-a-imagen="irAImagen"
    :toggle-auto-play="toggleAutoPlay"
  />
</template>
