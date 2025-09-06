<script setup>
import { computed } from 'vue'

// Props del componente
const props = defineProps({
  tourSeleccionado: {
    type: Object,
    default: null
  }
})

// Función para formatear fechas
const formatearFecha = (fecha) => {
  if (!fecha) return ''
  const date = new Date(fecha)
  return date.toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Computed para datos de la tabla
const tourData = computed(() => {
  if (!props.tourSeleccionado) return []
  
  return [{
    id: 1,
    nombre: props.tourSeleccionado.nombre,
    incluye: props.tourSeleccionado.incluye || '---',
    no_incluye: props.tourSeleccionado.no_incluye || '---',
    punto_salida: props.tourSeleccionado.punto_salida,
    fecha_salida: formatearFecha(props.tourSeleccionado.fecha_salida),
    fecha_regreso: formatearFecha(props.tourSeleccionado.fecha_regreso),
    precio: props.tourSeleccionado.precio
  }]
})
</script>

<template>
  <div>
    <h4 class="font-semibold text-gray-800 mb-2 sm:mb-3 flex items-center text-sm sm:text-base">
      <span class="text-base sm:text-lg mr-1 sm:mr-2">🏖️</span>
      <span class="hidden sm:inline">Resumen del tour</span>
      <span class="sm:hidden">Tour</span>
    </h4>
    
    <DataTable 
      :value="tourData" 
      stripedRows 
      size="small"
      class="border border-gray-200 rounded-lg overflow-hidden text-xs sm:text-sm"
      :pt="{
        table: { class: 'min-w-full' },
        header: { class: 'bg-red-50 border-b border-gray-200' },
        headerRow: { class: 'bg-red-50' },
        headerCell: { class: 'p-1 sm:p-2 text-xs sm:text-sm font-semibold text-red-700 border-r border-gray-200 last:border-r-0' },
        bodyRow: { class: 'hover:bg-gray-50' },
        bodyCell: { class: 'p-1 sm:p-2 text-xs sm:text-sm border-r border-gray-200 last:border-r-0' }
      }"
      responsiveLayout="scroll"
    >
      <Column field="nombre" header="Tour" class="min-w-[80px]" />
      <Column field="incluye" header="Incluye" class="min-w-[60px]" />
      <Column field="no_incluye" header="No Incluye" class="min-w-[60px]" />
      <Column field="punto_salida" header="Salida" class="min-w-[60px]" />
      <Column field="fecha_salida" header="F. Salida" class="min-w-[80px]" />
      <Column field="fecha_regreso" header="F. Regreso" class="min-w-[80px]" />
      <Column field="precio" header="Precio" class="min-w-[60px]">
        <template #body="slotProps">
          <span class="font-bold text-green-600 text-xs sm:text-sm">${{ slotProps.data.precio }}</span>
        </template>
      </Column>
    </DataTable>
  </div>
</template>
