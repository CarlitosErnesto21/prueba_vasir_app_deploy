<script setup>
import Catalogo from '../Catalogo.vue'
import Card from 'primevue/card'
import Button from 'primevue/button'
import { ref } from 'vue'

// Datos estáticos de tours internacionales
const tours = ref([
  {
    id: 1,
    nombre: 'Guatemala Colonial',
    descripcion: 'Explora Antigua Guatemala y el Lago Atitlán en un viaje de 3 días inolvidable.',
    precio: 285.00,
    duracion: '3 días',
    imagen: '/images/productos/producto1.jpg',
    pais: 'Guatemala 🇬🇹',
    incluye: ['Transporte internacional', 'Hotel 3*', 'Guía bilingüe', 'Desayunos']
  },
  {
    id: 2,
    nombre: 'Costa Rica Aventura',
    descripcion: 'Volcanes, playas del Pacífico y biodiversidad única en parques nacionales.',
    precio: 450.00,
    duracion: '5 días',
    imagen: '/images/productos/producto2.jpg',
    pais: 'Costa Rica 🇨🇷',
    incluye: ['Vuelos', 'Hotel 4*', 'Tours ecológicos', 'Todas las comidas']
  },
  {
    id: 3,
    nombre: 'Honduras Copán',
    descripcion: 'Descubre las ruinas mayas más importantes de Honduras y su cultura.',
    precio: 195.00,
    duracion: '2 días',
    imagen: '/images/productos/producto3.jpg',
    pais: 'Honduras 🇭🇳',
    incluye: ['Transporte', 'Hotel', 'Entrada a ruinas', 'Guía arqueológico']
  },
  {
    id: 4,
    nombre: 'Nicaragua Lagos y Volcanes',
    descripcion: 'Granada colonial, Isla Ometepe y el impresionante Lago Cocibolca.',
    precio: 320.00,
    duracion: '4 días',
    imagen: '/images/productos/producto4.jpg',
    pais: 'Nicaragua 🇳🇮',
    incluye: ['Transporte', 'Hotel boutique', 'Tours en lancha', 'Guía local']
  },
  {
    id: 5,
    nombre: 'Panamá Canal y Ciudad',
    descripcion: 'Ciudad de Panamá, Canal de Panamá y Casco Viejo histórico.',
    precio: 525.00,
    duracion: '4 días',
    imagen: '/images/productos/producto5.jpg',
    pais: 'Panamá 🇵🇦',
    incluye: ['Vuelos', 'Hotel 4*', 'Tour del Canal', 'City tour', 'Traslados']
  },
  {
    id: 6,
    nombre: 'Belice Barrera de Coral',
    descripcion: 'Snorkel en la segunda barrera de coral más grande del mundo.',
    precio: 680.00,
    duracion: '6 días',
    imagen: '/images/productos/producto6.jpg',
    pais: 'Belice 🇧🇿',
    incluye: ['Vuelos', 'Resort', 'Tours de snorkel', 'Todas las comidas']
  },
  {
    id: 7,
    nombre: 'México Riviera Maya',
    descripcion: 'Playas paradisíacas, cenotes cristalinos y ruinas mayas en Cancún.',
    precio: 750.00,
    duracion: '7 días',
    imagen: '/images/productos/producto1.jpg',
    pais: 'México 🇲🇽',
    incluye: ['Vuelos', 'Resort Todo Incluido', 'Tours opcionales', 'Traslados']
  },
  {
    id: 8,
    nombre: 'Estados Unidos Miami',
    descripcion: 'Experiencia urbana en Miami: playas, compras y vida nocturna.',
    precio: 920.00,
    duracion: '5 días',
    imagen: '/images/productos/producto2.jpg',
    pais: 'Estados Unidos 🇺🇸',
    incluye: ['Vuelos', 'Hotel en South Beach', 'City tour', 'Asistencia']
  }
])

// Funciones para los botones
const reservarTour = (tour) => {
  alert(`¡Tour "${tour.nombre}" seleccionado para reserva!\nDestino: ${tour.pais}\nPrecio: $${tour.precio}\nDuración: ${tour.duracion}`)
}

const verMasInfo = (tour) => {
  alert(`Información del tour: ${tour.nombre}\nDestino: ${tour.pais}\nIncluye: ${tour.incluye.join(', ')}\nDuración: ${tour.duracion}`)
}
</script>

