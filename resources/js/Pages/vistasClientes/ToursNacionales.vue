<script setup>
import Catalogo from '../Catalogo.vue'
import Card from 'primevue/card'
import Button from 'primevue/button'
import { ref } from 'vue'

// Datos estáticos de tours nacionales
const tours = ref([
  {
    id: 1,
    nombre: 'Tour El Salvador Colonial',
    descripcion: 'Descubre la historia colonial de El Salvador visitando iglesias centenarias y arquitectura colonial.',
    precio: 45.00,
    duracion: '1 día',
    imagen: '/images/productos/producto1.jpg',
    ubicacion: 'Suchitoto, Chalatenango',
    incluye: ['Transporte', 'Guía turístico', 'Almuerzo típico']
  },
  {
    id: 2,
    nombre: 'Ruta de las Flores',
    descripcion: 'Recorrido por los pueblos más pintorescos de la Cordillera del Bálsamo.',
    precio: 55.00,
    duracion: '1 día',
    imagen: '/images/productos/producto2.jpg',
    ubicacion: 'Ahuachapán',
    incluye: ['Transporte', 'Guía', 'Degustación de café', 'Almuerzo']
  },
  {
    id: 3,
    nombre: 'Volcán de Santa Ana',
    descripcion: 'Aventura al volcán más alto de El Salvador con vistas espectaculares de la laguna verde.',
    precio: 35.00,
    duracion: '1 día',
    imagen: '/images/productos/producto3.jpg',
    ubicacion: 'Santa Ana',
    incluye: ['Transporte', 'Guía especializado', 'Equipo de seguridad']
  },
  {
    id: 4,
    nombre: 'Playa El Tunco',
    descripcion: 'Día de playa en una de las costas más famosas para surf y relajación.',
    precio: 25.00,
    duracion: '1 día',
    imagen: '/images/productos/producto4.jpg',
    ubicacion: 'La Libertad',
    incluye: ['Transporte', 'Tiempo libre en playa', 'Almuerzo frente al mar']
  },
  {
    id: 5,
    nombre: 'Joya de Cerén',
    descripcion: 'Visita la "Pompeya de América" declarada Patrimonio de la Humanidad por la UNESCO.',
    precio: 30.00,
    duracion: '4 horas',
    imagen: '/images/productos/producto5.jpg',
    ubicacion: 'La Libertad',
    incluye: ['Transporte', 'Entrada al sitio', 'Guía arqueológico']
  },
  {
    id: 6,
    nombre: 'Lagos de Coatepeque',
    descripcion: 'Relajación en uno de los lagos más hermosos de Centroamérica.',
    precio: 40.00,
    duracion: '1 día',
    imagen: '/images/productos/producto6.jpg',
    ubicacion: 'Santa Ana',
    incluye: ['Transporte', 'Actividades acuáticas', 'Almuerzo con vista al lago']
  }
])

// Funciones para los botones
const reservarTour = (tour) => {
  alert(`¡Tour "${tour.nombre}" seleccionado para reserva!\nPrecio: $${tour.precio}\nDuración: ${tour.duracion}`)
}

const verMasInfo = (tour) => {
  alert(`Información del tour: ${tour.nombre}\nUbicación: ${tour.ubicacion}\nIncluye: ${tour.incluye.join(', ')}\nDuración: ${tour.duracion}`)
}
</script>

