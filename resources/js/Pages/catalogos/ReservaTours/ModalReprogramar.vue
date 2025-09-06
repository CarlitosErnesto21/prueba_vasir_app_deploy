<script setup>
import { ref, reactive, computed, watch } from 'vue'

// Props
const props = defineProps({
  visible: {
    type: Boolean,
    default: false
  },
  reserva: {
    type: Object,
    default: () => null
  }
})

// Emits
const emit = defineEmits([
  'update:visible',
  'confirmarReprogramacion'
])

// Estado del formulario
const formulario = reactive({
  fecha_nueva: null,
  motivo: '',
  observaciones: ''
})

// Estados de validación
const errores = ref({})
const procesando = ref(false)

// Motivos predefinidos
const motivosComunes = [
  'Cancelación del tour por parte de la agencia',
  'Solicitud del cliente',
  'Condiciones climáticas adversas',
  'Problemas de transporte',
  'Otro motivo'
]

// Watch para resetear formulario cuando cambie la reserva
watch(() => props.reserva, (nuevaReserva) => {
  if (nuevaReserva) {
    formulario.fecha_nueva = null
    formulario.motivo = ''
    formulario.observaciones = ''
    errores.value = {}
  }
}, { deep: true })

// Computed para validaciones
const fechaMinima = computed(() => {
  const mañana = new Date()
  mañana.setDate(mañana.getDate() + 1)
  return mañana
})

const esValido = computed(() => {
  return formulario.fecha_nueva && 
         formulario.motivo && 
         Object.keys(errores.value).length === 0
})

// Función para validar el formulario
const validarFormulario = () => {
  const nuevosErrores = {}

  if (!formulario.fecha_nueva) {
    nuevosErrores.fecha_nueva = 'La nueva fecha es requerida'
  } else if (new Date(formulario.fecha_nueva) <= new Date()) {
    nuevosErrores.fecha_nueva = 'La fecha debe ser posterior a hoy'
  }

  if (!formulario.motivo) {
    nuevosErrores.motivo = 'El motivo es requerido'
  }

  if (formulario.motivo === 'Otro motivo' && !formulario.observaciones) {
    nuevosErrores.observaciones = 'Las observaciones son requeridas cuando se selecciona "Otro motivo"'
  }

  errores.value = nuevosErrores
  return Object.keys(nuevosErrores).length === 0
}