<template>
  <Catalogo>
    <div class="p-4 bg-gray-50 min-h-screen">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
          <h1 class="text-4xl font-bold mb-4 text-red-700">🌎 Tours Internacionales</h1>
          <p class="text-lg text-gray-600 mb-2">Explora destinos increíbles alrededor del mundo</p>
          <p class="text-sm text-gray-500">Experiencias únicas más allá de nuestras fronteras</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
          <div class="bg-white rounded-lg p-6 shadow-md text-center border border-gray-200">
            <h3 class="text-2xl font-bold text-red-600">{{ tours.length }}</h3>
            <p class="text-gray-600">Destinos</p>
          </div>
          <div class="bg-white rounded-lg p-6 shadow-md text-center border border-gray-200">
            <h3 class="text-2xl font-bold text-green-600">7</h3>
            <p class="text-gray-600">Países</p>
          </div>
          <div class="bg-white rounded-lg p-6 shadow-md text-center border border-gray-200">
            <h3 class="text-2xl font-bold text-blue-600">Desde $195</h3>
            <p class="text-gray-600">Precios</p>
          </div>
          <div class="bg-white rounded-lg p-6 shadow-md text-center border border-gray-200">
            <h3 class="text-2xl font-bold text-purple-600">Todo Incluido</h3>
            <p class="text-gray-600">Paquetes</p>
          </div>
        </div>

        <!-- Tours Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <Card
            v-for="tour in tours"
            :key="tour.id"
            class="border border-gray-300 bg-white shadow-md hover:shadow-lg transition-all duration-300 flex flex-col h-full transform hover:-translate-y-1"
          >
            <template #header>
              <div class="relative w-full h-40 bg-gradient-to-br from-gray-100 to-gray-200 rounded-t-lg overflow-hidden group">
                <img
                  :src="tour.imagen"
                  :alt="tour.nombre"
                  class="object-cover h-full w-full group-hover:scale-110 transition-transform duration-500"
                  @error="$event.target.src = 'https://via.placeholder.com/350x250/ef4444/ffffff?text=' + encodeURIComponent(tour.pais.split(' ')[0])"
                />
                <div class="absolute top-2 right-2 bg-green-600 text-white px-2 py-1 rounded-full text-xs font-semibold shadow-lg">
                  ${{ tour.precio }}
                </div>
                <div class="absolute bottom-2 left-2 bg-black/70 text-white px-2 py-1 rounded-full text-xs">
                  {{ tour.duracion }}
                </div>
                <div class="absolute top-2 left-2 bg-blue-600 text-white px-2 py-1 rounded-full text-xs font-semibold">
                  {{ tour.pais }}
                </div>
              </div>
            </template>
            
            <template #title>
              <span class="text-base font-bold text-gray-800 leading-tight line-clamp-2">{{ tour.nombre }}</span>
            </template>
            
            <template #content>
              <div class="flex-grow space-y-2">
                <p class="text-gray-600 text-xs line-clamp-2">
                  {{ tour.descripcion }}
                </p>
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
              <div class="flex gap-2 mt-3">
                <Button
                  label="Reservar"
                  @click="reservarTour(tour)"
                  class="!bg-red-600 !border-none !px-3 !py-1 !text-white !text-xs font-semibold rounded hover:!bg-red-700 transition-all flex-1 shadow-sm"
                  size="small"
                />
                <Button
                  label="Info"
                  @click="verMasInfo(tour)"
                  outlined
                  class="!border-red-600 !text-red-600 !px-3 !py-1 !text-xs font-semibold rounded hover:!bg-red-50 transition-all"
                  size="small"
                />
              </div>
            </template>
          </Card>
        </div>

        <!-- Info adicional -->
        <div class="mt-12 bg-white rounded-xl p-8 shadow-md border border-gray-200">
          <h2 class="text-2xl font-bold text-red-700 mb-6 text-center">¿Por qué viajar al extranjero con nosotros?</h2>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center">
              <div class="text-4xl mb-3">✈️</div>
              <h3 class="font-semibold text-red-600 mb-2">Vuelos Incluidos</h3>
              <p class="text-gray-600 text-sm">Boletos aéreos y traslados incluidos en la mayoría de paquetes</p>
            </div>
            <div class="text-center">
              <div class="text-4xl mb-3">🏨</div>
              <h3 class="font-semibold text-red-600 mb-2">Hoteles Seleccionados</h3>
              <p class="text-gray-600 text-sm">Alojamiento en hoteles verificados y bien ubicados</p>
            </div>
            <div class="text-center">
              <div class="text-4xl mb-3">🗺️</div>
              <h3 class="font-semibold text-red-600 mb-2">Guías Expertos</h3>
              <p class="text-gray-600 text-sm">Guías locales que conocen los mejores lugares y secretos</p>
            </div>
            <div class="text-center">
              <div class="text-4xl mb-3">📞</div>
              <h3 class="font-semibold text-red-600 mb-2">Soporte 24/7</h3>
              <p class="text-gray-600 text-sm">Asistencia completa durante todo tu viaje</p>
            </div>
          </div>
        </div>

        <!-- Países disponibles -->
        <div class="mt-8 bg-white rounded-xl p-6 shadow-md border border-gray-200">
          <h3 class="text-xl font-semibold text-gray-800 mb-4 text-center">Destinos Disponibles</h3>
          <div class="flex flex-wrap justify-center gap-3">
            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-medium">🇬🇹 Guatemala</span>
            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-medium">🇨🇷 Costa Rica</span>
            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-medium">🇭🇳 Honduras</span>
            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-medium">🇳🇮 Nicaragua</span>
            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-medium">🇵🇦 Panamá</span>
            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-medium">🇧🇿 Belice</span>
            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-medium">🇲🇽 México</span>
            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-medium">🇺🇸 Estados Unidos</span>
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
