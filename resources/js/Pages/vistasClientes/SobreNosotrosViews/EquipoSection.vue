<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const currentSlide = ref(0);
const carouselInterval = ref(null);

const equipo = ref([
  {
    nombre: 'Carlos Ernesto',
    cargo: 'Director General',
    descripcion: 'Especialista en turismo con más de 5 años de experiencia en el sector.',
    imagen: '/images/equipo/carlos.jpg'
  },
  {
    nombre: 'María González',
    cargo: 'Coordinadora de Tours',
    descripcion: 'Experta en planificación de itinerarios y atención al cliente.',
    imagen: '/images/equipo/maria.jpg'
  },
  {
    nombre: 'Roberto Martínez',
    cargo: 'Guía Turístico Senior',
    descripcion: 'Conocedor profundo de la historia y cultura salvadoreña.',
    imagen: '/images/equipo/roberto.jpg'
  }
]);

// Funciones del carrusel
function startCarousel() {
  carouselInterval.value = setInterval(() => {
    nextSlide()
  }, 4000) // Cambia cada 4 segundos
}

function stopCarousel() {
  if (carouselInterval.value) {
    clearInterval(carouselInterval.value)
    carouselInterval.value = null
  }
}

function nextSlide() {
  currentSlide.value = (currentSlide.value + 1) % equipo.value.length
}

function prevSlide() {
  currentSlide.value = currentSlide.value === 0 ? equipo.value.length - 1 : currentSlide.value - 1
}

function goToSlide(index) {
  currentSlide.value = index
}

onMounted(() => {
  startCarousel()
})

onUnmounted(() => {
  stopCarousel()
})
</script>

<template>
  <!-- Nuestro Equipo -->
  <div class="mb-8 sm:mb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="bg-gradient-to-r from-red-600 to-blue-600 text-white text-center py-4 sm:py-6 rounded-t-xl">
        <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold">👥 Conoce Nuestro Equipo</h2>
      </div>
    
    <!-- Carrusel del equipo -->
    <div class="bg-gradient-to-br from-gray-50 to-white p-6 sm:p-8 rounded-b-xl shadow-lg">
      <div class="relative max-w-4xl mx-auto" @mouseenter="stopCarousel" @mouseleave="startCarousel">
        <div class="overflow-hidden rounded-xl">
          <div 
            class="flex transition-transform duration-500 ease-in-out"
            :style="{ transform: `translateX(-${currentSlide * 100}%)` }"
          >
            <div
              v-for="miembro in equipo"
              :key="miembro.nombre"
              class="w-full flex-shrink-0 bg-gradient-to-br from-white to-gray-50 p-6 sm:p-8 md:p-10 shadow-lg border border-gray-200 text-center rounded-xl"
            >
              <div class="w-20 h-20 sm:w-24 sm:h-24 md:w-32 md:h-32 bg-gradient-to-br from-red-500 to-blue-500 rounded-full mx-auto mb-4 sm:mb-6 flex items-center justify-center shadow-lg">
                <span class="text-white text-xl sm:text-2xl md:text-3xl font-bold">{{ miembro.nombre.charAt(0) }}</span>
              </div>
              <h3 class="text-xl sm:text-2xl md:text-3xl font-bold bg-gradient-to-r from-red-600 to-blue-600 bg-clip-text text-transparent mb-2 sm:mb-3">{{ miembro.nombre }}</h3>
              <p class="text-base sm:text-lg md:text-xl text-red-500 font-semibold mb-3 sm:mb-4">{{ miembro.cargo }}</p>
              <p class="text-sm sm:text-base md:text-lg text-gray-700 leading-relaxed max-w-2xl mx-auto">{{ miembro.descripcion }}</p>
            </div>
          </div>
        </div>

        <!-- Botones de navegación -->
        <button
          @click="prevSlide"
          class="absolute left-2 sm:left-4 top-1/2 transform -translate-y-1/2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white p-2 sm:p-3 rounded-full shadow-lg transition-all duration-300 z-10"
        >
          <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
          </svg>
        </button>
        <button
          @click="nextSlide"
          class="absolute right-2 sm:right-4 top-1/2 transform -translate-y-1/2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white p-2 sm:p-3 rounded-full shadow-lg transition-all duration-300 z-10"
        >
          <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </button>

        <!-- Indicadores de slide -->
        <div class="flex justify-center mt-4 sm:mt-6 space-x-2">
          <button
            v-for="(miembro, index) in equipo"
            :key="index"
            @click="goToSlide(index)"
            class="w-3 h-3 rounded-full transition-all duration-300"
            :class="currentSlide === index ? 'bg-gradient-to-r from-red-600 to-blue-600' : 'bg-gray-300 hover:bg-gray-400'"
          ></button>
        </div>
      </div>
      </div>
    </div>
  </div>
</template>
