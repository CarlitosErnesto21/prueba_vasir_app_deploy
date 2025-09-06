<script setup>
import { computed } from 'vue'

// Props
const props = defineProps({
  reservas: {
    type: Array,
    default: () => []
  },
  filtroTipo: {
    type: String,
    default: 'tours'
  },
  tipoEstado: {
    type: String,
    default: 'pendientes'
  },
  loading: {
    type: Boolean,
    default: false
  }
})

// Emits
const emit = defineEmits([
  'confirmarReserva',
  'rechazarReserva', 
  'reprogramarReserva',
  'finalizarReserva'
])

// Computed para el título
const tituloTabla = computed(() => {
  const estado = {
    'pendientes': 'pendientes por confirmar',
    'confirmadas': 'confirmadas',
    'rechazadas': 'rechazadas', 
    'reprogramadas': 'reprogramadas'
  }
  return `Reservas ${estado[props.tipoEstado] || ''}`
})

// Función para obtener clases del estado
const getEstadoClasses = (estado) => {
  const clases = {
    'Pendiente': 'bg-yellow-100 text-yellow-800',
    'Confirmado': 'bg-green-100 text-green-800',
    'Rechazada': 'bg-red-100 text-red-800',
    'Reprogramada': 'bg-blue-100 text-blue-800',
    'Finalizada': 'bg-gray-100 text-gray-800'
  }
  return clases[estado] || 'bg-gray-100 text-gray-800'
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

// Computed para determinar si mostrar acciones
const mostrarAcciones = computed(() => {
  return props.tipoEstado !== 'rechazadas'
})
</script>

<template>
  <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200">
    <div class="px-4 py-3 bg-gray-50 border-b">
      <h4 class="text-lg font-semibold text-blue-600">
        {{ tituloTabla }}
      </h4>
    </div>

    <div v-if="loading" class="p-8 text-center">
      <div class="inline-flex items-center">
        <i class="pi pi-spin pi-spinner text-2xl text-red-600 mr-3"></i>
        <span class="text-gray-600">Cargando reservas...</span>
      </div>
    </div>

    <div v-else-if="reservas.length === 0" class="p-8 text-center text-gray-500">
      <i class="pi pi-inbox text-4xl text-gray-300 block mb-2"></i>
      <span>No hay reservas {{ tipoEstado }} en este rango de fechas</span>
    </div>

    <DataTable
      v-else
      :value="reservas"
      :rows="10"
      :paginator="reservas.length > 10"
      :rowsPerPageOptions="[10, 20, 50]"
      class="border-0"
      :pt="{
        table: { class: 'w-full' },
        header: { class: 'bg-gray-50 border-b' },
        tbody: { class: 'divide-y divide-gray-200' }
      }"
    >
      <Column 
        field="tipo" 
        header="Tipo"
        :pt="{
          headerCell: { class: 'px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider' },
          bodyCell: { class: 'px-4 py-4 text-sm text-gray-900' }
        }"
      >
        <template #body="slotProps">
          <span class="capitalize">{{ slotProps.data.tipo }}</span>
        </template>
      </Column>
      
      <Column 
        header="Nombre"
        :pt="{
          headerCell: { class: 'px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider' },
          bodyCell: { class: 'px-4 py-4 text-sm font-medium text-gray-900' }
        }"
      >
        <template #body="slotProps">
          {{ slotProps.data.tour?.nombre || slotProps.data.hotel?.nombre || slotProps.data.aerolinea?.nombre || '-' }}
        </template>
      </Column>
      
      <Column 
        header="Cliente"
        :pt="{
          headerCell: { class: 'px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider' },
          bodyCell: { class: 'px-4 py-4 text-sm text-gray-900' }
        }"
      >
        <template #body="slotProps">
          {{ (slotProps.data.cliente?.user?.name) || (slotProps.data.cliente?.nombres) || '-' }}
        </template>
      </Column>
      
      <Column 
        header="Fecha"
        :pt="{
          headerCell: { class: 'px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider' },
          bodyCell: { class: 'px-4 py-4 text-sm text-gray-900' }
        }"
      >
        <template #body="slotProps">
          {{ formatearFecha(slotProps.data.fecha_reserva) }}
        </template>
      </Column>
      
      <Column 
        header="Cupos"
        :pt="{
          headerCell: { class: 'px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider' },
          bodyCell: { class: 'px-4 py-4 text-sm text-gray-900' }
        }"
      >
        <template #body="slotProps">
          <div class="flex flex-col text-xs">
            <span v-if="slotProps.data.cupos_adultos">
              Adultos: {{ slotProps.data.cupos_adultos }}
            </span>
            <span v-if="slotProps.data.cupos_menores">
              Menores: {{ slotProps.data.cupos_menores }}
            </span>
          </div>
        </template>
      </Column>
      
      <Column 
        header="Precio Total"
        :pt="{
          headerCell: { class: 'px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider' },
          bodyCell: { class: 'px-4 py-4 text-sm font-medium text-gray-900' }
        }"
      >
        <template #body="slotProps">
          {{ formatearPrecio(slotProps.data.precio_total) }}
        </template>
      </Column>
      
      <Column 
        v-if="tipoEstado !== 'rechazadas'"
        header="Estado"
        :pt="{
          headerCell: { class: 'px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider' },
          bodyCell: { class: 'px-4 py-4 text-sm text-gray-900' }
        }"
      >
        <template #body="slotProps">
          <span 
            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
            :class="getEstadoClasses(slotProps.data.estado)"
          >
            {{ slotProps.data.estado }}
          </span>
        </template>
      </Column>
      
      <Column 
        v-if="mostrarAcciones"
        header="Acciones"
        :pt="{
          headerCell: { class: 'px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider' },
          bodyCell: { class: 'px-4 py-4 text-sm text-gray-900' }
        }"
      >
        <template #body="slotProps">
          <div class="flex gap-1 flex-wrap">
            <!-- Botones para reservas pendientes -->
            <template v-if="tipoEstado === 'pendientes' && slotProps.data.estado === 'Pendiente'">
              <button
                class="bg-green-500 border border-green-500 text-white shadow-md hover:shadow-lg rounded-md hover:-translate-y-1 transition-transform duration-300 p-2"
                @click="$emit('confirmarReserva', slotProps.data)"
                :title="'Confirmar'"
              >
                <i class="pi pi-check"></i>
              </button>
              <button
                class="bg-red-500 border border-red-500 text-white shadow-md hover:shadow-lg rounded-md hover:-translate-y-1 transition-transform duration-300 p-2"
                @click="$emit('rechazarReserva', slotProps.data)"
                :title="'Rechazar'"
              >
                <i class="pi pi-times"></i>
              </button>
            </template>

            <!-- Botones para reservas confirmadas -->
            <template v-if="tipoEstado === 'confirmadas'">
              <button
                class="bg-red-500 border border-red-500 text-white shadow-md hover:shadow-lg rounded-md hover:-translate-y-1 transition-transform duration-300 p-2"
                @click="$emit('rechazarReserva', slotProps.data)"
                :title="'Rechazar'"
              >
                <i class="pi pi-times"></i>
              </button>
              <button
                class="bg-blue-500 border border-blue-500 text-white shadow-md hover:shadow-lg rounded-md hover:-translate-y-1 transition-transform duration-300 p-2"
                @click="$emit('reprogramarReserva', slotProps.data)"
                :title="'Reprogramar'"
              >
                <i class="pi pi-refresh"></i>
              </button>
              <button
                class="bg-green-500 border border-green-500 text-white shadow-md hover:shadow-lg rounded-md hover:-translate-y-1 transition-transform duration-300 p-2"
                @click="$emit('finalizarReserva', slotProps.data)"
                :title="'Finalizar'"
              >
                <i class="pi pi-check-circle"></i>
              </button>
            </template>

            <!-- Botones para reservas reprogramadas -->
            <template v-if="tipoEstado === 'reprogramadas'">
              <button
                class="bg-red-500 border border-red-500 text-white shadow-md hover:shadow-lg rounded-md hover:-translate-y-1 transition-transform duration-300 p-2"
                @click="$emit('rechazarReserva', slotProps.data)"
                :title="'Rechazar'"
              >
                <i class="pi pi-times"></i>
              </button>
              <button
                class="bg-green-500 border border-green-500 text-white shadow-md hover:shadow-lg rounded-md hover:-translate-y-1 transition-transform duration-300 p-2"
                @click="$emit('finalizarReserva', slotProps.data)"
                :title="'Finalizar'"
              >
                <i class="pi pi-check-circle"></i>
              </button>
            </template>
          </div>
        </template>
      </Column>
    </DataTable>
  </div>
</template>
