<script setup>
import Catalogo from '../Catalogo.vue'
import { ref, computed } from 'vue'
import { useToast } from 'primevue/usetoast'

// Inicializar toast
const toast = useToast()

// Datos estáticos de reservaciones disponibles
const reservaciones = ref([
  {
    id: 1,
    titulo: 'Cerro Verde y Lago de Ilopango',
    descripcion: 'Aventura completa en uno de los volcanes más espectaculares con vista al lago más grande de El Salvador.',
    precio: 60,
    imagen: '',
    destino: 'Cerro Verde, Santa Ana',
    punto_salida: 'Oficina Central San Salvador',
    fecha_salida: '2025-08-25',
    hora_salida: '08:00',
    hora_regreso: '17:00',
    duracion: '9 horas',
    cupo_maximo: 15,
    cupos_disponibles: 8,
    estado: 'Disponible',
    incluye: ['Transporte ida y vuelta', 'Guía especializado', 'Entrada al parque', 'Almuerzo típico'],
    categoria: 'Volcanes',
    dificultad: 'Intermedio'
  },
  {
    id: 2,
    titulo: 'Festival del Melocotón - El Pital',
    descripcion: 'Experiencia única en el punto más alto de El Salvador durante el famoso Festival del Melocotón.',
    precio: 85,
    imagen: '',
    destino: 'Cerro El Pital, Chalatenango',
    punto_salida: 'Terminal de Oriente',
    fecha_salida: '2025-08-30',
    hora_salida: '06:30',
    hora_regreso: '19:00',
    duracion: '12 horas',
    cupo_maximo: 20,
    cupos_disponibles: 12,
    estado: 'Disponible',
    incluye: ['Transporte especializado', 'Desayuno y almuerzo', 'Entrada al festival', 'Guía cultural', 'Degustación de melocotones'],
    categoria: 'Cultural',
    dificultad: 'Fácil'
  },
  {
    id: 3,
    titulo: 'Ruta de las Flores Completa',
    descripcion: 'Recorrido por los pueblos más pintorescos: Nahuizalco, Salcoatitán, Juayúa, Apaneca y Ataco.',
    precio: 75,
    imagen: '',
    destino: 'Ruta de las Flores, Ahuachapán',
    punto_salida: 'Plaza Salvador del Mundo',
    fecha_salida: '2025-09-01',
    hora_salida: '07:00',
    hora_regreso: '18:30',
    duracion: '11 horas',
    cupo_maximo: 25,
    cupos_disponibles: 18,
    estado: 'Disponible',
    incluye: ['Transporte turístico', 'Guía local', 'Almuerzo', 'Visitas a talleres', 'Tiempo libre en cada pueblo'],
    categoria: 'Cultural',
    dificultad: 'Fácil'
  },
  {
    id: 4,
    titulo: 'Joya de Cerén - Pompeya de América',
    descripcion: 'Descubre el sitio arqueológico mejor conservado de Mesoamérica, Patrimonio de la Humanidad.',
    precio: 45,
    imagen: '',
    destino: 'Joya de Cerén, La Libertad',
    punto_salida: 'Metrocentro',
    fecha_salida: '2025-09-05',
    hora_salida: '09:00',
    hora_regreso: '15:00',
    duracion: '6 horas',
    cupo_maximo: 30,
    cupos_disponibles: 22,
    estado: 'Disponible',
    incluye: ['Transporte', 'Guía arqueólogo', 'Entrada al sitio', 'Material educativo'],
    categoria: 'Arqueología',
    dificultad: 'Fácil'
  },
  {
    id: 5,
    titulo: 'Suchitoto Colonial y Lago Suchitlán',
    descripcion: 'Ciudad colonial mejor conservada de El Salvador con paseo en lancha por el embalse más grande.',
    precio: 70,
    imagen: '',
    destino: 'Suchitoto, Cuscatlán',
    punto_salida: 'Centro Histórico San Salvador',
    fecha_salida: '2025-09-08',
    hora_salida: '08:30',
    hora_regreso: '17:30',
    duracion: '9 horas',
    cupo_maximo: 20,
    cupos_disponibles: 5,
    estado: 'Últimos cupos',
    incluye: ['Transporte', 'City tour', 'Paseo en lancha', 'Almuerzo típico', 'Guía histórico'],
    categoria: 'Colonial',
    dificultad: 'Fácil'
  },
  {
    id: 6,
    titulo: 'Tazumal y Casa Blanca Arqueológicos',
    descripcion: 'Dos importantes sitios arqueológicos mayas en Chalchuapa con museo incluido.',
    precio: 50,
    imagen: '',
    destino: 'Chalchuapa, Santa Ana',
    fecha_salida: '2025-09-12',
    punto_salida: 'Terminal de Occidente',
    hora_salida: '08:00',
    hora_regreso: '16:00',
    duracion: '8 horas',
    cupo_maximo: 25,
    cupos_disponibles: 0,
    estado: 'Agotado',
    incluye: ['Transporte', 'Guía especializado', 'Entradas a sitios', 'Visita al museo', 'Almuerzo'],
    categoria: 'Arqueología',
    dificultad: 'Fácil'
  }
])

