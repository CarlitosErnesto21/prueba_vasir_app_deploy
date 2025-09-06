<script setup>
import Catalogo from './Catalogo.vue'
import { Link, Head } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const products = ref([])
const loading = ref(true)

// Datos estáticos como respaldo si la API falla
const fallbackData = [
  {
    titulo: 'Tours Culturales El Salvador',
    descripcion: 'Descubre la rica historia y tradiciones de El Salvador visitando sitios arqueológicos, pueblos coloniales y museos fascinantes.',
    imagen: ''
  },
  {
    titulo: 'Aventuras en la Naturaleza',
    descripcion: 'Explora volcanes activos, lagos cristalinos y bosques tropicales en emocionantes expediciones llenas de adrenalina.',
    imagen: ''
  },
  {
    titulo: 'Experiencias Gastronómicas',
    descripcion: 'Deléitate con los sabores auténticos salvadoreños en tours culinarios que despertarán todos tus sentidos.',
    imagen: ''
  },
  {
    titulo: 'Playas Paradisíacas',
    descripcion: 'Relájate en las mejores playas del Pacífico con arenas negras volcánicas y perfectas olas para surfear.',
    imagen: ''
  }
]

// Estadísticas de la empresa
const estadisticas = ref([
  { numero: '500+', descripcion: 'Clientes Felices', icono: '😊' },
  { numero: '50+', descripcion: 'Destinos Únicos', icono: '📍' },
  { numero: '5+', descripcion: 'Años de Experiencia', icono: '⭐' },
  { numero: '100%', descripcion: 'Satisfacción Garantizada', icono: '🏆' }
])

// Servicios principales
const servicios = ref([
  {
    titulo: 'Tours Personalizados',
    descripcion: 'Diseñamos experiencias únicas adaptadas a tus intereses y presupuesto.',
    icono: '🎯',
    enlace: '/paquetes'
  },
  {
    titulo: 'Transporte Privado',
    descripcion: 'Servicio de transporte seguro y cómodo con choferes profesionales.',
    icono: '🚐',
    enlace: '/contactos'
  },
  {
    titulo: 'Reservaciones de Hotel',
    descripcion: 'Hospedaje en los mejores hoteles y resorts del país.',
    icono: '🏨',
    enlace: '/reservaciones'
  },
  {
    titulo: 'Trámites de Visa',
    descripcion: 'Asesoría completa para trámites de visa americana y otros países.',
    icono: '📋',
    enlace: '/contactos'
  }
])

onMounted(async () => {
    try {
        const { data } = await axios.get('/api/productos')
        products.value = data.length > 0 ? data : fallbackData
    } catch {
        products.value = fallbackData
    } finally {
        loading.value = false
    }
})

const slides = computed(() =>
    products.value.map(({ nombre, titulo, descripcion, imagenes, imagen }) => ({
        titulo: nombre || titulo,
        descripcion,
        imagen: imagenes?.[0]?.nombre || imagen || null
    }))
)
</script>

