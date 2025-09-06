<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
    <!-- Debug: Mostrar cantidad de tours -->
    <div class="col-span-full bg-yellow-100 p-4 rounded-lg">
      <p><strong>Debug:</strong> {{ tours.length }} tours recibidos</p>
      <pre v-if="tours.length > 0">{{ JSON.stringify(tours[0], null, 2) }}</pre>
    </div>
    
    <!-- Cards simplificadas -->
    <div 
      v-for="tour in tours" 
      :key="tour.id"
      class="bg-white rounded-lg shadow-lg p-4 border"
    >
      <h3 class="text-lg font-bold mb-2">{{ tour.nombre }}</h3>
      <p class="text-sm text-gray-600 mb-2">ID: {{ tour.id }}</p>
      <p class="text-sm text-gray-600 mb-2">Categoría: {{ tour.categoria }}</p>
      <p class="text-sm text-gray-600 mb-2">Precio: ${{ tour.precio }}</p>
      
      <!-- Imagen simple -->
      <div class="w-full h-32 bg-gray-200 rounded mb-2 flex items-center justify-center">
        <span class="text-gray-500">Imagen</span>
      </div>
      
      <!-- Botones simples -->
      <div class="flex gap-2">
        <button 
          class="bg-blue-500 text-white px-3 py-1 rounded text-sm"
          @click="() => console.log('Reservar:', tour)"
        >
          Reservar
        </button>
        <button 
          class="bg-gray-500 text-white px-3 py-1 rounded text-sm"
          @click="() => console.log('Ver:', tour)"
        >
          Ver
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { watch } from 'vue'

const props = defineProps({
  tours: {
    type: Array,
    required: true
  }
})

// Watcher para debuggear
watch(() => props.tours, (newTours) => {
  console.log('🟡 ToursGridSimple - Tours recibidos:', newTours)
}, { immediate: true })
</script>
