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
  cuposDisponibles: {
    type: Number,
    default: 20
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

// Computed para verificar si se puede agregar más cupos
const puedeAgregarCupos = computed(() => {
  return cupos_total.value < props.cuposDisponibles
})

// Funciones para incrementar/decrementar cupos
const incrementAdultos = () => {
  if (puedeAgregarCupos.value) {
    emit('update:cuposAdultos', props.cuposAdultos + 1)
  }
}

const decrementAdultos = () => {
  if (props.cuposAdultos > 1) {
    emit('update:cuposAdultos', props.cuposAdultos - 1)
  }
}

const incrementMenores = () => {
  if (puedeAgregarCupos.value) {
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
    
    <!-- Información de cupos disponibles -->
    <div class="mb-3 p-2 bg-blue-50 border border-blue-200 rounded-lg">
      <p class="text-xs sm:text-sm text-blue-800">
        <span class="font-semibold">Cupos disponibles:</span> {{ cuposDisponibles }}
        <span v-if="cupos_total >= cuposDisponibles" class="ml-2 text-red-600 font-medium">
          (Máximo alcanzado)
        </span>
      </p>
    </div>
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
            :disabled="!puedeAgregarCupos"
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
            :disabled="!puedeAgregarCupos"
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
  </div>
</template>
