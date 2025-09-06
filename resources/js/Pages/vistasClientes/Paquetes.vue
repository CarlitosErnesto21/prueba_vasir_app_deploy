<script setup>
import Catalogo from '../Catalogo.vue'
import { ref, computed } from 'vue'
import { useToast } from 'primevue/usetoast'

// Inicializar toast
const toast = useToast()

// Datos estáticos de paquetes turísticos
const paquetes = ref([
  {
    id: 1,
    titulo: 'Aventura Extrema El Salvador',
    descripcion: 'Paquete completo de adrenalina: rapel, canopy, senderismo y kayak en los mejores destinos naturales.',
    precio: 145,
    duracion: '3 días / 2 noches',
    imagen: '',
    incluye: ['Transporte privado', 'Equipos de seguridad', 'Guía especializado', 'Desayunos y almuerzos', 'Hospedaje'],
    destinos: ['Cerro Verde', 'Lago de Coatepeque', 'Parque Nacional El Imposible'],
    categoria: 'Aventura',
    personas: '2-8 personas',
    destacado: true
  },
  {
    id: 2,
    titulo: 'Ruta Cultural Ancestral',
    descripcion: 'Descubre la rica historia de El Salvador visitando sitios arqueológicos, museos y pueblos coloniales.',
    precio: 98,
    duracion: '2 días / 1 noche',
    imagen: '',
    incluye: ['Transporte turístico', 'Entradas a sitios', 'Guía historiador', 'Almuerzo típico', 'Material informativo'],
    destinos: ['Joya de Cerén', 'Suchitoto', 'Museo Nacional de Antropología'],
    categoria: 'Cultural',
    personas: '4-15 personas',
    destacado: false
  },
  {
    id: 3,
    titulo: 'Playas y Relax Premium',
    descripcion: 'Escapada perfecta a las mejores playas del país con servicios de spa y gastronomía de primera.',
    precio: 189,
    duracion: '3 días / 2 noches',
    imagen: '',
    incluye: ['Resort 4 estrellas', 'Spa incluido', 'Todas las comidas', 'Actividades acuáticas', 'Traslados'],
    destinos: ['El Tunco', 'Costa del Sol', 'Puerto de La Libertad'],
    categoria: 'Playa',
    personas: '2-6 personas',
    destacado: true
  },
  {
    id: 4,
    titulo: 'Familia Aventurera',
    descripcion: 'Paquete diseñado especialmente para familias con actividades seguras y divertidas para todas las edades.',
    precio: 125,
    duracion: '2 días / 1 noche',
    imagen: '',
    incluye: ['Hotel familiar', 'Actividades para niños', 'Comidas incluidas', 'Transporte seguro', 'Entretenimiento'],
    destinos: ['Parque Acuático', 'Zoo Nacional', 'Parque de Diversiones'],
    categoria: 'Familiar',
    personas: '4-12 personas',
    destacado: false
  },
  {
    id: 5,
    titulo: 'Volcanes y Montañas',
    descripcion: 'Expedición a los volcanes más impresionantes con caminatas, observación de flora y fauna única.',
    precio: 165,
    duracion: '4 días / 3 noches',
    imagen: '',
    incluye: ['Guía de montaña', 'Equipo de trekking', 'Camping', 'Todas las comidas', 'Transporte 4x4'],
    destinos: ['Volcán de Santa Ana', 'Volcán de Izalco', 'Cerro El Pital'],
    categoria: 'Montaña',
    personas: '3-10 personas',
    destacado: false
  },
  {
    id: 6,
    titulo: 'Gastronomía Salvadoreña',
    descripcion: 'Tour gastronómico completo para conocer los sabores auténticos y tradiciones culinarias del país.',
    precio: 89,
    duracion: '1 día',
    imagen: '',
    incluye: ['Chef especializado', 'Degustaciones', 'Recetas tradicionales', 'Mercados locales', 'Transporte'],
    destinos: ['Mercado Central', 'Restaurantes típicos', 'Cocina tradicional'],
    categoria: 'Gastronomía',
    personas: '6-20 personas',
    destacado: true
  }
])