// Filtros
const selectedCategoria = ref('Todos')
const selectedEstado = ref('Todos')
const selectedPrecio = ref('Todos')

// Categorías disponibles
const categorias = computed(() => {
  const cats = [...new Set(reservaciones.value.map(r => r.categoria))]
  return cats
})

// Estados disponibles
const estados = computed(() => {
  const ests = [...new Set(reservaciones.value.map(r => r.estado))]
  return ests
})

// Reservaciones filtradas
const reservacionesFiltradas = computed(() => {
  let filtradas = reservaciones.value
  
  if (selectedCategoria.value !== 'Todos') {
    filtradas = filtradas.filter(r => r.categoria === selectedCategoria.value)
  }
  
  if (selectedEstado.value !== 'Todos') {
    filtradas = filtradas.filter(r => r.estado === selectedEstado.value)
  }
  
  if (selectedPrecio.value !== 'Todos') {
    if (selectedPrecio.value === 'bajo') {
      filtradas = filtradas.filter(r => r.precio < 60)
    } else if (selectedPrecio.value === 'medio') {
      filtradas = filtradas.filter(r => r.precio >= 60 && r.precio < 80)
    } else if (selectedPrecio.value === 'alto') {
      filtradas = filtradas.filter(r => r.precio >= 80)
    }
  }
  
  return filtradas
})

// Funciones para botones
const reservarAhora = (reservacion) => {
  if (reservacion.estado === 'Agotado') {
    toast.add({
      severity: 'warn',
      summary: 'Reservación agotada',
      detail: 'Lo sentimos, esta reservación está agotada.',
      life: 4000
    })
    return
  }
  toast.add({
    severity: 'success',
    summary: `Reservación "${reservacion.titulo}" seleccionada`,
    detail: `Fecha: ${reservacion.fecha_salida} | Hora: ${reservacion.hora_salida} | Precio: $${reservacion.precio} | Cupos disponibles: ${reservacion.cupos_disponibles}`,
    life: 5000
  })
}

