<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="relative w-[800px] h-[500px] bg-white rounded-xl shadow-lg overflow-hidden flex">
      <!-- Panel lateral animado -->
      <div
        class="absolute top-0 left-0 h-full w-1/2 flex flex-col items-center justify-center transition-all duration-700 z-20"
        :class="isRegister ? 'translate-x-full bg-gradient-to-br from-pink-400 to-red-400' : 'bg-gradient-to-br from-pink-400 to-red-400'"
      >
        <div class="text-white text-center px-8">
          <h2 class="text-3xl font-bold mb-2">
            {{ isRegister ? '¡Bienvenido!' : 'Hola!!!' }}
          </h2>
          <p class="mb-8">
            {{ isRegister ? 'Inicia sesión con tu cuenta' : 'Crear tu cuenta' }}
          </p>
          <button
            @click="props.toggleForm"
            class="border-2 border-white px-8 py-2 rounded-full text-white font-semibold hover:bg-white hover:text-pink-500 transition"
          >
            {{ props.isRegister ? 'INICIAR SESIÓN' : 'REGISTRAR' }}
          </button>
        </div>
      </div>
      <!-- Panel de formularios con ambos slots SIEMPRE presentes -->
      <div
        class="absolute top-0 left-0 w-full h-full flex transition-all duration-700"
        :class="isRegister ? '-translate-x-1/2' : ''"
      >
        <!-- Lado izquierdo (vacío solo para simetría visual) -->
        <div class="w-1/2 h-full"></div>
        <!-- Lado derecho: ambos formularios superpuestos -->
        <div class="relative w-1/2 h-full flex flex-col items-center justify-center bg-white px-10">
          <!-- Login -->
          <div
            class="absolute inset-0 flex flex-col items-center justify-center transition-all duration-700"
            :class="isRegister ? 'opacity-0 pointer-events-none z-0' : 'opacity-100 pointer-events-auto z-10'"
          >
            <slot name="login" />
          </div>
          <!-- Register -->
          <div
            class="absolute inset-0 flex flex-col items-center justify-center transition-all duration-700"
            :class="isRegister ? 'opacity-100 pointer-events-auto z-10' : 'opacity-0 pointer-events-none z-0'"
          >
            <slot name="register" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
const props = defineProps({
  isRegister: Boolean,
  toggleForm: Function,
});
</script>