<template>
  <Catalogo>
    <div class="p-4 bg-gray-50 min-h-screen">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
          <h1 class="text-4xl font-bold mb-4 text-red-700">Tours Nacionales</h1>
          <p class="text-lg text-gray-600 mb-2">Descubre las maravillas de El Salvador</p>
          <p class="text-sm text-gray-500">Experiencias auténticas en nuestro hermoso país</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
          <div class="bg-white rounded-lg p-6 shadow-md text-center border border-gray-200">
            <h3 class="text-2xl font-bold text-red-600">{{ tours.length }}</h3>
            <p class="text-gray-600">Tours Disponibles</p>
          </div>
          <div class="bg-white rounded-lg p-6 shadow-md text-center border border-gray-200">
            <h3 class="text-2xl font-bold text-green-600">Desde $25</h3>
            <p class="text-gray-600">Precios Accesibles</p>
          </div>
          <div class="bg-white rounded-lg p-6 shadow-md text-center border border-gray-200">
            <h3 class="text-2xl font-bold text-blue-600">100%</h3>
            <p class="text-gray-600">Experiencia Local</p>
          </div>
        </div>

        <!-- Tours Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <Card
            v-for="tour in tours"
            :key="tour.id"
            class="border border-gray-300 bg-white shadow-md hover:shadow-lg transition-all duration-300 flex flex-col h-full transform hover:-translate-y-1"
          >
            <template #header>
              <div class="relative w-full h-48 bg-gradient-to-br from-gray-100 to-gray-200 rounded-t-lg overflow-hidden group">
                <img
                  :src="tour.imagen"
                  :alt="tour.nombre"
                  class="object-cover h-full w-full group-hover:scale-110 transition-transform duration-500"
                  @error="$event.target.src = 'https://via.placeholder.com/400x300/ef4444/ffffff?text=' + encodeURIComponent(tour.nombre.substring(0, 15))"
                />
                <div class="absolute top-3 right-3 bg-green-600 text-white px-3 py-1 rounded-full text-sm font-semibold shadow-lg">
                  ${{ tour.precio }}
                </div>
                <div class="absolute bottom-3 left-3 bg-black/70 text-white px-3 py-1 rounded-full text-xs">
                  {{ tour.duracion }}
                </div>
              </div>
            </template>
            
            <template #title>
              <span class="text-lg font-bold text-gray-800 leading-tight">{{ tour.nombre }}</span>
            </template>
            
            <template #content>
              <div class="flex-grow space-y-3">
                <p class="text-gray-600 text-sm line-clamp-2">
                  {{ tour.descripcion }}
                </p>
                <div class="flex items-center text-sm text-gray-500">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                  </svg>
                  {{ tour.ubicacion }}
                </div>
                <div class="space-y-1">
                  <p class="text-xs font-semibold text-gray-700">Incluye:</p>
                  <ul class="text-xs text-gray-600">
                    <li v-for="item in tour.incluye.slice(0, 2)" :key="item" class="flex items-center">
                      <span class="w-1 h-1 bg-red-500 rounded-full mr-2"></span>
                      {{ item }}
                    </li>
                    <li v-if="tour.incluye.length > 2" class="text-gray-400">
                      + {{ tour.incluye.length - 2 }} más...
                    </li>
                  </ul>
                </div>
              </div>
            </template>
            
            <template #footer>
              <div class="flex gap-2 mt-4">
                <Button
                  label="Reservar"
                  @click="reservarTour(tour)"
                  class="!bg-red-600 !border-none !px-4 !py-2 !text-white !text-sm font-semibold rounded-lg hover:!bg-red-700 transition-all flex-1 shadow-sm"
                />
                <Button
                  label="Info"
                  @click="verMasInfo(tour)"
                  outlined
                  class="!border-red-600 !text-red-600 !px-4 !py-2 !text-sm font-semibold rounded-lg hover:!bg-red-50 transition-all"
                />
              </div>
            </template>
          </Card>
        </div>

        <!-- Info adicional -->
        <div class="mt-12 bg-white rounded-xl p-8 shadow-md border border-gray-200">
          <h2 class="text-2xl font-bold text-red-700 mb-6 text-center">¿Por qué elegir nuestros tours nacionales?</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
              <div class="text-4xl mb-3">🏛️</div>
              <h3 class="font-semibold text-red-600 mb-2">Historia y Cultura</h3>
              <p class="text-gray-600 text-sm">Descubre la rica historia y tradiciones de El Salvador con guías locales expertos</p>
            </div>
            <div class="text-center">
              <div class="text-4xl mb-3">🌋</div>
              <h3 class="font-semibold text-red-600 mb-2">Naturaleza Única</h3>
              <p class="text-gray-600 text-sm">Explora volcanes, lagos, playas y bosques en paisajes únicos de Centroamérica</p>
            </div>
            <div class="text-center">
              <div class="text-4xl mb-3">🤝</div>
              <h3 class="font-semibold text-red-600 mb-2">Apoyo Local</h3>
              <p class="text-gray-600 text-sm">Contribuye al desarrollo de las comunidades locales con turismo responsable</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Catalogo>
</template>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
