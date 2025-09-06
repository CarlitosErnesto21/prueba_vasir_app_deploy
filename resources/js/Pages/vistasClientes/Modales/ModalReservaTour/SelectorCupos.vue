<script setup>
import { computed } from 'vue'

// Props del componente
const props = defineProps({
  cuposAdultos: {
    type: Number,
    default: 1
  },
  cuposMenores: {
    type: Number,
    default: 0
  },
  tourSeleccionado: {
    type: Object,
    default: null
  }
})

// Emits para comunicación con el componente padre
const emit = defineEmits(['update:cuposAdultos', 'update:cuposMenores'])

// Computed para el total de cupos
const cupos_total = computed(() => {
  const adultos = Number(props.cuposAdultos) || 0
  const menores = Number(props.cuposMenores) || 0
  return adultos + menores
})

// Funciones para incrementar/decrementar cupos
const incrementAdultos = () => {
  if (props.cuposAdultos < 20) {
    emit('update:cuposAdultos', props.cuposAdultos + 1)
  }
}

const decrementAdultos = () => {
  if (props.cuposAdultos > 1) {
    emit('update:cuposAdultos', props.cuposAdultos - 1)
  }
}

const incrementMenores = () => {
  if (props.cuposMenores < 20) {
    emit('update:cuposMenores', props.cuposMenores + 1)
  }
}

const decrementMenores = () => {
  if (props.cuposMenores > 0) {
    emit('update:cuposMenores', props.cuposMenores - 1)
  }
}
</script>

<template>
  <div>
    <h4 class="font-semibold text-gray-800 mb-2 sm:mb-3 flex flex-col sm:flex-row sm:items-center justify-between text-sm sm:text-base">
      <span class="flex items-center mb-1 sm:mb-0">
        <span class="text-base sm:text-lg mr-1 sm:mr-2">👥</span>
        <span class="hidden sm:inline">Cupos a reservar</span>
        <span class="sm:hidden">Cupos</span>
      </span>
      <span class="bg-red-100 text-red-700 px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-bold self-start sm:self-auto">
        Total: {{ cupos_total }}
      </span>
    </h4>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 bg-gray-50 p-3 sm:p-4 rounded-lg">
      <div>
        <label class="flex items-center text-xs sm:text-sm font-semibold mb-1 sm:mb-2 text-gray-700">
          <span class="mr-1 sm:mr-2">🧑</span>
          <span class="hidden sm:inline">Mayores de edad</span>
          <span class="sm:hidden">Adultos</span>
        </label>
        <!-- Botones personalizados para todas las pantallas -->
        <div class="flex justify-center gap-2 sm:gap-3">
          <button 
            type="button"
            @click="decrementAdultos"
            :disabled="cuposAdultos <= 1"
            class="bg-red-600 hover:bg-red-700 disabled:bg-gray-300 text-white w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center transition-colors font-bold text-sm sm:text-base"
          >
            -
          </button>
          <span class="flex items-center px-3 sm:px-4 py-2 sm:py-2.5 bg-white border border-gray-300 rounded-lg text-sm sm:text-base font-medium min-w-[3rem] sm:min-w-[4rem] justify-center">
            {{ cuposAdultos }}
          </span>
          <button 
            type="button"
            @click="incrementAdultos"
            :disabled="cuposAdultos >= 20"
            class="bg-red-600 hover:bg-red-700 disabled:bg-gray-300 text-white w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center transition-colors font-bold text-sm sm:text-base"
          >
            +
          </button>
        </div>
      </div>
      <div>
        <label class="flex items-center text-xs sm:text-sm font-semibold mb-1 sm:mb-2 text-gray-700">
          <span class="mr-1 sm:mr-2">🧒</span>
          <span class="hidden sm:inline">Menores de edad</span>
          <span class="sm:hidden">Niños</span>
        </label>
        <!-- Botones personalizados para todas las pantallas -->
        <div class="flex justify-center gap-2 sm:gap-3">
          <button 
            type="button"
            @click="decrementMenores"
            :disabled="cuposMenores <= 0"
            class="bg-red-600 hover:bg-red-700 disabled:bg-gray-300 text-white w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center transition-colors font-bold text-sm sm:text-base"
          >
            -
          </button>
          <span class="flex items-center px-3 sm:px-4 py-2 sm:py-2.5 bg-white border border-gray-300 rounded-lg text-sm sm:text-base font-medium min-w-[3rem] sm:min-w-[4rem] justify-center">
            {{ cuposMenores }}
          </span>
          <button 
            type="button"
            @click="incrementMenores"
            :disabled="cuposMenores >= 20"
            class="bg-red-600 hover:bg-red-700 disabled:bg-gray-300 text-white w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center transition-colors font-bold text-sm sm:text-base"
          >
            +
          </button>
        </div>
        <p class="text-xs text-amber-600 mt-1 sm:mt-2 flex items-center justify-center sm:justify-start">
          <span class="mr-1">⚠️</span>
          <span class="hidden sm:inline">Presentar permiso firmado de padre/madre</span>
          <span class="sm:hidden">Requiere permiso de los padres</span>
        </p>
      </div>
    </div>

    <!-- Resumen de precios -->
    <div v-if="tourSeleccionado" class="bg-gradient-to-r from-red-50 to-red-100 border border-red-200 p-4 sm:p-5 rounded-xl mt-4 sm:mt-5 shadow-sm">
      <h5 class="font-bold text-red-800 mb-3 sm:mb-4 flex items-center text-sm sm:text-base">
        <span class="mr-2 text-lg">💰</span>
        <span>Resumen de reserva</span>
      </h5>
      
      <div class="bg-white rounded-lg p-3 sm:p-4 shadow-sm">
        <div class="space-y-3">
          <!-- Información del tour -->
          <div class="flex justify-between items-start">
            <div class="flex-1">
              <p class="font-semibold text-gray-800 text-sm sm:text-base">{{ tourSeleccionado.nombre }}</p>
              <p class="text-gray-600 text-xs sm:text-sm">Precio por persona: ${{ (tourSeleccionado.precio || tourSeleccionado.precio_adulto || 0).toLocaleString() }}</p>
            </div>
          </div>
          
          <hr class="border-gray-200">
          
          <!-- Total de cupos -->
          <div class="flex justify-between items-center">
            <span class="text-gray-700 font-medium text-sm sm:text-base">Total de cupos:</span>
            <span class="font-bold text-gray-800 text-sm sm:text-base">{{ cupos_total }}</span>
          </div>
          
          <!-- Total a pagar -->
          <div class="bg-red-50 border border-red-200 rounded-lg p-3 mt-3">
            <div class="flex justify-between items-center">
              <span class="text-red-800 font-bold text-base sm:text-lg">Total a pagar:</span>
              <span class="text-red-800 font-bold text-xl sm:text-2xl">${{ ((tourSeleccionado.precio || tourSeleccionado.precio_adulto || 0) * cupos_total).toLocaleString() }}</span>
            </div>
            <p class="text-red-600 text-xs sm:text-sm mt-1 opacity-80">
              {{ cupos_total }} {{ cupos_total === 1 ? 'cupo' : 'cupos' }} × ${{ (tourSeleccionado.precio || tourSeleccionado.precio_adulto || 0).toLocaleString() }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
