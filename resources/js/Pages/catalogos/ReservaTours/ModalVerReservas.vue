<script setup>
import { ref, computed } from 'vue'

// Props
const props = defineProps({
  visible: {
    type: Boolean,
    default: false
  },
  reservas: {
    type: Array,
    default: () => []
  },
  titulo: {
    type: String,
    default: 'Reservas'
  },
  loading: {
    type: Boolean,
    default: false
  }
})

// Emits
const emit = defineEmits([
  'update:visible',
  'confirmarReserva',
  'rechazarReserva'
])

// Estado local para filtros del modal
const busquedaCliente = ref('')
const busquedaFecha = ref(null)

// Computed para filtrar reservas en el modal
const reservasFiltradas = computed(() => {
  let lista = props.reservas || []
  
  if (busquedaCliente.value) {
    const search = busquedaCliente.value.toLowerCase()
    lista = lista.filter(r => 
      (r.cliente?.user?.name?.toLowerCase().includes(search)) ||
      (r.cliente?.nombres?.toLowerCase().includes(search)) ||
      (r.cliente?.user?.email?.toLowerCase().includes(search)) ||
      (r.cliente?.correo?.toLowerCase().includes(search))
    )
  }
  
  if (busquedaFecha.value) {
    const fechaBusqueda = new Date(busquedaFecha.value).toISOString().split('T')[0]
    lista = lista.filter(r => {
      if (!r.fecha_reserva) return false
      const fechaReserva = new Date(r.fecha_reserva).toISOString().split('T')[0]
      return fechaReserva === fechaBusqueda
    })
  }
  
  return lista.sort((a, b) => new Date(b.fecha_reserva) - new Date(a.fecha_reserva))
})

// Función para limpiar filtros cuando se cierra el modal
const cerrarModal = () => {
  busquedaCliente.value = ''
  busquedaFecha.value = null
  emit('update:visible', false)
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

// Función para obtener clases del estado
const getEstadoClasses = (estado) => {
  const clases = {
    'Pendiente': 'bg-yellow-100 text-yellow-800',
    'Confirmado': 'bg-green-100 text-green-800',
    'Rechazada': 'bg-red-100 text-red-800',
    'Reprogramada': 'bg-blue-100 text-blue-800'
  }
  return clases[estado] || 'bg-gray-100 text-gray-800'
}
</script>

<template>
  <Dialog
    :visible="visible"
    @update:visible="cerrarModal"
    modal
    :header="titulo"
    :style="{ width: '95vw', maxWidth: '1200px' }"
    :draggable="false"
    position="center"
    :pt="{
      root: { class: 'rounded-lg border border-gray-200' },
      header: { class: 'bg-red-50 border-b px-6 py-4' },
      content: { class: 'p-0' },
      footer: { class: 'border-t px-6 py-4 bg-gray-50' }
    }"
  >
    <div class="p-6">
      <!-- Filtros del modal -->
      <div class="flex flex-col md:flex-row gap-4 mb-6">
        <div class="flex-1">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Buscar por cliente
          </label>
          <InputText
            v-model="busquedaCliente"
            placeholder="Nombre o correo del cliente..."
            class="w-full"
          />
        </div>
        <div class="flex-1">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Buscar por fecha
          </label>
          <DatePicker
            v-model="busquedaFecha"
            placeholder="Seleccionar fecha..."
            dateFormat="dd/mm/yy"
            class="w-full"
            showIcon
          />
        </div>
      </div>

      <!-- Loading state -->
      <div v-if="loading" class="p-8 text-center">
        <div class="inline-flex items-center">
          <i class="pi pi-spin pi-spinner text-2xl text-red-600 mr-3"></i>
          <span class="text-gray-600">Cargando reservas...</span>
        </div>
      </div>

      <!-- Empty state -->
      <div v-else-if="reservasFiltradas.length === 0" class="p-8 text-center text-gray-500">
        <i class="pi pi-inbox text-4xl text-gray-300 block mb-2"></i>
        <span>No hay reservas que coincidan con los filtros</span>
      </div>

      <!-- Tabla de reservas -->
      <div v-else class="overflow-hidden rounded-lg border border-gray-200">
        <DataTable
          :value="reservasFiltradas"
          :rows="10"
          :paginator="reservasFiltradas.length > 10"
          :rowsPerPageOptions="[10, 20, 30]"
          scrollable
          scrollHeight="400px"
          class="border-0"
          :pt="{
            table: { class: 'w-full' },
            header: { class: 'bg-gray-50' },
            tbody: { class: 'divide-y divide-gray-200' }
          }"
        >
          <Column 
            header="Cliente"
            :pt="{
              headerCell: { class: 'px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50' },
              bodyCell: { class: 'px-4 py-4 text-sm text-gray-900' }
            }"
          >
            <template #body="slotProps">
              <div class="flex flex-col">
                <span class="font-medium">{{ (slotProps.data.cliente?.user?.name) || (slotProps.data.cliente?.nombres) || '-' }}</span>
                <span class="text-xs text-gray-500">{{ (slotProps.data.cliente?.user?.email) || (slotProps.data.cliente?.correo) || '-' }}</span>
              </div>
            </template>
          </Column>
          
          <Column 
            header="Fecha"
            :pt="{
              headerCell: { class: 'px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50' },
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
              headerCell: { class: 'px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50' },
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
              headerCell: { class: 'px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50' },
              bodyCell: { class: 'px-4 py-4 text-sm font-medium text-gray-900' }
            }"
          >
            <template #body="slotProps">
              {{ formatearPrecio(slotProps.data.precio_total) }}
            </template>
          </Column>
          
          <Column 
            header="Estado"
            :pt="{
              headerCell: { class: 'px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50' },
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
            header="Acciones"
            :pt="{
              headerCell: { class: 'px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50' },
              bodyCell: { class: 'px-4 py-4 text-sm text-gray-900' }
            }"
          >
            <template #body="slotProps">
              <div class="flex gap-1" v-if="slotProps.data.estado === 'Pendiente'">
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
              </div>
            </template>
          </Column>
        </DataTable>
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
