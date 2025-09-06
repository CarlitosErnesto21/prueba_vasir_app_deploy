<script setup>
import { computed } from 'vue'
// Props
const props = defineProps({
  resumen: {
    type: Array,
    default: () => []
  },
  filtroTipo: {
    type: String,
    default: 'tours'
  },
  loading: {
    type: Boolean,
    default: false
  }
})

// Emits
const emit = defineEmits(['verReservas'])

const verReservas = (nombre) => {
  emit('verReservas', props.filtroTipo, nombre)
}

// Computed para el header de la tabla
const headerNombre = computed(() => {
  switch (props.filtroTipo) {
    case 'tours':
      return 'Nombre del Tour'
    case 'hoteles':
      return 'Nombre del Hotel'
    case 'aerolineas':
      return 'Nombre de la Aerolínea'
    default:
      return 'Nombre'
  }
})
</script>

<template>
  <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200">
    <div class="px-4 py-3 bg-gray-50 border-b">
      <h4 class="text-lg font-semibold text-blue-600">
        Resumen de reservas pendientes por {{ filtroTipo }}
      </h4>
    </div>

    <div v-if="loading" class="p-8 text-center">
      <div class="inline-flex items-center">
        <i class="pi pi-spin pi-spinner text-2xl text-red-600 mr-3"></i>
        <span class="text-gray-600">Cargando reservas...</span>
      </div>
    </div>

    <div v-else-if="resumen.length === 0" class="p-8 text-center text-gray-500">
      <i class="pi pi-inbox text-4xl text-gray-300 block mb-2"></i>
      <span>No hay reservas pendientes en este rango de fechas</span>
    </div>

    <DataTable
      v-else
      :value="resumen"
      :rows="5"
      :paginator="resumen.length > 5"
      :rowsPerPageOptions="[5, 10, 15]"
      class="border-0"
      :pt="{
        table: { class: 'w-full' },
        header: { class: 'bg-gray-50 border-b' },
        tbody: { class: 'divide-y divide-gray-200' }
      }"
    >
      <Column 
        :field="'nombre'" 
        :header="headerNombre"
        :pt="{
          headerCell: { class: 'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider' },
          bodyCell: { class: 'px-6 py-4 text-sm font-medium text-gray-900' }
        }"
      />
      
      <Column 
        header="Cantidad de Reservas"
        :pt="{
          headerCell: { class: 'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider' },
          bodyCell: { class: 'px-6 py-4 text-sm text-gray-900' }
        }"
      >
        <template #body="slotProps">
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
            {{ slotProps.data.cantidad }} 
            {{ slotProps.data.cantidad === 1 ? 'reserva' : 'reservas' }}
          </span>
        </template>
      </Column>
      
      <Column 
        header="Acciones"
        :pt="{
          headerCell: { class: 'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider' },
          bodyCell: { class: 'px-6 py-4 text-sm text-gray-900' }
        }"
      >
        <template #body="slotProps">
          <button
            class="bg-blue-500 border border-blue-500 text-white shadow-md hover:shadow-lg rounded-md hover:-translate-y-1 transition-transform duration-300 px-3 py-2 flex items-center gap-2 text-sm"
            @click="verReservas(slotProps.data.nombre)"
          >
            <i class="pi pi-eye"></i>
            Ver reservas
          </button>
        </template>
      </Column>
    </DataTable>
  </div>
</template>
