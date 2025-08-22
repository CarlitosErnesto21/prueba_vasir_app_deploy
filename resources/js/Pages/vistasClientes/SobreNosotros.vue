<script setup>
import Catalogo from '../Catalogo.vue'
import Card from 'primevue/card'
import { ref } from 'vue'

const props = defineProps({
    siteSettings: {
        type: Object,
        default: () => ({})
    }
});

const showImagenAmpliada = ref(false)

// Datos del equipo y estadísticas
const estadisticas = ref([
  { numero: '4+', descripcion: 'Años de experiencia', icono: '⏰' },
  { numero: '500+', descripcion: 'Clientes satisfechos', icono: '😊' },
  { numero: '50+', descripcion: 'Destinos visitados', icono: '📍' },
  { numero: '100%', descripcion: 'Compromiso y calidad', icono: '⭐' }
])

const valores = ref([
  {
    titulo: 'Excelencia',
    descripcion: 'Nos esforzamos por brindar el mejor servicio en cada experiencia.',
    icono: '🏆'
  },
  {
    titulo: 'Seguridad',
    descripcion: 'Tu seguridad es nuestra prioridad en cada aventura.',
    icono: '🛡️'
  },
  {
    titulo: 'Sostenibilidad',
    descripcion: 'Promovemos el turismo responsable y el cuidado del medio ambiente.',
    icono: '🌱'
  },
  {
    titulo: 'Innovación',
    descripcion: 'Utilizamos tecnología y métodos modernos para mejorar tu experiencia.',
    icono: '💡'
  }
])

const equipo = ref([
  {
    nombre: 'Carlos Ernesto',
    cargo: 'Director General',
    descripcion: 'Especialista en turismo con más de 5 años de experiencia en el sector.',
    imagen: '/images/equipo/carlos.jpg'
  },
  {
    nombre: 'María González',
    cargo: 'Coordinadora de Tours',
    descripcion: 'Experta en planificación de itinerarios y atención al cliente.',
    imagen: '/images/equipo/maria.jpg'
  },
  {
    nombre: 'Roberto Martínez',
    cargo: 'Guía Turístico Senior',
    descripcion: 'Conocedor profundo de la historia y cultura salvadoreña.',
    imagen: '/images/equipo/roberto.jpg'
  }
])

function ampliarImagen() {
  showImagenAmpliada.value = true
}

