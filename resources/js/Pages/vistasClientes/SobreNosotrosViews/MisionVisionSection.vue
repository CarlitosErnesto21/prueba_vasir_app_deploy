<script setup>
const props = defineProps({
    siteSettings: {
        type: Object,
        default: () => ({})
    }
});
</script>

<template>
  <!-- Misión y Visión -->
  <div v-if="siteSettings.mission || siteSettings.vision" class="grid grid-cols-2 gap-3 sm:gap-4 md:gap-6 lg:gap-8 mb-8 sm:mb-12 px-2 sm:px-4 md:px-0">
    <div v-if="siteSettings.mission" class="border border-gray-300 bg-white shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 rounded-xl p-6">
      <div class="flex items-center mb-2 sm:mb-3 md:mb-4">
        <span class="text-xl sm:text-2xl md:text-3xl mr-1 sm:mr-2 md:mr-3">🎯</span>
        <h2 class="text-sm sm:text-lg md:text-xl lg:text-2xl font-bold text-red-700">Nuestra Misión</h2>
      </div>
      <p class="text-xs sm:text-sm md:text-base lg:text-lg text-gray-700 leading-relaxed">
        {{ siteSettings.mission }}
      </p>
    </div>

    <div v-if="siteSettings.vision" class="border border-gray-300 bg-white shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 rounded-xl p-6">
      <div class="flex items-center mb-2 sm:mb-3 md:mb-4">
        <span class="text-xl sm:text-2xl md:text-3xl mr-1 sm:mr-2 md:mr-3">🌟</span>
        <h2 class="text-sm sm:text-lg md:text-xl lg:text-2xl font-bold text-red-700">Nuestra Visión</h2>
      </div>
      <p class="text-xs sm:text-sm md:text-base lg:text-lg text-gray-700 leading-relaxed">
        {{ siteSettings.vision }}
      </p>
    </div>
  </div>

  <!-- Mensaje cuando no hay contenido configurado -->
  <div v-if="!siteSettings.mission && !siteSettings.vision" class="text-center py-8 sm:py-12 mb-8 sm:mb-12 px-4">
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 sm:p-6 md:p-8 max-w-2xl mx-auto">
      <div class="text-4xl sm:text-5xl md:text-6xl mb-3 sm:mb-4">⚙️</div>
      <h3 class="text-lg sm:text-xl font-semibold text-yellow-800 mb-2">Contenido en configuración</h3>
      <p class="text-sm sm:text-base text-yellow-700">
        La misión y visión de la empresa están siendo configuradas por nuestro equipo.
        <br>
        <strong>Administradores:</strong> Pueden configurar este contenido desde la sección "Configuración" del panel de administración.
      </p>
    </div>
  </div>
</template>
