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
    <div class="bg-gradient-to-br from-gray-50 via-blue-50/30 to-red-50/30 min-h-screen">
      <template v-if="loading">
        <div class="p-4 sm:p-6 md:p-8 max-w-7xl mx-auto">
          <div class="animate-pulse space-y-8">
            <!-- Hero loading -->
            <div class="h-96 bg-gradient-to-r from-red-100 via-red-50 to-red-100 rounded-2xl shadow-lg"></div>
            <!-- Stats loading -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
              <div v-for="i in 4" :key="i" class="h-28 bg-white/80 backdrop-blur-sm rounded-xl shadow-md border border-white/20"></div>
            </div>
            <!-- Content loading -->
            <div class="h-72 bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20"></div>
          </div>
        </div>
      </template>
      
      <template v-else>
        <!-- Hero Section -->
        <section class="relative bg-gradient-to-br from-red-600 via-red-500 to-blue-600 text-white py-12 sm:py-16 md:py-20 lg:py-24 overflow-hidden">
          <div class="absolute inset-0 bg-gradient-to-r from-black/30 via-transparent to-black/30"></div>
          <div class="absolute top-0 left-0 w-full h-full">
            <div class="absolute top-5 sm:top-10 left-5 sm:left-10 w-20 h-20 sm:w-32 sm:h-32 bg-white/10 rounded-full blur-xl"></div>
            <div class="absolute top-20 sm:top-40 right-10 sm:right-20 w-32 h-32 sm:w-48 sm:h-48 bg-yellow-400/20 rounded-full blur-2xl"></div>
            <div class="absolute bottom-10 sm:bottom-20 left-1/4 sm:left-1/3 w-40 h-40 sm:w-64 sm:h-64 bg-blue-400/15 rounded-full blur-2xl"></div>
          </div>
          <div class="relative max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
            <div class="text-center mb-8 sm:mb-12 md:mb-16">
              <div class="inline-block mb-4 sm:mb-6">
                <div class="bg-gradient-to-r from-yellow-400 to-yellow-300 text-red-900 px-3 py-1.5 sm:px-6 sm:py-2 rounded-full font-bold text-xs sm:text-sm shadow-lg">
                  ✨ Experiencias Únicas en El Salvador
                </div>
              </div>
              <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold mb-4 sm:mb-6 leading-tight">
                ¡Bienvenido a <span class="bg-gradient-to-r from-yellow-300 to-yellow-400 bg-clip-text text-transparent">VASIR</span>!
              </h1>
              <p class="text-base sm:text-lg md:text-xl lg:text-2xl mb-6 sm:mb-8 md:mb-10 opacity-90 max-w-sm sm:max-w-2xl md:max-w-3xl lg:max-w-4xl mx-auto leading-relaxed px-4 sm:px-0">
                Tu puerta de entrada a las experiencias más increíbles de El Salvador. 
                Descubre paisajes únicos, cultura vibrante y aventuras inolvidables.
              </p>
              <div class="flex flex-col sm:flex-row gap-4 sm:gap-6 justify-center items-center px-4 sm:px-0">
                <Link :href="route('paquetes')">
                  <Button
                    label="🎒 Explorar Paquetes"
                    class="w-full sm:w-auto !bg-gradient-to-r !from-yellow-500 !to-yellow-400 !border-none !px-6 sm:!px-8 !py-3 sm:!py-4 !text-black font-bold rounded-xl hover:!from-yellow-400 hover:!to-yellow-300 transform hover:scale-105 transition-all duration-300 shadow-xl hover:shadow-2xl text-sm sm:text-base"
                  />
                </Link>
                <Link :href="route('tours-nacionales')">
                  <Button
                    label="🌋 Ver Tours"
                    outlined
                    class="w-full sm:w-auto !border-2 !border-white/80 !text-white !px-6 sm:!px-8 !py-3 sm:!py-4 font-bold rounded-xl hover:!bg-white/20 hover:!border-white backdrop-blur-sm transform hover:scale-105 transition-all duration-300 shadow-lg text-sm sm:text-base"
                  />
                </Link>
              </div>
            </div>
          </div>
        </section>

        <!-- Carousel de Productos -->
        <section class="py-12 sm:py-16 md:py-20 lg:py-24 bg-gradient-to-br from-white via-blue-50/50 to-red-50/50 relative">
          <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
          <div class="relative max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
            <div class="text-center mb-8 sm:mb-12 md:mb-16">
              <div class="inline-block mb-3 sm:mb-4">
                <span class="bg-gradient-to-r from-red-600 to-red-500 text-white px-3 py-1.5 sm:px-4 sm:py-2 rounded-full font-semibold text-xs sm:text-sm shadow-lg">
                  Destacados
                </span>
              </div>
              <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold bg-gradient-to-r from-red-700 to-red-600 bg-clip-text text-transparent mb-4 sm:mb-6">
                ✨ Experiencias Destacadas
              </h2>
              <p class="text-sm sm:text-base md:text-lg text-gray-600 max-w-sm sm:max-w-xl md:max-w-2xl lg:max-w-3xl mx-auto leading-relaxed px-4 sm:px-0">
                Descubre nuestras experiencias más populares, cuidadosamente diseñadas para crear 
                recuerdos inolvidables que atesorarás para siempre.
              </p>
            </div>
            
            <div class="w-full px-2 sm:px-0">
              <Carousel
                :value="slides"
                :numVisible="1"
                :numScroll="1"
                circular
                :autoplayInterval="6000"
                class="mb-6 sm:mb-8 w-full"
                :responsiveOptions="[
                  { breakpoint: '1400px', numVisible: 1, numScroll: 1 },
                  { breakpoint: '1024px', numVisible: 1, numScroll: 1 },
                  { breakpoint: '768px', numVisible: 1, numScroll: 1 },
                  { breakpoint: '640px', numVisible: 1, numScroll: 1 }
                ]"
              >
                <template #item="{ data }">
                  <Card class="bg-white/80 backdrop-blur-sm shadow-lg sm:shadow-xl md:shadow-2xl border border-white/40 w-full mx-1 sm:mx-2 md:mx-4 rounded-xl sm:rounded-2xl overflow-hidden transform hover:scale-105 transition-all duration-500 hover:shadow-3xl">
                    <template #header>
                      <div class="relative w-full h-48 sm:h-64 md:h-72 lg:h-80 xl:h-96 overflow-hidden">
                        <div
                          v-if="data.imagen"
                          class="object-cover h-full w-full hover:scale-110 transition-transform duration-700 bg-center bg-cover"
                          :style="{ backgroundImage: `url(/images/productos/${data.imagen})` }"
                        ></div>
                        <div 
                          v-else
                          class="h-full w-full flex items-center justify-center bg-gradient-to-br from-red-200 via-red-100 to-blue-100"
                        >
                          <div class="text-center text-red-600">
                            <i class="fas fa-map-marked-alt text-4xl sm:text-6xl md:text-8xl mb-3 sm:mb-4 md:mb-6 opacity-80"></i>
                            <p class="text-lg sm:text-xl md:text-2xl font-bold mb-1 sm:mb-2">VASIR</p>
                            <p class="text-sm sm:text-base md:text-lg bg-gradient-to-r from-red-600 to-red-500 bg-clip-text text-transparent font-semibold">Experiencia Premium</p>
                          </div>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                        <div class="absolute bottom-3 sm:bottom-4 md:bottom-6 left-3 sm:left-4 md:left-6 right-3 sm:right-4 md:right-6">
                          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-0">
                            <span class="bg-gradient-to-r from-red-600 to-red-500 text-white px-2 py-1 sm:px-3 sm:py-1.5 md:px-4 md:py-2 rounded-full text-xs sm:text-sm font-semibold shadow-lg backdrop-blur-sm inline-block">
                              ⭐ Experiencia Premium
                            </span>
                            <div class="bg-white/20 backdrop-blur-sm rounded-full px-2 py-0.5 sm:px-3 sm:py-1 self-start sm:self-auto">
                              <span class="text-white text-xs font-medium">Populares</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </template>
                    <template #title>
                      <h3 class="text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold bg-gradient-to-r from-red-700 to-red-600 bg-clip-text text-transparent mb-3 sm:mb-4 text-center px-3 sm:px-4 md:px-6 leading-tight">
                        {{ data.titulo }}
                      </h3>
                    </template>
                    <template #content>
                      <div class="px-3 sm:px-4 md:px-6 pb-4 sm:pb-6">
                        <p class="text-gray-700 text-center text-sm sm:text-base md:text-lg leading-relaxed mb-4 sm:mb-6 md:mb-8 opacity-90">
                          {{ data.descripcion }}
                        </p>
                        <div class="text-center">
                          <Link :href="route('paquetes')">
                            <Button
                              label="Descubrir Más →"
                              class="w-full sm:w-auto !bg-gradient-to-r !from-red-600 !to-red-500 !border-none !px-4 sm:!px-6 md:!px-8 !py-2.5 sm:!py-3 !text-white font-semibold rounded-lg sm:rounded-xl hover:!from-red-700 hover:!to-red-600 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 text-sm sm:text-base"
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
        <section class="py-12 sm:py-16 md:py-20 lg:py-24 bg-gradient-to-br from-red-600 via-red-500 to-blue-600 text-white relative overflow-hidden">
          <div class="absolute inset-0 bg-gradient-to-r from-black/20 via-transparent to-black/20"></div>
          <div class="absolute top-0 left-0 w-full h-full">
            <div class="absolute top-5 sm:top-10 right-5 sm:right-10 w-24 h-24 sm:w-40 sm:h-40 bg-white/10 rounded-full blur-2xl"></div>
            <div class="absolute bottom-10 sm:bottom-20 left-10 sm:left-20 w-32 h-32 sm:w-56 sm:h-56 bg-yellow-400/15 rounded-full blur-3xl"></div>
          </div>
          <div class="relative max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
            <div class="text-center mb-8 sm:mb-12 md:mb-16">
              <div class="inline-block mb-3 sm:mb-4">
                <span class="bg-white/20 backdrop-blur-sm text-white px-3 py-1.5 sm:px-4 sm:py-2 rounded-full font-semibold text-xs sm:text-sm shadow-lg">
                  Nuestra Experiencia
                </span>
              </div>
              <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold mb-4 sm:mb-6">
                🌟 Nuestra Trayectoria
              </h2>
              <p class="text-sm sm:text-base md:text-lg lg:text-xl opacity-90 max-w-sm sm:max-w-xl md:max-w-2xl lg:max-w-3xl mx-auto leading-relaxed px-4 sm:px-0">
                Números que respaldan nuestra excelencia y compromiso con experiencias inolvidables
              </p>
            </div>
            
            <div class="grid grid-cols-1 xs:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 md:gap-8">
              <div 
                v-for="stat in estadisticas" 
                :key="stat.descripcion"
                class="text-center p-4 sm:p-6 md:p-8 bg-white/15 backdrop-blur-sm rounded-xl sm:rounded-2xl border border-white/20 hover:bg-white/25 transition-all duration-300 transform hover:-translate-y-1 sm:hover:-translate-y-2 hover:scale-105 group shadow-lg sm:shadow-xl"
              >
                <div class="text-3xl sm:text-4xl md:text-5xl mb-2 sm:mb-3 md:mb-4 group-hover:scale-110 transition-transform duration-300">{{ stat.icono }}</div>
                <div class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold mb-2 sm:mb-3 bg-gradient-to-r from-yellow-300 to-yellow-400 bg-clip-text text-transparent">{{ stat.numero }}</div>
                <div class="text-xs sm:text-sm md:text-base opacity-90 font-medium px-1">{{ stat.descripcion }}</div>
              </div>
            </div>
          </div>
        </section>

        <!-- Servicios -->
        <section class="py-12 sm:py-16 md:py-20 lg:py-24 bg-gradient-to-br from-gray-50 via-blue-50/40 to-red-50/40 relative">
          <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent"></div>
          <div class="relative max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
            <div class="text-center mb-8 sm:mb-12 md:mb-16">
              <div class="inline-block mb-3 sm:mb-4">
                <span class="bg-gradient-to-r from-red-600 to-red-500 text-white px-3 py-1.5 sm:px-4 sm:py-2 rounded-full font-semibold text-xs sm:text-sm shadow-lg">
                  Servicios Premium
                </span>
              </div>
              <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold bg-gradient-to-r from-red-700 to-red-600 bg-clip-text text-transparent mb-4 sm:mb-6">
                🎯 Nuestros Servicios
              </h2>
              <p class="text-sm sm:text-base md:text-lg text-gray-600 max-w-sm sm:max-w-xl md:max-w-2xl lg:max-w-3xl mx-auto leading-relaxed px-4 sm:px-0">
                Ofrecemos una gama completa de servicios turísticos profesionales para hacer realidad 
                tus sueños de viaje con la más alta calidad y atención personalizada.
              </p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 md:gap-8">
              <div 
                v-for="servicio in servicios" 
                :key="servicio.titulo"
                class="bg-white/80 backdrop-blur-sm rounded-xl sm:rounded-2xl p-4 sm:p-6 md:p-8 shadow-lg sm:shadow-xl border border-white/40 text-center hover:shadow-2xl transform hover:-translate-y-1 sm:hover:-translate-y-2 transition-all duration-300 group hover:bg-white/90"
              >
                <div class="text-3xl sm:text-4xl md:text-5xl mb-3 sm:mb-4 md:mb-6 group-hover:scale-110 transition-transform duration-300 group-hover:animate-bounce">
                  {{ servicio.icono }}
                </div>
                <h3 class="text-base sm:text-lg md:text-xl font-bold bg-gradient-to-r from-red-700 to-red-600 bg-clip-text text-transparent mb-2 sm:mb-3 md:mb-4 leading-tight">{{ servicio.titulo }}</h3>
                <p class="text-gray-600 mb-4 sm:mb-5 md:mb-6 text-xs sm:text-sm leading-relaxed opacity-90">{{ servicio.descripcion }}</p>
                <Link :href="servicio.enlace">
                  <Button
                    label="Descubrir →"
                    outlined
                    class="w-full sm:w-auto !border-2 !border-red-600 !text-red-600 !px-4 sm:!px-6 !py-1.5 sm:!py-2 !text-xs sm:!text-sm font-semibold rounded-lg sm:rounded-xl hover:!bg-red-600 hover:!text-white transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-105"
                    size="small"
                  />
                </Link>
              </div>
            </div>
          </div>
        </section>

        <!-- CTA Final -->
        <section class="py-12 sm:py-16 md:py-20 lg:py-24 bg-gradient-to-br from-red-600 via-red-500 to-blue-600 text-white relative overflow-hidden">
          <div class="absolute inset-0 bg-gradient-to-r from-black/20 via-transparent to-black/20"></div>
          <div class="absolute top-0 left-0 w-full h-full">
            <div class="absolute top-10 sm:top-20 left-5 sm:left-10 w-20 h-20 sm:w-32 sm:h-32 bg-yellow-400/20 rounded-full blur-2xl"></div>
            <div class="absolute bottom-5 sm:bottom-10 right-10 sm:right-20 w-32 h-32 sm:w-48 sm:h-48 bg-white/10 rounded-full blur-3xl"></div>
          </div>
          <div class="relative max-w-3xl sm:max-w-4xl lg:max-w-5xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8 text-center">
            <div class="mb-6 sm:mb-8">
              <span class="bg-white/20 backdrop-blur-sm text-white px-3 py-1.5 sm:px-4 sm:py-2 rounded-full font-semibold text-xs sm:text-sm shadow-lg">
                ¡Comienza Tu Aventura!
              </span>
            </div>
            <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-bold mb-4 sm:mb-6 leading-tight">
              🚀 ¿Listo para tu próxima aventura?
            </h2>
            <p class="text-sm sm:text-base md:text-lg lg:text-xl xl:text-2xl mb-8 sm:mb-10 md:mb-12 opacity-90 max-w-xs sm:max-w-2xl md:max-w-3xl lg:max-w-4xl mx-auto leading-relaxed px-4 sm:px-0">
              Permítenos ser parte de tus mejores recuerdos. Contáctanos hoy mismo y comienza a planificar 
              la experiencia de viaje que siempre has soñado. Tu próxima gran aventura te está esperando.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 sm:gap-6 justify-center items-center px-4 sm:px-0">
              <Link :href="route('contactos')">
                <Button
                  label="📞 Contactar Ahora"
                  class="w-full sm:w-auto !bg-gradient-to-r !from-yellow-500 !to-yellow-400 !border-none !px-6 sm:!px-8 md:!px-10 !py-3 sm:!py-4 !text-black font-bold rounded-xl hover:!from-yellow-400 hover:!to-yellow-300 transform hover:scale-105 transition-all duration-300 shadow-xl hover:shadow-2xl text-sm sm:text-base"
                />
              </Link>
              <Link :href="route('paquetes')">
                <Button
                  label="🎯 Ver Todos los Paquetes"
                  outlined
                  class="w-full sm:w-auto !border-2 !border-white/80 !text-white !px-6 sm:!px-8 md:!px-10 !py-3 sm:!py-4 font-bold rounded-xl hover:!bg-white/20 hover:!border-white backdrop-blur-sm transform hover:scale-105 transition-all duration-300 shadow-lg text-sm sm:text-base"
                />
              </Link>
            </div>
          </div>
        </section>
      </template>
    </div>
  </Catalogo>
</template>