// Función para formatear fecha
const formatearFecha = (fecha) => {
  if (!fecha) return '-'
  const date = new Date(fecha)
  return date.toLocaleDateString('es-ES', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

// Función para formatear precio
const formatearPrecio = (precio) => {
  return new Intl.NumberFormat('es-ES', {
    style: 'currency',
    currency: 'USD'
  }).format(precio || 0)
}

// Función para cerrar modal
const cerrarModal = () => {
  formulario.fecha_nueva = null
  formulario.motivo = ''
  formulario.observaciones = ''
  errores.value = {}
  procesando.value = false
  emit('update:visible', false)
}

// Función para confirmar reprogramación
const confirmarReprogramacion = async () => {
  if (!validarFormulario()) return

  procesando.value = true
  
  try {
    const datosReprogramacion = {
      reserva_id: props.reserva.id,
      fecha_nueva: formulario.fecha_nueva,
      motivo: formulario.motivo,
      observaciones: formulario.observaciones
    }

    emit('confirmarReprogramacion', datosReprogramacion)
    cerrarModal()
  } catch (error) {
    console.error('Error al reprogramar:', error)
    // El manejo de errores se hará en el componente padre
  } finally {
    procesando.value = false
  }
}
</script>

<template>
  <Dialog
    :visible="visible"
    @update:visible="cerrarModal"
    modal
    header="Reprogramar Reserva"
    :style="{ width: '600px' }"
    :draggable="false"
    position="center"
    :pt="{
      root: { class: 'rounded-lg border border-gray-200' },
      header: { class: 'bg-red-50 border-b px-6 py-4' },
      content: { class: 'p-0' },
      footer: { class: 'border-t px-6 py-4 bg-gray-50' }
    }"
  >
    <div class="p-6" v-if="reserva">
      <!-- Información de la reserva actual -->
      <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <h4 class="font-medium text-gray-900 mb-3">Información Actual</h4>
        <div class="grid grid-cols-2 gap-4 text-sm">
          <div>
            <span class="font-medium text-gray-600">Cliente:</span>
            <span class="block text-gray-900">{{ (reserva.cliente?.user?.name) || (reserva.cliente?.nombres) || '-' }}</span>
          </div>
          <div>
            <span class="font-medium text-gray-600">Fecha Actual:</span>
            <span class="block text-gray-900">{{ formatearFecha(reserva.fecha_reserva) }}</span>
          </div>
          <div>
            <span class="font-medium text-gray-600">Tour/Hotel:</span>
            <span class="block text-gray-900">{{ reserva.tour?.nombre || reserva.hotel?.nombre || '-' }}</span>
          </div>
          <div>
            <span class="font-medium text-gray-600">Precio Total:</span>
            <span class="block text-gray-900 font-medium">{{ formatearPrecio(reserva.precio_total) }}</span>
          </div>
        </div>
      </div>

      <!-- Formulario de reprogramación -->
      <div class="space-y-6">
        <!-- Nueva fecha -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Nueva Fecha *
          </label>
          <DatePicker
            v-model="formulario.fecha_nueva"
            :minDate="fechaMinima"
            placeholder="Seleccionar nueva fecha..."
            dateFormat="dd/mm/yy"
            class="w-full"
            showIcon
            :class="{ 'p-invalid': errores.fecha_nueva }"
          />
          <small v-if="errores.fecha_nueva" class="text-red-600 mt-1 block">
            {{ errores.fecha_nueva }}
          </small>
        </div>

        <!-- Motivo -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Motivo de la Reprogramación *
          </label>
          <Select
            v-model="formulario.motivo"
            :options="motivosComunes"
            placeholder="Seleccionar motivo..."
            class="w-full"
            :class="{ 'p-invalid': errores.motivo }"
          />
          <small v-if="errores.motivo" class="text-red-600 mt-1 block">
            {{ errores.motivo }}
          </small>
        </div>

        <!-- Observaciones -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Observaciones
            <span v-if="formulario.motivo === 'Otro motivo'" class="text-red-500">*</span>
          </label>
          <Textarea
            v-model="formulario.observaciones"
            placeholder="Detalles adicionales sobre la reprogramación..."
            rows="3"
            class="w-full"
            :class="{ 'p-invalid': errores.observaciones }"
          />
          <small v-if="errores.observaciones" class="text-red-600 mt-1 block">
            {{ errores.observaciones }}
          </small>
          <small v-else class="text-gray-500 mt-1 block">
            Información adicional que será enviada al cliente
          </small>
        </div>

        <!-- Advertencia -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
          <div class="flex">
            <i class="pi pi-exclamation-triangle text-yellow-400 mr-2 mt-0.5"></i>
            <div class="text-sm text-yellow-800">
              <strong>Importante:</strong> La reprogramación será notificada automáticamente al cliente por correo electrónico.
              Asegúrate de que la nueva fecha esté disponible antes de confirmar.
            </div>
          </div>
        </div>
      </div>
    </div>

    <template #footer>
      <div class="flex justify-between">
        <button
          class="bg-gray-500 border border-gray-500 text-white shadow-md hover:shadow-lg rounded-md hover:-translate-y-1 transition-transform duration-300 px-4 py-2 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
          @click="cerrarModal"
          :disabled="procesando"
        >
          <i class="pi pi-times"></i>
          Cancelar
        </button>
        <button
          class="bg-yellow-500 border border-yellow-500 text-white shadow-md hover:shadow-lg rounded-md hover:-translate-y-1 transition-transform duration-300 px-4 py-2 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
          @click="confirmarReprogramacion"
          :disabled="!esValido || procesando"
        >
          <i class="pi pi-calendar" :class="{ 'animate-spin': procesando }"></i>
          <span v-if="procesando">Procesando...</span>
          <span v-else>Confirmar Reprogramación</span>
        </button>
      </div>
    </template>
  </Dialog>
</template>