function cerrarImagen() {
  showImagenAmpliada.value = false
}
</script>
<template>
  <Catalogo>
    <div class="p-4 bg-gray-50 min-h-screen">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
          <h1 class="text-4xl font-bold mb-4 text-red-700">🏢 Sobre Nosotros</h1>
          <p class="text-xl text-gray-600 mb-4">Descubre la historia y pasión detrás de VASIR</p>
          <p class="text-lg text-gray-700 max-w-3xl mx-auto leading-relaxed">
            Somos VASIR, una empresa dedicada a ofrecer experiencias turísticas únicas en El Salvador. 
            Nuestro objetivo es que vivas aventuras inolvidables, descubras la riqueza natural y cultural 
            de nuestro país y disfrutes de un servicio de calidad excepcional.
          </p>
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
          <div
            v-for="stat in estadisticas"
            :key="stat.descripcion"
            class="bg-white rounded-xl p-6 shadow-md border border-gray-200 text-center transform hover:-translate-y-1 transition-all duration-300"
          >
            <div class="text-4xl mb-3">{{ stat.icono }}</div>
            <h3 class="text-3xl font-bold text-red-600 mb-2">{{ stat.numero }}</h3>
            <p class="text-gray-600 font-medium">{{ stat.descripcion }}</p>
          </div>
        </div>

        <!-- Misión y Visión -->
        <div v-if="siteSettings.mission || siteSettings.vision" class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
          <Card v-if="siteSettings.mission" class="border border-gray-300 bg-white shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
            <template #title>
              <div class="flex items-center mb-4">
                <span class="text-3xl mr-3">🎯</span>
                <h2 class="text-2xl font-bold text-red-700">Nuestra Misión</h2>
              </div>
            </template>
            <template #content>
              <p class="text-gray-700 leading-relaxed text-lg">
                {{ siteSettings.mission }}
              </p>
            </template>
          </Card>

          <Card v-if="siteSettings.vision" class="border border-gray-300 bg-white shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
            <template #title>
              <div class="flex items-center mb-4">
                <span class="text-3xl mr-3">🌟</span>
                <h2 class="text-2xl font-bold text-red-700">Nuestra Visión</h2>
              </div>
            </template>
            <template #content>
              <p class="text-gray-700 leading-relaxed text-lg">
                {{ siteSettings.vision }}
              </p>
            </template>
          </Card>
        </div>

        <!-- Mensaje cuando no hay contenido configurado -->
        <div v-if="!siteSettings.mission && !siteSettings.vision" class="text-center py-12 mb-12">
          <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-8 max-w-2xl mx-auto">
            <div class="text-6xl mb-4">⚙️</div>
            <h3 class="text-xl font-semibold text-yellow-800 mb-2">Contenido en configuración</h3>
            <p class="text-yellow-700">
              La misión y visión de la empresa están siendo configuradas por nuestro equipo.
            </p>
          </div>
        </div>        <!-- Nuestros Valores -->
        <div class="mb-12">
          <h2 class="text-3xl font-bold text-red-700 text-center mb-8">💎 Nuestros Valores</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div
              v-for="valor in valores"
              :key="valor.titulo"
              class="bg-white rounded-xl p-6 shadow-md border border-gray-200 text-center hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1"
            >
              <div class="text-4xl mb-4">{{ valor.icono }}</div>
              <h3 class="text-xl font-bold text-red-600 mb-3">{{ valor.titulo }}</h3>
              <p class="text-gray-600 text-sm leading-relaxed">{{ valor.descripcion }}</p>
            </div>
          </div>
        </div>

        <!-- Nuestro Equipo -->
        <div class="mb-12">
          <h2 class="text-3xl font-bold text-red-700 text-center mb-8">👥 Conoce Nuestro Equipo</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <div
              v-for="miembro in equipo"
              :key="miembro.nombre"
              class="bg-white rounded-xl p-6 shadow-md border border-gray-200 text-center hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1"
            >
              <div class="w-24 h-24 bg-gradient-to-br from-red-400 to-red-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                <span class="text-white text-2xl font-bold">{{ miembro.nombre.charAt(0) }}</span>
              </div>
              <h3 class="text-xl font-bold text-red-700 mb-1">{{ miembro.nombre }}</h3>
              <p class="text-red-500 font-semibold mb-3">{{ miembro.cargo }}</p>
              <p class="text-gray-600 text-sm leading-relaxed">{{ miembro.descripcion }}</p>
            </div>
          </div>

          <!-- Foto del negocio -->
          <div class="bg-white rounded-xl p-8 shadow-md border border-gray-200">
            <div class="flex flex-col lg:flex-row items-center gap-8">
              <div class="lg:w-1/2">
                <img
                  src="../../../../imagenes/Negocio.jpg"
                  alt="Oficinas VASIR"
                  class="w-full rounded-lg shadow-md border border-red-100 cursor-zoom-in hover:scale-105 transition-transform duration-300"
                  @click="ampliarImagen"
                />
              </div>
              <div class="lg:w-1/2">
                <h3 class="text-2xl font-bold text-red-700 mb-4">🏢 Nuestras Instalaciones</h3>
                <p class="text-gray-700 leading-relaxed mb-4">
                  Contamos con modernas instalaciones ubicadas en el corazón de Chalatenango, 
                  diseñadas para brindarte el mejor servicio y atención personalizada.
                </p>
                <p class="text-gray-700 leading-relaxed">
                  Nuestro equipo profesional y apasionado por el turismo está siempre dispuesto 
                  a ayudarte y a hacer de tu viaje una experiencia memorable que atesorarás para siempre.
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Por qué elegirnos -->
        <div class="bg-white rounded-xl p-8 shadow-md border border-gray-200 mb-12">
          <h2 class="text-3xl font-bold text-red-700 text-center mb-8">🌟 ¿Por qué elegir VASIR?</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
              <div class="text-4xl mb-4">🎖️</div>
              <h3 class="text-xl font-bold text-red-600 mb-3">Experiencia Comprobada</h3>
              <p class="text-gray-600">Más de 5 años creando experiencias turísticas únicas y memorables.</p>
            </div>
            <div class="text-center">
              <div class="text-4xl mb-4">🤝</div>
              <h3 class="text-xl font-bold text-red-600 mb-3">Atención Personalizada</h3>
              <p class="text-gray-600">Cada cliente es único, por eso adaptamos nuestros servicios a tus necesidades.</p>
            </div>
            <div class="text-center">
              <div class="text-4xl mb-4">🌍</div>
              <h3 class="text-xl font-bold text-red-600 mb-3">Turismo Responsable</h3>
              <p class="text-gray-600">Comprometidos con el desarrollo sostenible y el cuidado del medio ambiente.</p>
            </div>
          </div>
        </div>

        <!-- Información de contacto y ubicación -->
        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
          <div class="p-8">
            <h2 class="text-3xl font-bold text-red-700 text-center mb-8">📍 Encuéntranos</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
              <div class="space-y-4">
                <div class="flex items-center">
                  <span class="text-2xl mr-3">📍</span>
                  <div>
                    <h3 class="font-semibold text-red-600">Dirección</h3>
                    <p class="text-gray-700">2a Calle Oriente casa #12, Chalatenango, El Salvador</p>
                  </div>
                </div>
                <div class="flex items-center">
                  <span class="text-2xl mr-3">⏰</span>
                  <div>
                    <h3 class="font-semibold text-red-600">Horario de Atención</h3>
                    <p class="text-gray-700">Lunes a Viernes: 8:00 AM - 4:00 PM</p>
                    <p class="text-gray-700">Sábados: 8:00 AM - 12:00 PM</p>
                  </div>
                </div>
                <div class="flex items-center">
                  <span class="text-2xl mr-3">📞</span>
                  <div>
                    <h3 class="font-semibold text-red-600">Contacto</h3>
                    <p class="text-gray-700">¿Listo para tu próxima aventura?</p>
                    <a href="/contactos" class="text-red-600 hover:underline font-semibold">Contáctanos aquí</a>
                  </div>
                </div>
              </div>
              <div class="bg-red-50 rounded-lg p-6 border border-red-200">
                <h3 class="text-xl font-bold text-red-700 mb-4">🎉 ¡Únete a la familia VASIR!</h3>
                <p class="text-gray-700 mb-4">
                  Más que una agencia de viajes, somos tu compañero de aventuras. 
                  Permítenos ser parte de tus mejores recuerdos.
                </p>
                <a 
                  href="/paquetes" 
                  class="inline-block bg-red-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors shadow-md"
                >
                  Ver Nuestros Paquetes
                </a>
              </div>
            </div>
          </div>
          
          <!-- Mapa -->
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d241.91319397188843!2d-88.9363812!3d14.0410515!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8f636570efc5e09d%3A0xe884d67df04d7ff5!2sVASIR!5e0!3m2!1ses-419!2ssv!4v1749418509387!5m2!1ses-419!2ssv"
            width="100%"
            height="400"
            style="border:0;"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            title="Ubicación VASIR"
            class="w-full"
          >
          </iframe>
        </div>

        <!-- Modal de imagen ampliada -->
        <teleport to="body">
          <div
            v-if="showImagenAmpliada"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-80 p-4"
            @click.self="cerrarImagen"
          >
            <div class="relative max-w-4xl max-h-[90vh]">
              <img
                src="../../../../imagenes/Negocio.jpg"
                alt="Oficinas VASIR"
                class="max-w-full max-h-full rounded-lg shadow-2xl border-4 border-white"
              />
              <button
                class="absolute -top-4 -right-4 text-white text-2xl font-bold bg-red-600 rounded-full w-10 h-10 flex items-center justify-center hover:bg-red-700 transition-colors shadow-lg"
                @click="cerrarImagen"
                aria-label="Cerrar"
              >
                ✕
              </button>
            </div>
          </div>
        </teleport>
      </div>
    </div>
  </Catalogo>
</template>