// Categorías para filtros
const categorias = computed(() => {
  const cats = [...new Set(paquetes.value.map(p => p.categoria))]
  return cats
})

const selectedCategoria = ref('Todos')
const selectedPrecio = ref('Todos')

// Paquetes filtrados
const paquetesFiltrados = computed(() => {
  let filtrados = paquetes.value
  
  if (selectedCategoria.value !== 'Todos') {
    filtrados = filtrados.filter(p => p.categoria === selectedCategoria.value)
  }
  
  if (selectedPrecio.value !== 'Todos') {
    if (selectedPrecio.value === 'bajo') {
      filtrados = filtrados.filter(p => p.precio < 100)
    } else if (selectedPrecio.value === 'medio') {
      filtrados = filtrados.filter(p => p.precio >= 100 && p.precio < 150)
    } else if (selectedPrecio.value === 'alto') {
      filtrados = filtrados.filter(p => p.precio >= 150)
    }
  }
  
  return filtrados
})

// Funciones para botones
const verDetalles = (paquete) => {
  toast.add({
    severity: 'info',
    summary: `Detalles de ${paquete.titulo}`,
    detail: `Duración: ${paquete.duracion} | Destinos: ${paquete.destinos.join(', ')} | Incluye: ${paquete.incluye.join(', ')} | Precio: $${paquete.precio} por persona`,
    life: 8000
  })
}

