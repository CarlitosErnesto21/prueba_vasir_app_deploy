<script setup>
import { ref, computed, watch, watchEffect, reactive, onMounted } from 'vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faXmark, faCheck } from '@fortawesome/free-solid-svg-icons'
import axios from 'axios'

// Importar componentes modulares
import ResumenTour from './ModalReservaTour/ResumenTour.vue'
import FormularioDatosPersonales from './ModalReservaTour/FormularioDatosPersonales.vue'
import SelectorCupos from './ModalReservaTour/SelectorCupos.vue'

// Props del componente
const props = defineProps({
  visible: {
    type: Boolean,
    default: false
  },
  tourSeleccionado: {
    type: Object,
    default: null
  },
  user: {
    type: Object,
    default: null
  }
})

// Emits para comunicación con el componente padre
const emit = defineEmits(['update:visible', 'confirmar-reserva'])

// Referencias a componentes hijos
const formularioDatosRef = ref(null)

// Estado local del formulario
const reservaForm = ref({
  correo: '',
  nombres: '',
  tipo_documento: null,
  tipo_documento_id: null, // ID del tipo de documento para precarga
  numero_identificacion: '',
  fecha_nacimiento: '',
  genero: '', // Vacío por defecto
  direccion: '',
  telefono: '',
  cupos_adultos: 1,
  cupos_menores: 0
})

// Estado para controlar si hay datos precargados del cliente
const tieneClienteExistente = ref(false)

// Estado para controlar el loading de confirmación de reserva
const confirmandoReserva = ref(false)

// Computed para calcular el precio total
const precioTotal = computed(() => {
  if (!props.tourSeleccionado?.precio) return 0
  
  const cuposTotales = (reservaForm.value.cupos_adultos || 0) + (reservaForm.value.cupos_menores || 0)
  return cuposTotales * parseFloat(props.tourSeleccionado.precio)
})

// Función para cargar datos del cliente existente
const cargarDatosCliente = async () => {
  if (!props.user) return null

  try {
    const response = await fetch('/api/cliente-datos', {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        'X-Requested-With': 'XMLHttpRequest'
      },
      credentials: 'same-origin'
    })

    if (response.ok) {
      const data = await response.json()
      return data.cliente || null
    } else if (response.status === 404) {
      return null
    } else {
      console.error('Error al cargar datos del cliente:', response.status)
      return null
    }
  } catch (error) {
    console.error('Error al cargar datos del cliente:', error)
    return null
  }
}

// Función para resetear el formulario
const resetFormularioReserva = async () => {
  const loggedInUser = props.user
  let nombreCompleto = ''
  let correo = ''

  if (loggedInUser) {
    correo = loggedInUser.email || ''
    nombreCompleto = loggedInUser.name || ''
  }

  // Inicializar con datos básicos del usuario
  reservaForm.value = {
    correo: correo,
    nombres: nombreCompleto,
    tipo_documento: null,
    tipo_documento_id: null,
    numero_identificacion: '',
    fecha_nacimiento: '',
    genero: '',
    direccion: '',
    telefono: '',
    cupos_adultos: 1,
    cupos_menores: 0,
  }

  // Resetear estado de cliente existente
  tieneClienteExistente.value = false

  // Cargar datos existentes del cliente si está logueado
  if (loggedInUser) {
    const clienteExistente = await cargarDatosCliente()
    
    if (clienteExistente) {
      // Marcar que hay datos precargados
      tieneClienteExistente.value = true

      // Formatear fecha de nacimiento para el DatePicker
      let fechaNacimientoFormateada = ''
      if (clienteExistente.fecha_nacimiento) {
        try {
          fechaNacimientoFormateada = new Date(clienteExistente.fecha_nacimiento)
        } catch (error) {
          console.warn('Error al formatear fecha de nacimiento:', error)
        }
      }

      // Actualizar el formulario con los datos existentes
      reservaForm.value = {
        ...reservaForm.value,
        tipo_documento_id: clienteExistente.tipo_documento_id, // Pasar el ID para que el componente hijo lo use
        numero_identificacion: clienteExistente.numero_identificacion || '',
        fecha_nacimiento: fechaNacimientoFormateada,
        genero: clienteExistente.genero || '',
        direccion: clienteExistente.direccion || '',
        telefono: clienteExistente.telefono || '',
      }
    }
  }
}

// Función para cerrar el modal
const cerrarModal = () => {
  emit('update:visible', false)
}