const verMasInfo = (reservacion) => {
  toast.add({
    severity: 'info',
    summary: `Información de ${reservacion.titulo}`,
    detail: `Destino: ${reservacion.destino} | Salida: ${reservacion.punto_salida} | Fecha: ${reservacion.fecha_salida} | Horario: ${reservacion.hora_salida} - ${reservacion.hora_regreso} | Duración: ${reservacion.duracion} | Dificultad: ${reservacion.dificultad} | Incluye: ${reservacion.incluye.join(', ')} | Cupos: ${reservacion.cupos_disponibles}/${reservacion.cupo_maximo}`,
    life: 8000
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
          <h1 class="text-4xl font-bold mb-4 text-red-700">📅 Reservaciones Disponibles</h1>
          <p class="text-lg text-gray-600 mb-2">Reserva tu lugar en nuestros próximos tours y excursiones</p>
          <p class="text-sm text-gray-500">Fechas confirmadas con salidas garantizadas</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
          <div class="bg-white rounded-lg p-6 shadow-md text-center border border-gray-200">
            <h3 class="text-2xl font-bold text-red-600">{{ reservaciones.length }}</h3>
            <p class="text-gray-600">Tours Programados</p>
          </div>
          <div class="bg-white rounded-lg p-6 shadow-md text-center border border-gray-200">
            <h3 class="text-2xl font-bold text-green-600">{{ reservaciones.filter(r => r.estado === 'Disponible').length }}</h3>
            <p class="text-gray-600">Disponibles</p>
          </div>
          <div class="bg-white rounded-lg p-6 shadow-md text-center border border-gray-200">
            <h3 class="text-2xl font-bold text-yellow-600">{{ reservaciones.filter(r => r.estado === 'Últimos cupos').length }}</h3>
            <p class="text-gray-600">Últimos Cupos</p>
          </div>
          <div class="bg-white rounded-lg p-6 shadow-md text-center border border-gray-200">
            <h3 class="text-2xl font-bold text-blue-600">Desde $45</h3>
            <p class="text-gray-600">Precios</p>
          </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-xl p-6 shadow-md border border-gray-200 mb-8">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">Filtrar reservaciones</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
              <label class="block text-sm font-medium text-gray-700 mb-2">Por disponibilidad</label>
              <div class="flex flex-wrap gap-2">
                <button
                  @click="selectedEstado = 'Todos'"
                  :class="['px-3 py-1 rounded-full text-sm font-medium transition-all', 
                    selectedEstado === 'Todos' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-blue-100']"
                >
                  Todos
                </button>
                <button
                  v-for="estado in estados"
                  :key="estado"
                  @click="selectedEstado = estado"
                  :class="['px-3 py-1 rounded-full text-sm font-medium transition-all', 
                    selectedEstado === estado ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-blue-100']"
                >
                  {{ estado }}
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
                  < $60
                </button>
                <button
                  @click="selectedPrecio = 'medio'"
                  :class="['px-3 py-1 rounded-full text-sm font-medium transition-all', 
                    selectedPrecio === 'medio' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-green-100']"
                >
                  $60 - $80
                </button>
                <button
                  @click="selectedPrecio = 'alto'"
                  :class="['px-3 py-1 rounded-full text-sm font-medium transition-all', 
                    selectedPrecio === 'alto' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-green-100']"
                >
                  > $80
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Reservaciones Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <Card
            v-for="reservacion in reservacionesFiltradas"
            :key="reservacion.id"
            class="border border-gray-300 bg-white shadow-md hover:shadow-lg transition-all duration-300 flex flex-col h-full transform hover:-translate-y-1"
            :class="{ 'opacity-75': reservacion.estado === 'Agotado' }"
          >
            <template #header>
              <div class="relative w-full h-48 bg-gradient-to-br from-gray-100 to-gray-200 rounded-t-lg overflow-hidden group">
                <div
                  v-if="reservacion.imagen"
                  class="object-cover h-full w-full group-hover:scale-110 transition-transform duration-500 bg-center bg-cover"
                  :style="{ backgroundImage: `url(${reservacion.imagen})` }"
                ></div>
                <div 
                  v-else
                  class="h-full w-full flex items-center justify-center bg-gradient-to-br from-gray-200 to-gray-300"
                >
                  <div class="text-center text-gray-500">
                    <i class="fas fa-calendar-alt text-4xl mb-2"></i>
                    <p class="text-sm font-medium">{{ reservacion.categoria }}</p>
                  </div>
                </div>
                <div class="absolute top-2 right-2 bg-green-600 text-white px-3 py-1 rounded-full text-sm font-semibold shadow-lg">
                  ${{ reservacion.precio }}
                </div>
                <div class="absolute bottom-2 left-2 bg-black/70 text-white px-2 py-1 rounded-full text-xs">
                  {{ reservacion.fecha_salida }}
                </div>
                <div class="absolute top-2 left-2 bg-purple-600 text-white px-2 py-1 rounded-full text-xs font-semibold">
                  {{ reservacion.categoria }}
                </div>
                <div class="absolute bottom-2 right-2" :class="{
                  'bg-green-500': reservacion.estado === 'Disponible',
                  'bg-yellow-500': reservacion.estado === 'Últimos cupos',
                  'bg-red-500': reservacion.estado === 'Agotado'
                }">
                  <span class="text-white px-2 py-1 rounded-full text-xs font-bold">
                    {{ reservacion.estado }}
                  </span>
                </div>
              </div>
            </template>
            
            <template #title>
              <span class="text-lg font-bold text-gray-800 leading-tight line-clamp-2">{{ reservacion.titulo }}</span>
            </template>
            
            <template #content>
              <div class="flex-grow space-y-3">
                <p class="text-gray-600 text-sm line-clamp-2">
                  {{ reservacion.descripcion }}
                </p>
                
                <!-- Información del tour -->
                <div class="space-y-2">
                  <div class="grid grid-cols-2 gap-2 text-xs text-gray-600">
                    <div class="flex items-center">
                      <span class="w-1 h-1 bg-blue-500 rounded-full mr-2"></span>
                      <strong>Destino:</strong>
                    </div>
                    <span class="text-xs">{{ reservacion.destino }}</span>
                    
                    <div class="flex items-center">
                      <span class="w-1 h-1 bg-green-500 rounded-full mr-2"></span>
                      <strong>Salida:</strong>
                    </div>
                    <span class="text-xs">{{ reservacion.hora_salida }}</span>
                    
                    <div class="flex items-center">
                      <span class="w-1 h-1 bg-purple-500 rounded-full mr-2"></span>
                      <strong>Duración:</strong>
                    </div>
                    <span class="text-xs">{{ reservacion.duracion }}</span>
                    
                    <div class="flex items-center">
                      <span class="w-1 h-1 bg-red-500 rounded-full mr-2"></span>
                      <strong>Cupos:</strong>
                    </div>
                    <span class="text-xs">{{ reservacion.cupos_disponibles }}/{{ reservacion.cupo_maximo }}</span>
                  </div>
                  
                  <div>
                    <p class="text-xs font-semibold text-gray-700 mb-1">Punto de salida:</p>
                    <p class="text-xs text-gray-600">{{ reservacion.punto_salida }}</p>
                  </div>
                  
                  <div>
                    <p class="text-xs font-semibold text-gray-700 mb-1">Incluye:</p>
                    <ul class="text-xs text-gray-600">
                      <li v-for="item in reservacion.incluye.slice(0, 2)" :key="item" class="flex items-center">
                        <span class="w-1 h-1 bg-green-500 rounded-full mr-2"></span>
                        {{ item }}
                      </li>
                      <li v-if="reservacion.incluye.length > 2" class="text-gray-400">
                        + {{ reservacion.incluye.length - 2 }} servicios más...
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </template>
            
            <template #footer>
              <div class="flex gap-2 mt-4">
                <Button
                  :label="reservacion.estado === 'Agotado' ? 'Agotado' : 'Reservar'"
                  @click="reservarAhora(reservacion)"
                  :disabled="reservacion.estado === 'Agotado'"
                  :class="[
                    '!border-none !px-3 !py-2 !text-sm font-semibold rounded transition-all flex-1 shadow-sm',
                    reservacion.estado === 'Agotado' 
                      ? '!bg-gray-400 !text-white cursor-not-allowed' 
                      : '!bg-red-600 !text-white hover:!bg-red-700'
                  ]"
                  size="small"
                />
                <Button
                  label="Info"
                  @click="verMasInfo(reservacion)"
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
          <h2 class="text-2xl font-bold text-red-700 mb-6 text-center">¿Cómo funciona el sistema de reservaciones?</h2>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center">
              <div class="text-4xl mb-3">🔍</div>
              <h3 class="font-semibold text-red-600 mb-2">1. Explora</h3>
              <p class="text-gray-600 text-sm">Revisa tours disponibles con fechas confirmadas</p>
            </div>
            <div class="text-center">
              <div class="text-4xl mb-3">📝</div>
              <h3 class="font-semibold text-red-600 mb-2">2. Reserva</h3>
              <p class="text-gray-600 text-sm">Asegura tu lugar con información detallada</p>
            </div>
            <div class="text-center">
              <div class="text-4xl mb-3">💳</div>
              <h3 class="font-semibold text-red-600 mb-2">3. Paga</h3>
              <p class="text-gray-600 text-sm">Confirma tu reservación con pago seguro</p>
            </div>
            <div class="text-center">
              <div class="text-4xl mb-3">🚌</div>
              <h3 class="font-semibold text-red-600 mb-2">4. Viaja</h3>
              <p class="text-gray-600 text-sm">Disfruta tu experiencia sin preocupaciones</p>
            </div>
          </div>
        </div>

        <!-- Mensaje de no resultados -->
        <div v-if="reservacionesFiltradas.length === 0" class="text-center py-12">
          <div class="text-6xl mb-4">📅</div>
          <h3 class="text-xl font-semibold text-gray-600 mb-2">No se encontraron reservaciones</h3>
          <p class="text-gray-500">Intenta con otros filtros o <button @click="selectedCategoria = 'Todos'; selectedEstado = 'Todos'; selectedPrecio = 'Todos'" class="text-red-600 hover:underline">ver todas las opciones</button></p>
        </div>

        <!-- CTA final -->
        <div class="mt-8 text-center">
          <span class="text-gray-600">¿Tienes dudas sobre las reservaciones? <a href="/contactos" class="text-red-600 hover:underline font-semibold">Contáctanos para más información</a></span>
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