const reservarPaquete = (paquete) => {
  toast.add({
    severity: 'success',
    summary: `Paquete "${paquete.titulo}" seleccionado`,
    detail: `Precio: $${paquete.precio} | Duración: ${paquete.duracion} | Personas: ${paquete.personas}`,
    life: 5000
  })
}
</script>
<template>
  <Catalogo>
    <Toast />
    <div class="p-4 bg-gray-50 min-h-screen">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
          <h1 class="text-4xl font-bold mb-4 text-red-700">📦 Paquetes Turísticos</h1>
          <p class="text-lg text-gray-600 mb-2">Experiencias completas diseñadas para crear recuerdos inolvidables</p>
          <p class="text-sm text-gray-500">Descubre El Salvador con nuestros paquetes todo incluido</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
          <div class="bg-white rounded-lg p-6 shadow-md text-center border border-gray-200">
            <h3 class="text-2xl font-bold text-red-600">{{ paquetes.length }}</h3>
            <p class="text-gray-600">Paquetes</p>
          </div>
          <div class="bg-white rounded-lg p-6 shadow-md text-center border border-gray-200">
            <h3 class="text-2xl font-bold text-green-600">{{ categorias.length }}</h3>
            <p class="text-gray-600">Categorías</p>
          </div>
          <div class="bg-white rounded-lg p-6 shadow-md text-center border border-gray-200">
            <h3 class="text-2xl font-bold text-blue-600">Desde $89</h3>
            <p class="text-gray-600">Precios</p>
          </div>
          <div class="bg-white rounded-lg p-6 shadow-md text-center border border-gray-200">
            <h3 class="text-2xl font-bold text-purple-600">Todo Incluido</h3>
            <p class="text-gray-600">Servicios</p>
          </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-xl p-6 shadow-md border border-gray-200 mb-8">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">Filtrar paquetes</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Por categoría</label>
              <div class="flex flex-wrap gap-2">
                <button
                  @click="selectedCategoria = 'Todos'"
                  :class="['px-3 py-1 rounded-full text-sm font-medium transition-all', 
                    selectedCategoria === 'Todos' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-red-100']"
                >
                  Todos
                </button>
                <button
                  v-for="categoria in categorias"
                  :key="categoria"
                  @click="selectedCategoria = categoria"
                  :class="['px-3 py-1 rounded-full text-sm font-medium transition-all', 
                    selectedCategoria === categoria ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-red-100']"
                >
                  {{ categoria }}
                </button>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Por precio</label>
              <div class="flex flex-wrap gap-2">
                <button
                  @click="selectedPrecio = 'Todos'"
                  :class="['px-3 py-1 rounded-full text-sm font-medium transition-all', 
                    selectedPrecio === 'Todos' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-green-100']"
                >
                  Todos
                </button>
                <button
                  @click="selectedPrecio = 'bajo'"
                  :class="['px-3 py-1 rounded-full text-sm font-medium transition-all', 
                    selectedPrecio === 'bajo' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-green-100']"
                >
                  < $100
                </button>
                <button
                  @click="selectedPrecio = 'medio'"
                  :class="['px-3 py-1 rounded-full text-sm font-medium transition-all', 
                    selectedPrecio === 'medio' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-green-100']"
                >
                  $100 - $150
                </button>
                <button
                  @click="selectedPrecio = 'alto'"
                  :class="['px-3 py-1 rounded-full text-sm font-medium transition-all', 
                    selectedPrecio === 'alto' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-green-100']"
                >
                  > $150
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Paquetes Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <Card
            v-for="paquete in paquetesFiltrados"
            :key="paquete.id"
            class="border border-gray-300 bg-white shadow-md hover:shadow-lg transition-all duration-300 flex flex-col h-full transform hover:-translate-y-1"
          >
            <template #header>
              <div class="relative w-full h-48 bg-gradient-to-br from-gray-100 to-gray-200 rounded-t-lg overflow-hidden group">
                <div
                  v-if="paquete.imagen"
                  class="object-cover h-full w-full group-hover:scale-110 transition-transform duration-500 bg-center bg-cover"
                  :style="{ backgroundImage: `url(${paquete.imagen})` }"
                ></div>
                <div 
                  v-else
                  class="h-full w-full flex items-center justify-center bg-gradient-to-br from-gray-200 to-gray-300"
                >
                  <div class="text-center text-gray-500">
                    <i class="fas fa-image text-4xl mb-2"></i>
                    <p class="text-sm font-medium">{{ paquete.categoria }}</p>
                  </div>
                </div>
                <div class="absolute top-2 right-2 bg-green-600 text-white px-3 py-1 rounded-full text-sm font-semibold shadow-lg">
                  ${{ paquete.precio }}
                </div>
                <div class="absolute bottom-2 left-2 bg-black/70 text-white px-2 py-1 rounded-full text-xs">
                  {{ paquete.duracion }}
                </div>
                <div class="absolute top-2 left-2 bg-blue-600 text-white px-2 py-1 rounded-full text-xs font-semibold">
                  {{ paquete.categoria }}
                </div>
                <div v-if="paquete.destacado" class="absolute bottom-2 right-2 bg-yellow-500 text-black px-2 py-1 rounded-full text-xs font-bold">
                  ⭐ Destacado
                </div>
              </div>
            </template>
            
            <template #title>
              <span class="text-lg font-bold text-gray-800 leading-tight line-clamp-2">{{ paquete.titulo }}</span>
            </template>
            
            <template #content>
              <div class="flex-grow space-y-3">
                <p class="text-gray-600 text-sm line-clamp-2">
                  {{ paquete.descripcion }}
                </p>
                <div class="space-y-2">
                  <div class="flex items-center text-xs text-gray-600">
                    <span class="w-1 h-1 bg-blue-500 rounded-full mr-2"></span>
                    <strong>Personas:</strong> {{ paquete.personas }}
                  </div>
                  <div>
                    <p class="text-xs font-semibold text-gray-700 mb-1">Destinos principales:</p>
                    <div class="flex flex-wrap gap-1">
                      <span
                        v-for="destino in paquete.destinos.slice(0, 2)"
                        :key="destino"
                        class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs"
                      >
                        {{ destino }}
                      </span>
                      <span v-if="paquete.destinos.length > 2" class="text-gray-400 text-xs">
                        +{{ paquete.destinos.length - 2 }} más
                      </span>
                    </div>
                  </div>
                  <div>
                    <p class="text-xs font-semibold text-gray-700 mb-1">Incluye:</p>
                    <ul class="text-xs text-gray-600">
                      <li v-for="item in paquete.incluye.slice(0, 2)" :key="item" class="flex items-center">
                        <span class="w-1 h-1 bg-green-500 rounded-full mr-2"></span>
                        {{ item }}
                      </li>
                      <li v-if="paquete.incluye.length > 2" class="text-gray-400">
                        + {{ paquete.incluye.length - 2 }} servicios más...
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </template>
            
            <template #footer>
              <div class="flex gap-2 mt-4">
                <Button
                  label="Reservar"
                  @click="reservarPaquete(paquete)"
                  class="!bg-red-600 !border-none !px-3 !py-2 !text-white !text-sm font-semibold rounded hover:!bg-red-700 transition-all flex-1 shadow-sm"
                  size="small"
                />
                <Button
                  label="Detalles"
                  @click="verDetalles(paquete)"
                  outlined
                  class="!border-red-600 !text-red-600 !px-3 !py-2 !text-sm font-semibold rounded hover:!bg-red-50 transition-all"
                  size="small"
                />
              </div>
            </template>
          </Card>
        </div>

        <!-- Info adicional -->
        <div class="mt-12 bg-white rounded-xl p-8 shadow-md border border-gray-200">
          <h2 class="text-2xl font-bold text-red-700 mb-6 text-center">¿Por qué elegir nuestros paquetes?</h2>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center">
              <div class="text-4xl mb-3">📋</div>
              <h3 class="font-semibold text-red-600 mb-2">Todo Planificado</h3>
              <p class="text-gray-600 text-sm">Itinerarios completos sin preocupaciones</p>
            </div>
            <div class="text-center">
              <div class="text-4xl mb-3">💰</div>
              <h3 class="font-semibold text-red-600 mb-2">Mejor Precio</h3>
              <p class="text-gray-600 text-sm">Paquetes con descuentos y ofertas especiales</p>
            </div>
            <div class="text-center">
              <div class="text-4xl mb-3">👥</div>
              <h3 class="font-semibold text-red-600 mb-2">Guías Expertos</h3>
              <p class="text-gray-600 text-sm">Acompañamiento profesional en cada destino</p>
            </div>
            <div class="text-center">
              <div class="text-4xl mb-3">🔒</div>
              <h3 class="font-semibold text-red-600 mb-2">Seguridad Total</h3>
              <p class="text-gray-600 text-sm">Seguros y protocolos de seguridad incluidos</p>
            </div>
          </div>
        </div>

        <!-- Categorías disponibles -->
        <div class="mt-8 bg-white rounded-xl p-6 shadow-md border border-gray-200">
          <h3 class="text-xl font-semibold text-gray-800 mb-4 text-center">Categorías de Paquetes</h3>
          <div class="flex flex-wrap justify-center gap-3">
            <span
              v-for="categoria in categorias"
              :key="categoria"
              class="bg-red-100 text-red-700 px-4 py-2 rounded-full text-sm font-medium"
            >
              {{ categoria }}
            </span>
          </div>
        </div>

        <!-- Mensaje de no resultados -->
        <div v-if="paquetesFiltrados.length === 0" class="text-center py-12">
          <div class="text-6xl mb-4">🔍</div>
          <h3 class="text-xl font-semibold text-gray-600 mb-2">No se encontraron paquetes</h3>
          <p class="text-gray-500">Intenta con otros filtros o <button @click="selectedCategoria = 'Todos'; selectedPrecio = 'Todos'" class="text-red-600 hover:underline">ver todos los paquetes</button></p>
        </div>

        <!-- CTA final -->
        <div class="mt-8 text-center">
          <span class="text-gray-600">¿Buscas algo personalizado? <a href="/contactos" class="text-red-600 hover:underline font-semibold">Contáctanos para paquetes a medida</a></span>
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
