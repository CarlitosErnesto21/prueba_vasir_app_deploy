<template>
  <!-- Modal de galería de imágenes -->
  <Dialog
    :visible="visible"
    modal
    header=""
    class="w-full max-w-6xl mx-4"
    :style="{ width: '90vw', maxHeight: '90vh' }"
    @update:visible="cerrarGaleria"
    @hide="cerrarGaleria"
  >
    <div class="relative flex flex-col items-center">
      <!-- Imagen principal -->
      <div class="relative w-full max-w-4xl">
        <img
          :src="imagenUrl"
          :alt="`Imagen ${currentImageIndex + 1}`"
          class="w-full h-auto max-h-[70vh] object-contain rounded-lg shadow-lg"
        />

        <!-- Botones de navegación -->
        <button
          v-if="imagenes.length > 1"
          @click="imagenAnterior"
          class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black/50 text-white p-3 rounded-full hover:bg-black/70 transition-all duration-200"
        >
          <i class="pi pi-chevron-left text-lg"></i>
        </button>

        <button
          v-if="imagenes.length > 1"
          @click="siguienteImagen"
          class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black/50 text-white p-3 rounded-full hover:bg-black/70 transition-all duration-200"
        >
          <i class="pi pi-chevron-right text-lg"></i>
        </button>

        <!-- Contador de imágenes -->
        <div class="absolute top-4 left-4 bg-black/50 text-white px-3 py-1 rounded-full text-sm">
          {{ currentImageIndex + 1 }} / {{ imagenes.length }}
        </div>

        <!-- Controles de autoplay -->
        <div class="absolute top-4 right-4 flex space-x-2">
          <button
            v-if="imagenes.length > 1"
            @click="toggleAutoPlay"
            :class="[
              'bg-black/50 text-white p-2 rounded-full hover:bg-black/70 transition-all duration-200',
              isAutoPlaying ? 'text-green-400' : 'text-red-400'
            ]"
            :title="isAutoPlaying ? 'Pausar autoplay' : 'Activar autoplay'"
          >
            <i :class="isAutoPlaying ? 'pi pi-pause' : 'pi pi-play'"></i>
          </button>
          
          <button
            @click="cerrarGaleria"
            class="bg-black/50 text-white p-2 rounded-full hover:bg-black/70 transition-all duration-200"
            title="Cerrar galería"
          >
            <i class="pi pi-times"></i>
          </button>
        </div>
      </div>

      <!-- Miniaturas -->
      <div 
        v-if="imagenes.length > 1"
        class="flex space-x-2 mt-4 overflow-x-auto pb-2 max-w-full"
        style="scrollbar-width: thin; scrollbar-color: #cbd5e0 #f7fafc;"
      >
        <button
          v-for="(imagen, index) in imagenes"
          :key="index"
          @click="irAImagen(index)"
          :class="[
            'flex-shrink-0 w-16 h-16 border-2 rounded-lg overflow-hidden transition-all duration-200',
            currentImageIndex === index 
              ? 'border-blue-500 scale-110' 
              : 'border-gray-300 hover:border-gray-400'
          ]"
        >
          <img
            :src="obtenerUrlImagen(imagen)"
            :alt="`Miniatura ${index + 1}`"
            class="w-full h-full object-cover"
          />
        </button>
      </div>

      <!-- Información adicional -->
      <div class="mt-4 text-center text-gray-600 text-sm">
        <p v-if="imagenes.length > 1 && isAutoPlaying">
          <i class="pi pi-info-circle mr-2"></i>
          Las imágenes cambian automáticamente cada 5 segundos
        </p>
        <p v-else-if="imagenes.length > 1">
          <i class="pi pi-hand-pointer mr-2"></i>
          Haz clic en las flechas o miniaturas para navegar
        </p>
      </div>
    </div>
  </Dialog>
</template>

<script setup>
import { computed } from 'vue'
import Dialog from 'primevue/dialog'

const props = defineProps({
  visible: {
    type: Boolean,
    required: true
  },
  imagenes: {
    type: Array,
    default: () => []
  },
  currentImageIndex: {
    type: Number,
    default: 0
  },
  isAutoPlaying: {
    type: Boolean,
    default: true
  },
  // Funciones
  cerrarGaleria: {
    type: Function,
    required: true
  },
  siguienteImagen: {
    type: Function,
    required: true
  },
  imagenAnterior: {
    type: Function,
    required: true
  },
  irAImagen: {
    type: Function,
    required: true
  },
  toggleAutoPlay: {
    type: Function,
    required: true
  }
})

// Función para obtener la URL de una imagen
const obtenerUrlImagen = (imagen) => {
  const nombreImagen = typeof imagen === 'string' ? imagen : imagen.nombre
  return `/images/tours/${nombreImagen}`
}

// Computed para la imagen actual
const imagenUrl = computed(() => {
  if (!props.imagenes || props.imagenes.length === 0) {
    return 'https://via.placeholder.com/800x500/2563eb/ffffff?text=Sin+Imagen+Disponible'
  }
  
  const imagen = props.imagenes[props.currentImageIndex]
  return obtenerUrlImagen(imagen)
})
</script>

<style scoped>
/* Estilos para el scrollbar de las miniaturas */
.overflow-x-auto::-webkit-scrollbar {
  height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: #f7fafc;
  border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background: #cbd5e0;
  border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background: #a0aec0;
}
</style>