// Función para confirmar la reserva
const confirmarReserva = async () => {
  // Validar formulario usando el componente hijo
  if (formularioDatosRef.value?.validateForm) {
    if (!formularioDatosRef.value.validateForm()) {
      return
    }
  }

  if (reservaForm.value.cupos_adultos <= 0 && reservaForm.value.cupos_menores <= 0) {
    alert('Debe seleccionar al menos un cupo')
    return
  }

  confirmandoReserva.value = true

  try {
    const requestData = {
      tour_id: props.tourSeleccionado.id,
      cliente_data: {
        correo: reservaForm.value.correo,
        nombres: reservaForm.value.nombres,
        tipo_documento_id: reservaForm.value.tipo_documento_id || reservaForm.value.tipo_documento,
        numero_identificacion: reservaForm.value.numero_identificacion,
        fecha_nacimiento: reservaForm.value.fecha_nacimiento,
        genero: reservaForm.value.genero,
        direccion: reservaForm.value.direccion,
        telefono: reservaForm.value.telefono
      },
      cupos_adultos: reservaForm.value.cupos_adultos,
      cupos_menores: reservaForm.value.cupos_menores,
      precio_total: precioTotal.value
    }
    
    console.log('Datos que se envían:', requestData)
    
    const response = await axios.post('/reservas/tour', requestData)
    
    // Emitir información de la reserva incluyendo los cupos reservados
    const reservaData = {
      ...response.data,
      tour_id: props.tourSeleccionado.id,
      cupos_reservados: requestData.cupos_adultos + requestData.cupos_menores
    }
    
    emit('confirmar-reserva', reservaData)
    cerrarModal()
  } catch (error) {
    console.error('Error al confirmar reserva:', error)
    console.error('Error response:', error.response?.data)
    
    if (error.response?.status === 419) {
      alert('Su sesión ha expirado. Por favor, recargue la página.')
      window.location.reload()
    } else if (error.response?.status === 422) {
      // Error de validación
      const errors = error.response.data.errors || {}
      const message = error.response.data.message || 'Error de validación'
      
      console.error('Errores de validación:', errors)
      console.error('Mensaje del servidor:', message)
      
      // Mostrar errores específicos
      let errorMessage = message + '\n\nDetalles:\n'
      for (const [field, fieldErrors] of Object.entries(errors)) {
        errorMessage += `• ${field}: ${fieldErrors.join(', ')}\n`
      }
      
      alert(errorMessage)
    } else {
      const message = error.response?.data?.message || 'Error al procesar la reserva. Inténtelo de nuevo.'
      alert(message)
    }
  } finally {
    confirmandoReserva.value = false
  }
}

// Watch para resetear el formulario cuando se abre el modal  
watch(() => props.visible, async (newValue) => {
  if (newValue) {
    await resetFormularioReserva()
  }
})
</script>

<template>
  <Dialog 
    :visible="visible" 
    @update:visible="emit('update:visible', $event)"
    modal 
    :closable="false" 
    class="w-[95vw] sm:w-[90vw] md:w-[85vw] lg:w-[80vw] xl:max-w-4xl mx-2 sm:mx-4" 
    :draggable="false"
  >
    <template #header>
      <h3 class="text-sm sm:text-base lg:text-lg font-bold text-red-700 flex items-center">
        <span class="mr-1 sm:mr-2">🧾</span>
        <span class="hidden sm:inline">Reservando Tour</span>
        <span class="sm:hidden">Reserva</span>
      </h3>
    </template>

    <div v-if="tourSeleccionado" class="space-y-3 sm:space-y-4 lg:space-y-6 text-xs sm:text-sm text-gray-700">
      <!-- Resumen del tour usando componente modular -->
      <ResumenTour :tour-seleccionado="tourSeleccionado" />

      <!-- Formulario de datos personales usando componente modular -->
      <FormularioDatosPersonales 
        ref="formularioDatosRef"
        v-model:formulario="reservaForm"
        :tiene-cliente-existente="tieneClienteExistente"
        :user="user"
      />

      <!-- Selector de cupos usando componente modular -->
      <SelectorCupos 
        v-model:cupos-adultos="reservaForm.cupos_adultos"
        v-model:cupos-menores="reservaForm.cupos_menores"
        :cupos-disponibles="tourSeleccionado?.cupos_disponibles || 20"
      />
    </div>

    <template #footer>
      <div class="flex flex-row gap-3 sm:gap-4 pt-4 sm:pt-6 px-1 sm:px-0">
        <button 
          @click="cerrarModal" 
          type="button"
          class="bg-white hover:bg-red-50 text-red-600 border border-red-600 flex-1 sm:flex-none sm:px-6 px-3 py-3 sm:py-2 text-sm sm:text-base font-medium rounded-lg transition-all duration-200 ease-in-out flex items-center justify-center gap-2"
        >
          <FontAwesomeIcon :icon="faXmark" class="h-4 w-4 text-red-600" />
          <span class="hidden sm:inline">Cancelar</span>
          <span class="sm:hidden">Cancelar</span>
        </button>
        <button 
          @click="confirmarReserva" 
          type="button"
          :disabled="confirmandoReserva"
          :class="[
            'border border-red-600 flex-1 sm:flex-none sm:px-6 px-3 py-3 sm:py-2 text-sm sm:text-base font-semibold rounded-lg transition-all duration-200 ease-in-out flex items-center justify-center gap-2 shadow-lg hover:shadow-xl',
            confirmandoReserva 
              ? 'bg-gray-400 text-gray-600 border-gray-400 cursor-not-allowed' 
              : 'bg-red-600 hover:bg-red-700 text-white'
          ]"
        >
          <div v-if="confirmandoReserva" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
          <FontAwesomeIcon v-else :icon="faCheck" class="h-4 w-4 text-white" />
          <span class="hidden sm:inline">{{ confirmandoReserva ? 'Procesando...' : 'Confirmar Reserva' }}</span>
          <span class="sm:hidden">{{ confirmandoReserva ? 'Procesando...' : 'Confirmar' }}</span>
        </button>
      </div>
    </template>
  </Dialog>
</template>