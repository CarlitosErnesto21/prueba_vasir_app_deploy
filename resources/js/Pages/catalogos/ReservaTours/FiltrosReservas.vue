<script setup>
import { computed } from 'vue'

// Props
const props = defineProps({
  filtroTipo: {
    type: String,
    default: 'tours'
  },
  filtroDesde: {
    type: [Date, null],
    default: null
  },
  filtroHasta: {
    type: [Date, null],
    default: null
  },
  tipoEstado: {
    type: String,
    default: 'pendientes'
  },
  busquedaNombre: {
    type: String,
    default: ''
  }
})

// Emits
const emit = defineEmits([
  'update:filtroTipo',
  'update:filtroDesde', 
  'update:filtroHasta',
  'update:tipoEstado',
  'update:busquedaNombre',
  'limpiarFechas',
  'mostrarHistorial'
])

// Computed para manejar v-model
const filtroTipoLocal = computed({
  get: () => props.filtroTipo,
  set: (value) => emit('update:filtroTipo', value)
})

const filtroDesdeLocal = computed({
  get: () => props.filtroDesde,
  set: (value) => emit('update:filtroDesde', value)
})

const filtroHastaLocal = computed({
  get: () => props.filtroHasta,
  set: (value) => emit('update:filtroHasta', value)
})

const tipoEstadoLocal = computed({
  get: () => props.tipoEstado,
  set: (value) => emit('update:tipoEstado', value)
})

const busquedaNombreLocal = computed({
  get: () => props.busquedaNombre,
  set: (value) => emit('update:busquedaNombre', value)
})

const limpiarFechas = () => {
  emit('limpiarFechas')
}

const mostrarHistorial = () => {
  emit('mostrarHistorial')
}
</script>

<template>
  <div class="space-y-4">
    <!-- Filtros superiores -->
    <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
      <div class="flex flex-col md:flex-row md:items-start md:gap-12">
        <!-- Filtro por tipo de reserva -->
        <div class="flex flex-col mb-4 md:mb-0 md:w-1/3">
          <span class="font-semibold text-base text-blue-600 mb-2">Filtrar por tipo de reserva</span>
          <div class="flex flex-row gap-6">
            <label class="inline-flex items-center cursor-pointer">
              <input 
                v-model="filtroTipoLocal" 
                type="radio" 
                value="tours" 
                name="tipo-reserva" 
                class="mr-2 text-red-600 focus:ring-red-500" 
              />
              <span class="text-sm font-medium text-gray-700">Tours</span>
            </label>
            <label class="inline-flex items-center cursor-pointer">
              <input 
                v-model="filtroTipoLocal" 
                type="radio" 
                value="hoteles" 
                name="tipo-reserva" 
                class="mr-2 text-red-600 focus:ring-red-500" 
              />
              <span class="text-sm font-medium text-gray-700">Hoteles</span>
            </label>
            <label class="inline-flex items-center cursor-pointer">
              <input 
                v-model="filtroTipoLocal" 
                type="radio" 
                value="aerolineas" 
                name="tipo-reserva" 
                class="mr-2 text-red-600 focus:ring-red-500" 
              />
              <span class="text-sm font-medium text-gray-700">Aerolíneas</span>
            </label>
          </div>
        </div>

        <!-- Filtro por fechas -->
        <div class="flex-1 flex flex-col md:w-2/3">
          <span class="font-semibold text-base text-blue-600 mb-1">Filtrar por rango de fechas</span>
          <div class="flex flex-col md:flex-row md:items-end gap-2">
            <div class="flex flex-col md:flex-row md:items-center w-full gap-2">
              <div class="flex flex-col w-full md:w-1/2">
                <label class="block font-semibold mb-1 text-sm text-gray-600">Desde:</label>
                <DatePicker 
                  v-model="filtroDesdeLocal" 
                  dateFormat="dd/mm/yy" 
                  class="w-full" 
                  showIcon 
                  placeholder="Seleccionar fecha"
                />
              </div>
              <div class="flex flex-col w-full md:w-1/2">
                <label class="block font-semibold mb-1 text-sm text-gray-600">Hasta:</label>
                <DatePicker 
                  v-model="filtroHastaLocal" 
                  dateFormat="dd/mm/yy" 
                  class="w-full" 
                  showIcon 
                  placeholder="Seleccionar fecha"
                />
              </div>
              <div class="flex items-end md:ml-4 mt-2 md:mt-0">
                <button 
                  class="bg-red-500 border border-red-500 text-white shadow-md hover:shadow-lg rounded-md hover:-translate-y-1 transition-transform duration-300 px-3 py-2 flex items-center gap-2 text-sm"
                  @click="limpiarFechas"
                >
                  <i class="pi pi-times"></i>
                  Limpiar fechas
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Controles de estado y búsqueda -->
    <div class="flex flex-col md:flex-row items-start md:items-center gap-4 bg-white p-4 rounded-lg shadow border border-gray-200">
      <div class="flex items-center gap-2">
        <label class="font-semibold text-blue-600 whitespace-nowrap">Ver reservas:</label>
        <Select
          v-model="tipoEstadoLocal"
          :options="[
            { label: 'Pendientes', value: 'pendientes' },
            { label: 'Confirmadas', value: 'confirmadas' },
            { label: 'Rechazadas', value: 'rechazadas' },
            { label: 'Reprogramadas', value: 'reprogramadas' }
          ]"
          optionLabel="label"
          optionValue="value"
          class="w-40"
          placeholder="Seleccionar estado"
        />
      </div>

      <div class="flex-1">
        <InputText
          v-model="busquedaNombreLocal"
          placeholder="Buscar por nombre de tour, hotel, aerolínea o cliente..."
          class="w-full"
          :pt="{
            root: { class: 'w-full' }
          }"
        />
      </div>

      <button
        class="bg-blue-500 border border-blue-500 text-white shadow-md hover:shadow-lg rounded-md hover:-translate-y-1 transition-transform duration-300 px-4 py-2 flex items-center gap-2"
        @click="mostrarHistorial"
      >
        <i class="pi pi-history"></i>
        Historial de Reservas
      </button>
    </div>
  </div>
</template>
