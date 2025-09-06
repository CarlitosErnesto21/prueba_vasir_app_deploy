import { ref, computed, watch, nextTick } from 'vue'
import { useToast } from 'primevue/usetoast'
import { usePage } from '@inertiajs/vue3'

export function useToursCommon() {
  const page = usePage()
  const user = computed(() => page.props.auth.user)
  const toast = useToast()

  // Estados reactivos comunes
  const tours = ref([])
  const loading = ref(true)
  const error = ref(null)
  const showReservaDialog = ref(false)
  const showAuthDialog = ref(false)
  const tourSeleccionado = ref(null)
  const currentApiUrl = ref('') // Para guardar la URL actual de la API

  // Variables para carruseles
  const showImageDialog = ref(false)
  const selectedTourImages = ref([])
  const currentImageIndex = ref(0)
  const galleryIntervalId = ref(null)
  const isGalleryAutoPlaying = ref(true)
  const cardImageIndices = ref({})
  const intervalIds = ref({})

  // Función para obtener tours desde la API
  const obtenerTours = async (url) => {
    try {
      console.log('🔄 Iniciando obtención de tours desde:', url)
      loading.value = true
      error.value = null
      
      // Guardar la URL para poder recargar después
      if (url) {
        currentApiUrl.value = url
      }
      
      const apiUrl = url || currentApiUrl.value
      if (!apiUrl) {
        throw new Error('No se ha especificado una URL de API')
      }
      
      const response = await fetch(apiUrl, {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      
      console.log('📡 Respuesta de la API:', response.status, response.statusText)
      
      if (!response.ok) {
        throw new Error(`Error ${response.status}: ${response.statusText}`)
      }
      
      const data = await response.json()
      console.log('📦 Datos recibidos:', data)
      
      tours.value = data.data || data || []
      console.log('✅ Tours asignados:', tours.value.length, 'tours')
      console.log('🔍 Primer tour:', tours.value[0])
      
    } catch (err) {
      console.error('❌ Error al obtener tours:', err)
      error.value = err.message
      tours.value = []
    } finally {
      loading.value = false
    }
  }

  // Funciones helper para manejar estados de tours
  const obtenerEstadoInfo = (estado, cuposDisponibles = 0) => {
    const estadoUpper = estado?.toUpperCase()
    
    switch (estadoUpper) {
      case 'DISPONIBLE':
        if (cuposDisponibles === 0) {
          return {
            texto: 'AGOTADO',
            color: 'bg-red-600 text-white',
            descripcion: 'Sin cupos disponibles'
          }
        }
        return {
          texto: 'DISPONIBLE',
          color: 'bg-yellow-500 text-black',
          descripcion: 'Cupos disponibles para reservar'
        }
      case 'AGOTADO':
        return {
          texto: 'AGOTADO',
          color: 'bg-red-600 text-white',
          descripcion: 'Sin cupos disponibles'
        }
      case 'EN_CURSO':
        return {
          texto: 'EN CURSO',
          color: 'bg-blue-600 text-white',
          descripcion: 'Tour en progreso'
        }
      case 'COMPLETADO':
        return {
          texto: 'COMPLETADO',
          color: 'bg-gray-600 text-white',
          descripcion: 'Tour finalizado'
        }
      case 'CANCELADO':
        return {
          texto: 'CANCELADO',
          color: 'bg-red-800 text-white',
          descripcion: 'Tour cancelado'
        }
      case 'SUSPENDIDO':
        return {
          texto: 'SUSPENDIDO',
          color: 'bg-orange-600 text-white',
          descripcion: 'Tour temporalmente pausado'
        }
      case 'REPROGRAMADO':
        return {
          texto: 'REPROGRAMADO',
          color: 'bg-purple-600 text-white',
          descripcion: 'Tour reprogramado para nueva fecha'
        }
      default:
        return {
          texto: 'DESCONOCIDO',
          color: 'bg-gray-400 text-white',
          descripcion: 'Estado no definido'
        }
    }
  }

  // Función para verificar si un tour está disponible para reserva
  const esTourReservable = (tour) => {
    const estadoUpper = tour.estado?.toUpperCase()
    const estadosReservables = ['DISPONIBLE']
    
    return estadosReservables.includes(estadoUpper) && tour.cupos_disponibles > 0
  }

  // Función para obtener el texto del botón según el estado
  const obtenerTextoBoton = (tour) => {
    const estadoUpper = tour.estado?.toUpperCase()
    
    if (tour.cupos_disponibles === 0 || estadoUpper === 'AGOTADO') {
      return 'Agotado'
    }
    
    switch (estadoUpper) {
      case 'DISPONIBLE':
        return 'Reservar'
      case 'EN_CURSO':
        return 'En Curso'
      case 'COMPLETADO':
        return 'Completado'
      case 'CANCELADO':
        return 'Cancelado'
      case 'SUSPENDIDO':
        return 'Suspendido'
      case 'REPROGRAMADO':
        return 'Reprogramado'
      default:
        return 'No Disponible'
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
    const ubicacionesUnicas = [...new Set(tours.value.map(tour => tour.punto_salida).filter(ubicacion => ubicacion))]
    
    return {
      totalDestinos: tours.value.length,
      totalPaises: ubicacionesUnicas.length,
      precioMinimo: precios.length > 0 ? Math.min(...precios) : 0,
      paisesUnicos: ubicacionesUnicas
    }
  })

  // Función para obtener la imagen actual del carrusel automático
  const obtenerImagenActual = (tour) => {
    if (!tour.imagenes || tour.imagenes.length === 0) {
      return 'https://via.placeholder.com/400x300/2563eb/ffffff?text=Sin+Imagen'
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
    detenerCarruselGaleria()
  }

  // Funciones para la galería de imágenes
  const verGaleria = (tour) => {
    if (tour.imagenes && tour.imagenes.length > 0) {
      selectedTourImages.value = tour.imagenes
      currentImageIndex.value = 0
      showImageDialog.value = true
      iniciarCarruselGaleria()
    }
  }

  const cerrarGaleria = () => {
    showImageDialog.value = false
    detenerCarruselGaleria()
  }

  const iniciarCarruselGaleria = () => {
    detenerCarruselGaleria()
    if (selectedTourImages.value.length > 1 && isGalleryAutoPlaying.value) {
      galleryIntervalId.value = setInterval(() => {
        siguienteImagen()
      }, 5000)
    }
  }

  const detenerCarruselGaleria = () => {
    if (galleryIntervalId.value) {
      clearInterval(galleryIntervalId.value)
      galleryIntervalId.value = null
    }
  }

  const toggleAutoPlay = () => {
    isGalleryAutoPlaying.value = !isGalleryAutoPlaying.value
    if (isGalleryAutoPlaying.value && selectedTourImages.value.length > 1) {
      iniciarCarruselGaleria()
    } else {
      detenerCarruselGaleria()
    }
  }

  const siguienteImagen = () => {
    currentImageIndex.value = currentImageIndex.value === selectedTourImages.value.length - 1 
      ? 0 
      : currentImageIndex.value + 1
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

  // Función para reservar tour
  const reservarTour = (tour) => {
    // Verificar si el tour está disponible para reserva
    if (!esTourReservable(tour)) {
      const estadoInfo = obtenerEstadoInfo(tour.estado, tour.cupos_disponibles)
      toast.add({
        severity: 'warn',
        summary: 'Tour No Disponible',
        detail: `Este tour está ${estadoInfo.texto.toLowerCase()}. ${estadoInfo.descripcion}`,
        life: 4000
      })
      return
    }
    
    tourSeleccionado.value = tour
    
    // Verificar si el usuario está logueado
    if (!user.value) {
      showAuthDialog.value = true
    } else {
      showReservaDialog.value = true
    }
  }

  // Función para verificar si hay una reserva pendiente después del login
  const verificarReservaPendiente = async () => {
    try {
      const reservaPendiente = sessionStorage.getItem('tour_reserva_pendiente')
      const sessionActiva = sessionStorage.getItem('reserva_session_activa')
      
      // Solo procesar si hay reserva pendiente Y la sesión está activa
      if (reservaPendiente && sessionActiva === 'true' && user.value && tours.value && Array.isArray(tours.value)) {
        const data = JSON.parse(reservaPendiente)
        
        // Buscar el tour en la lista actual
        const tour = tours.value.find(t => t && t.id === data.tourId)
        
        if (tour) {
          // Usar nextTick para asegurar que el componente esté completamente montado
          await nextTick()
          
          // Pequeño delay adicional para asegurar el rendering
          setTimeout(() => {
            // Abrir modal de reserva automáticamente
            tourSeleccionado.value = tour
            showReservaDialog.value = true
            
            // Mostrar mensaje informativo DESPUÉS de abrir el modal
            setTimeout(() => {
              toast.add({
                severity: 'success',
                summary: '🎯 Continuando con tu reserva',
                detail: `¡Perfecto! Ahora puedes completar la reserva para: ${tour.nombre}`,
                life: 6000
              })
            }, 500)
          }, 100)
          
          // Limpiar sessionStorage
          sessionStorage.removeItem('tour_reserva_pendiente')
          sessionStorage.removeItem('reserva_session_activa')
        } else {
          // Si no encontramos el tour, limpiar la información obsoleta
          sessionStorage.removeItem('tour_reserva_pendiente')
          sessionStorage.removeItem('reserva_session_activa')
        }
      } else {
        if (reservaPendiente && sessionActiva !== 'true') {
          // Si hay información de reserva pero no es de la sesión activa, limpiarla
          sessionStorage.removeItem('tour_reserva_pendiente')
          sessionStorage.removeItem('reserva_session_activa')
        }
      }
    } catch (error) {
      // Limpiar sessionStorage si hay errores
      sessionStorage.removeItem('tour_reserva_pendiente')
      sessionStorage.removeItem('reserva_session_activa')
    }
  }

  // Función para manejar la confirmación de reserva desde el componente hijo
  const manejarConfirmacionReserva = async (reserva) => {
    const cuposReservados = reserva.cupos_reservados || 0
    
    toast.add({ 
      severity: 'success', 
      summary: 'Reserva Confirmada', 
      detail: `Tu reserva de ${cuposReservados} cupo${cuposReservados > 1 ? 's' : ''} ha sido registrada. Te contactaremos pronto.`, 
      life: 5000 
    })

    // Cerrar modal
    showReservaDialog.value = false
    
    // Refrescar los datos de tours para obtener los cupos actualizados
    try {
      await obtenerTours() // Ahora usa la URL guardada
      console.log('Lista de tours actualizada después de la reserva')
      
      // Buscar el tour actualizado y mostrar información de cupos
      if (reserva.tour_id) {
        const tourActualizado = tours.value.find(t => t.id === reserva.tour_id)
        if (tourActualizado) {
          const cuposRestantes = tourActualizado.cupos_disponibles || 0
          if (cuposRestantes > 0) {
            setTimeout(() => {
              toast.add({
                severity: 'info',
                summary: 'Cupos Disponibles',
                detail: `Quedan ${cuposRestantes} cupo${cuposRestantes > 1 ? 's' : ''} disponible${cuposRestantes > 1 ? 's' : ''} para "${tourActualizado.nombre}".`,
                life: 4000
              })
            }, 1500)
          } else {
            setTimeout(() => {
              toast.add({
                severity: 'warn',
                summary: 'Tour Completo',
                detail: `"${tourActualizado.nombre}" ya no tiene cupos disponibles.`,
                life: 4000
              })
            }, 1500)
          }
        }
      }
    } catch (error) {
      console.error('Error al actualizar lista de tours:', error)
      
      // Fallback: actualizar cupos localmente si falla la API
      if (reserva.tour_id && reserva.cupos_reservados) {
        const tourIndex = tours.value.findIndex(t => t.id === reserva.tour_id)
        if (tourIndex !== -1 && tours.value[tourIndex].cupos_disponibles) {
          tours.value[tourIndex].cupos_disponibles = Math.max(0, 
            tours.value[tourIndex].cupos_disponibles - reserva.cupos_reservados
          )
          tours.value[tourIndex].cupos_reservados = (tours.value[tourIndex].cupos_reservados || 0) + reserva.cupos_reservados
        }
      }
    }
  }

  // Watch para verificar reserva pendiente cuando el usuario cambie
  watch(user, async (newUser) => {
    try {
      if (newUser && tours.value && tours.value.length > 0) {
        await verificarReservaPendiente()
      }
    } catch (error) {
      console.error('Error en watcher de usuario:', error)
    }
  }, { immediate: false })

  return {
    // Estados
    tours,
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
    intervalIds,
    user,
    toast,
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
    detenerCarrusel,
    detenerTodosLosCarruseles,
    verGaleria,
    cerrarGaleria,
    siguienteImagen,
    imagenAnterior,
    irAImagen,
    toggleAutoPlay
  }
}