<template>
  <Catalogo>
    <div class="bg-gray-50 min-h-screen">
      <template v-if="loading">
        <div class="p-4 sm:p-6 md:p-8 max-w-7xl mx-auto">
          <div class="animate-pulse space-y-8">
            <!-- Hero loading -->
            <div class="h-96 bg-gradient-to-r from-red-100 to-red-200 rounded-xl"></div>
            <!-- Stats loading -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div v-for="i in 4" :key="i" class="h-24 bg-white rounded-lg"></div>
            </div>
            <!-- Content loading -->
            <div class="h-64 bg-white rounded-xl"></div>
          </div>
        </div>
      </template>
      
      <template v-else>
        <!-- Hero Section -->
        <section class="relative bg-gradient-to-br from-red-600 via-red-500 to-red-400 text-white py-16">
          <div class="absolute inset-0 bg-black opacity-20"></div>
          <div class="relative max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <div class="text-center mb-12">
              <h1 class="text-4xl md:text-6xl font-bold mb-4 leading-tight">
                ¡Bienvenido a <span class="text-yellow-300">VASIR</span>!
              </h1>
              <p class="text-xl md:text-2xl mb-8 opacity-90 max-w-3xl mx-auto">
                Tu puerta de entrada a las experiencias más increíbles de El Salvador
              </p>
              <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <Link :href="route('paquetes')">
                  <Button
                    label="🎒 Explorar Paquetes"
                    class="!bg-yellow-500 !border-none !px-6 !py-3 !text-black font-bold rounded-lg hover:!bg-yellow-400 transform hover:scale-105 transition-all duration-300 shadow-lg"
                  />
                </Link>
                <Link :href="route('tours-nacionales')">
                  <Button
                    label="🌋 Ver Tours"
                    outlined
                    class="!border-2 !border-white !text-white !px-6 !py-3 font-bold rounded-lg hover:!bg-white hover:!text-red-600 transform hover:scale-105 transition-all duration-300"
                  />
                </Link>
              </div>
            </div>
          </div>
        </section>

        <!-- Carousel de Productos -->
        <section class="py-16 bg-white">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <div class="text-center mb-12">
              <h2 class="text-3xl md:text-4xl font-bold text-red-700 mb-4">
                ✨ Experiencias Destacadas
              </h2>
              <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Descubre nuestras experiencias más populares diseñadas para crear recuerdos inolvidables
              </p>
            </div>
            
            <div class="w-full">
              <Carousel
                :value="slides"
                :numVisible="1"
                :numScroll="1"
                circular
                :autoplayInterval="5000"
                class="mb-8 w-full"
                :responsiveOptions="[
                  { breakpoint: '1024px', numVisible: 1, numScroll: 1 },
                  { breakpoint: '768px', numVisible: 1, numScroll: 1 }
                ]"
              >
                <template #item="{ data }">
                  <Card class="bg-white shadow-xl border border-gray-200 w-full mx-4 rounded-xl overflow-hidden transform hover:scale-105 transition-all duration-300">
                    <template #header>
                      <div class="relative w-full h-64 sm:h-80 md:h-96 bg-gradient-to-br from-red-100 to-red-200 overflow-hidden">
                        <div
                          v-if="data.imagen"
                          class="object-cover h-full w-full hover:scale-110 transition-transform duration-500 bg-center bg-cover"
                          :style="{ backgroundImage: `url(/images/productos/${data.imagen})` }"
                        ></div>
                        <div 
                          v-else
                          class="h-full w-full flex items-center justify-center bg-gradient-to-br from-red-200 to-red-300"
                        >
                          <div class="text-center text-red-600">
                            <i class="fas fa-map-marked-alt text-6xl mb-4"></i>
                            <p class="text-lg font-bold">VASIR</p>
                            <p class="text-sm">Experiencia Premium</p>
                          </div>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4">
                          <span class="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-semibold">Experiencia Premium</span>
                        </div>
                      </div>
                    </template>
                    <template #title>
                      <h3 class="text-2xl sm:text-3xl font-bold text-red-700 mb-3 text-center px-4">
                        {{ data.titulo }}
                      </h3>
                    </template>
                    <template #content>
                      <div class="px-4 pb-4">
                        <p class="text-gray-700 text-center text-base sm:text-lg leading-relaxed mb-6">
                          {{ data.descripcion }}
                        </p>
                        <div class="text-center">
                          <Link :href="route('paquetes')">
                            <Button
                              label="Más Información"
                              class="!bg-red-600 !border-none !px-6 !py-2 !text-white font-semibold rounded-lg hover:!bg-red-700 transition-colors shadow-md"
                            />
                          </Link>
                        </div>
                      </div>
                    </template>
                  </Card>
                </template>
              </Carousel>
            </div>
          </div>
        </section>

        <!-- Estadísticas -->
        <section class="py-16 bg-red-600 text-white">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <div class="text-center mb-12">
              <h2 class="text-3xl md:text-4xl font-bold mb-4">🌟 Nuestra Trayectoria</h2>
              <p class="text-xl opacity-90">Números que respaldan nuestra excelencia</p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
              <div 
                v-for="stat in estadisticas" 
                :key="stat.descripcion"
                class="text-center p-6 bg-white/10 rounded-xl backdrop-blur-sm hover:bg-white/20 transition-all duration-300 transform hover:-translate-y-1"
              >
                <div class="text-4xl mb-3">{{ stat.icono }}</div>
                <div class="text-3xl md:text-4xl font-bold mb-2">{{ stat.numero }}</div>
                <div class="text-sm md:text-base opacity-90">{{ stat.descripcion }}</div>
              </div>
            </div>
          </div>
        </section>

        <!-- Servicios -->
        <section class="py-16 bg-gray-50">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <div class="text-center mb-12">
              <h2 class="text-3xl md:text-4xl font-bold text-red-700 mb-4">🎯 Nuestros Servicios</h2>
              <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Ofrecemos una gama completa de servicios turísticos para hacer realidad tus sueños de viaje
              </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
              <div 
                v-for="servicio in servicios" 
                :key="servicio.titulo"
                class="bg-white rounded-xl p-6 shadow-md border border-gray-200 text-center hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300 group"
              >
                <div class="text-4xl mb-4 group-hover:scale-110 transition-transform duration-300">
                  {{ servicio.icono }}
                </div>
                <h3 class="text-xl font-bold text-red-700 mb-3">{{ servicio.titulo }}</h3>
                <p class="text-gray-600 mb-4 text-sm leading-relaxed">{{ servicio.descripcion }}</p>
                <Link :href="servicio.enlace">
                  <Button
                    label="Saber Más"
                    outlined
                    class="!border-red-600 !text-red-600 !px-4 !py-2 !text-sm font-semibold rounded-lg hover:!bg-red-600 hover:!text-white transition-all"
                    size="small"
                  />
                </Link>
              </div>
            </div>
          </div>
        </section>

        <!-- CTA Final -->
        <section class="py-16 bg-gradient-to-r from-red-600 to-red-500 text-white">
          <div class="max-w-4xl mx-auto px-4 sm:px-6 md:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">🚀 ¿Listo para tu próxima aventura?</h2>
            <p class="text-xl mb-8 opacity-90">
              Permítenos ser parte de tus mejores recuerdos. Contáctanos hoy mismo y comienza a planificar 
              la experiencia de viaje que siempre has soñado.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
              <Link :href="route('contactos')">
                <Button
                  label="📞 Contactar Ahora"
                  class="!bg-yellow-500 !border-none !px-8 !py-3 !text-black font-bold rounded-lg hover:!bg-yellow-400 transform hover:scale-105 transition-all duration-300 shadow-lg"
                />
              </Link>
              <Link :href="route('paquetes')">
                <Button
                  label="🎯 Ver Todos los Paquetes"
                  outlined
                  class="!border-2 !border-white !text-white !px-8 !py-3 font-bold rounded-lg hover:!bg-white hover:!text-red-600 transform hover:scale-105 transition-all duration-300"
                />
              </Link>
            </div>
          </div>
        </section>

        <!-- Nota informativa -->
        <section class="py-8 bg-blue-50 border-t-4 border-blue-400">
          <div class="max-w-4xl mx-auto px-4 sm:px-6 md:px-8 text-center">
            <div class="bg-white rounded-lg p-6 shadow-md border border-blue-200">
              <div class="text-2xl mb-2">ℹ️</div>
              <p class="text-gray-700 text-sm md:text-base">
                <strong>Nota:</strong> Los elementos que contiene estas vistas son de ejemplo y se actualizan dinámicamente. 
                Puedes ver nuestros productos reales en la sección de 
                <Link :href="route('tienda')" class="text-red-600 hover:underline font-semibold">Tienda</Link>.
              </p>
            </div>
          </div>
        </section>
      </template>
    </div>
  </Catalogo>
</template>