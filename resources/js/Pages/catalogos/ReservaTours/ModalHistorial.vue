<script setup>
import { ref, computed } from 'vue'

// Props
const props = defineProps({
  visible: {
    type: Boolean,
    default: false
  },
  reserva: {
    type: Object,
    default: () => null
  },
  historial: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  }
})

// Emits
const emit = defineEmits([
  'update:visible'
])

// Función para formatear fecha y hora
const formatearFechaHora = (fecha) => {
  if (!fecha) return '-'
  const date = new Date(fecha)
  return date.toLocaleString('es-ES', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Función para formatear precio
const formatearPrecio = (precio) => {
  return new Intl.NumberFormat('es-ES', {
    style: 'currency',
    currency: 'USD'
  }).format(precio || 0)
}

// Función para obtener el icono según el tipo de evento
const getIconoEvento = (tipo) => {
  const iconos = {
    'creacion': 'pi-plus-circle',
    'confirmacion': 'pi-check-circle',
    'rechazo': 'pi-times-circle',
    'reprogramacion': 'pi-calendar',
    'modificacion': 'pi-pencil',
    'cancelacion': 'pi-ban',
    'pago': 'pi-credit-card',
    'comentario': 'pi-comment'
  }
  return iconos[tipo] || 'pi-info-circle'
}

// Función para obtener el color según el tipo de evento
const getColorEvento = (tipo) => {
  const colores = {
    'creacion': 'text-blue-600 bg-blue-100',
    'confirmacion': 'text-green-600 bg-green-100',
    'rechazo': 'text-red-600 bg-red-100',
    'reprogramacion': 'text-yellow-600 bg-yellow-100',
    'modificacion': 'text-purple-600 bg-purple-100',
    'cancelacion': 'text-gray-600 bg-gray-100',
    'pago': 'text-emerald-600 bg-emerald-100',
    'comentario': 'text-indigo-600 bg-indigo-100'
  }
  return colores[tipo] || 'text-gray-600 bg-gray-100'
}

// Función para obtener el título del evento
const getTituloEvento = (evento) => {
  const titulos = {
    'creacion': 'Reserva Creada',
    'confirmacion': 'Reserva Confirmada',
    'rechazo': 'Reserva Rechazada',
    'reprogramacion': 'Reserva Reprogramada',
    'modificacion': 'Reserva Modificada',
    'cancelacion': 'Reserva Cancelada',
    'pago': 'Pago Registrado',
    'comentario': 'Comentario Agregado'
  }
  return titulos[evento.tipo] || 'Evento'
}

// Computed para ordenar historial por fecha
const historialOrdenado = computed(() => {
  return [...props.historial].sort((a, b) => new Date(b.fecha) - new Date(a.fecha))
})

// Función para cerrar modal
const cerrarModal = () => {
  emit('update:visible', false)
}
</script>

<template>
  <Dialog
    :visible="visible"
    @update:visible="cerrarModal"
    modal
    header="Historial de Reserva"
    :style="{ width: '800px' }"
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
      <!-- Información básica de la reserva -->
      <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <div class="flex justify-between items-start">
          <div>
            <h4 class="font-medium text-gray-900 mb-2">Reserva #{{ reserva.id }}</h4>
            <div class="grid grid-cols-2 gap-4 text-sm">
              <div>
                <span class="font-medium text-gray-600">Cliente:</span>
                <span class="block text-gray-900">{{ (reserva.cliente?.user?.name) || (reserva.cliente?.nombres) || '-' }}</span>
              </div>
              <div>
                <span class="font-medium text-gray-600">Estado Actual:</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ml-2"
                      :class="{
                        'bg-yellow-100 text-yellow-800': reserva.estado === 'Pendiente',
                        'bg-green-100 text-green-800': reserva.estado === 'Confirmado',
                        'bg-red-100 text-red-800': reserva.estado === 'Rechazada',
                        'bg-blue-100 text-blue-800': reserva.estado === 'Reprogramada'
                      }">
                  {{ reserva.estado }}
                </span>
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
        </div>
      </div>

      <!-- Loading state -->
      <div v-if="loading" class="p-8 text-center">
        <div class="inline-flex items-center">
          <i class="pi pi-spin pi-spinner text-2xl text-red-600 mr-3"></i>
          <span class="text-gray-600">Cargando historial...</span>
        </div>
      </div>

      <!-- Empty state -->
      <div v-else-if="historialOrdenado.length === 0" class="p-8 text-center text-gray-500">
        <i class="pi pi-clock text-4xl text-gray-300 block mb-2"></i>
        <span>No hay eventos en el historial</span>
      </div>

      <!-- Timeline del historial -->
      <div v-else class="space-y-4">
        <h5 class="font-medium text-gray-900 mb-4">Cronología de Eventos</h5>
        
        <div class="relative">
          <!-- Línea vertical -->
          <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-200"></div>
          
          <!-- Eventos -->
          <div v-for="(evento, index) in historialOrdenado" :key="index" class="relative flex items-start pb-6">
            <!-- Icono del evento -->
            <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center relative z-10"
                 :class="getColorEvento(evento.tipo)">
              <i :class="`pi ${getIconoEvento(evento.tipo)} text-lg`"></i>
            </div>
            
            <!-- Contenido del evento -->
            <div class="ml-4 flex-1 min-w-0">
              <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="flex justify-between items-start mb-2">
                  <h6 class="font-medium text-gray-900">{{ getTituloEvento(evento) }}</h6>
                  <span class="text-xs text-gray-500">{{ formatearFechaHora(evento.fecha) }}</span>
                </div>
                
                <!-- Descripción del evento -->
                <p v-if="evento.descripcion" class="text-sm text-gray-600 mb-2">
                  {{ evento.descripcion }}
                </p>
                
                <!-- Detalles específicos según el tipo -->
                <div v-if="evento.detalles" class="text-xs text-gray-500 space-y-1">
                  <div v-if="evento.tipo === 'reprogramacion' && evento.detalles.fecha_anterior">
                    <strong>Fecha anterior:</strong> {{ formatearFechaHora(evento.detalles.fecha_anterior) }}
                    <br>
                    <strong>Nueva fecha:</strong> {{ formatearFechaHora(evento.detalles.fecha_nueva) }}
                    <br v-if="evento.detalles.motivo">
                    <strong v-if="evento.detalles.motivo">Motivo:</strong> {{ evento.detalles.motivo }}
                  </div>
                  
                  <div v-else-if="evento.tipo === 'pago' && evento.detalles.monto">
                    <strong>Monto:</strong> {{ formatearPrecio(evento.detalles.monto) }}
                    <br v-if="evento.detalles.metodo">
                    <strong>Método:</strong> {{ evento.detalles.metodo }}
                  </div>
                  
                  <div v-else-if="evento.detalles.usuario">
                    <strong>Usuario:</strong> {{ evento.detalles.usuario }}
                  </div>
                </div>
                
                <!-- Usuario responsable -->
                <div v-if="evento.usuario" class="mt-2 text-xs text-gray-400">
                  Por: {{ evento.usuario }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <template #footer>
      <div class="flex justify-end">
        <button
          class="bg-gray-500 border border-gray-500 text-white shadow-md hover:shadow-lg rounded-md hover:-translate-y-1 transition-transform duration-300 px-4 py-2 flex items-center gap-2"
          @click="cerrarModal"
        >
          <i class="pi pi-times"></i>
          Cerrar
        </button>
      </div>
    </template>
  </Dialog>
</template>
