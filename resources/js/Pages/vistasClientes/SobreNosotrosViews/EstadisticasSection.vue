<script setup>
import { ref } from 'vue';

// Datos del equipo y estadísticas
const estadisticas = ref([
  { numero: '4+', descripcion: 'Años de experiencia', icono: '⏰' },
  { numero: '500+', descripcion: 'Clientes satisfechos', icono: '😊' },
  { numero: '50+', descripcion: 'Destinos visitados', icono: '📍' },
  { numero: '100%', descripcion: 'Compromiso y calidad', icono: '⭐' }
]);
</script>

<template>
  <!-- Estadísticas -->
  <div class="mb-8 sm:mb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-4 gap-2 sm:gap-3 md:gap-4 lg:gap-6">
        <div
          v-for="(stat, index) in estadisticas"
          :key="stat.descripcion"
          class="bg-gradient-to-br from-white to-gray-50 hover:from-red-50 hover:to-blue-50 rounded-lg sm:rounded-xl p-2 sm:p-3 md:p-4 lg:p-6 shadow-lg border-2 border-transparent hover:border-red-200 text-center transform hover:-translate-y-2 hover:scale-105 transition-all duration-300"
        >
          <div class="text-3xl sm:text-4xl md:text-5xl mb-2 sm:mb-3">{{ stat.icono }}</div>
          <h3 class="text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold bg-gradient-to-r from-red-600 to-blue-600 bg-clip-text text-transparent mb-1 sm:mb-2">{{ stat.numero }}</h3>
          <p class="text-xs sm:text-sm md:text-base text-gray-700 font-medium leading-tight">{{ stat.descripcion }}</p>
        </div>
      </div>
    </div>
  </div>
</template>
