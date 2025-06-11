<script setup>
import Catalogo from '../Catalogo.vue'
import Card from 'primevue/card'
import { ref, onMounted } from 'vue'

const loading = ref(true)
const showImagenAmpliada = ref(false)

function ampliarImagen() {
  showImagenAmpliada.value = true
}
function cerrarImagen() {
  showImagenAmpliada.value = false
}

onMounted(() => {
  setTimeout(() => { loading.value = false }, 600)
})
</script>
<template>
  <Catalogo>
    <div class="p-8 max-w-8xl mx-auto bg-white rounded shadow">
      <template v-if="loading">
        <div class="animate-pulse space-y-6">
          <div class="h-10 bg-red-50 rounded w-1/2"></div>
          <div class="h-6 bg-red-50 rounded w-full"></div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="h-40 bg-red-50 rounded"></div>
            <div class="h-40 bg-red-50 rounded"></div>
          </div>
          <div class="flex flex-col md:flex-row items-center gap-8 mt-8">
            <div class="h-40 w-full md:w-1/2 bg-red-50 rounded"></div>
            <div class="h-40 w-full bg-red-50 rounded"></div>
          </div>
        </div>
      </template>
      <template v-else>
        <h1 class="text-3xl font-bold mb-4 text-red-700">Sobre Nosotros</h1>
        <p class="mb-6 text-gray-700">
          Somos Vasir, una empresa dedicada a ofrecer experiencias turísticas únicas en El Salvador. Nuestro objetivo es que vivas aventuras inolvidables, descubras la riqueza natural y cultural de nuestro país y disfrutes de un servicio de calidad.
        </p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <Card class="bg-red-50 border border-red-200 rounded shadow">
            <template #title>
              <h2 class="text-xl font-semibold text-red-600 mb-2">Nuestra Misión</h2>
            </template>
            <template #content>
              <p class="text-gray-700">
                Brindar a nuestros clientes los mejores paquetes turísticos, tours y servicios, garantizando seguridad, comodidad y satisfacción en cada viaje.
              </p>
            </template>
          </Card>
          <Card class="bg-red-50 border border-red-200 rounded shadow">
            <template #title>
              <h2 class="text-xl font-semibold text-red-600 mb-2">Nuestra Visión</h2>
            </template>
            <template #content>
              <p class="text-gray-700">
                Ser la agencia líder en turismo en El Salvador, reconocida por la innovación, excelencia y compromiso con nuestros clientes y el medio ambiente.
              </p>
            </template>
          </Card>
        </div>
        <div class="flex flex-col md:flex-row items-center gap-8 mt-8">
          <img
            src="../../../../imagenes/Negocio.jpg"
            alt="Equipo Vasir"
            class="w-full md:w-1/3 rounded shadow border border-red-100 cursor-zoom-in hover:scale-105 transition-transform duration-300"
            @click="ampliarImagen"
          />
          <Card class="flex-1 bg-white border border-red-100 rounded shadow p-4">
            <template #title>
              <h3 class="text-lg font-bold text-red-700 mb-2">Nuestro equipo</h3>
            </template>
            <template #content>
              <p class="text-gray-700">
                Contamos con un equipo profesional y apasionado por el turismo, siempre dispuesto a ayudarte y a hacer de tu viaje una experiencia memorable.
              </p>
            </template>
          </Card>
        </div>
        <teleport to="body">
          <div
            v-if="showImagenAmpliada"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-80"
            @click.self="cerrarImagen"
          >
            <img
              src="../../../../imagenes/Negocio.jpg"
              alt="Equipo Vasir"
              class="max-w-3xl max-h-[90vh] rounded shadow-lg border-4 border-white"
            />
            <button
              class="absolute top-6 right-8 text-white text-3xl font-bold bg-black bg-opacity-50 rounded-full px-4 py-1 hover:bg-opacity-80"
              @click="cerrarImagen"
              aria-label="Cerrar"
            >&times;</button>
          </div>
        </teleport>
        <div class="mt-10 rounded overflow-hidden shadow border border-red-200">
          <p class="mb-2 text-gray-700 text-center">
            <span class="font-semibold text-red-600">Nuestra ubicación:&nbsp;</span>2a Calle Oriente casa #12, Chalatenango, El Salvador
          </p>
          <p class="mb-2 text-gray-700 text-center">
            <span class="font-semibold text-red-600">Horario de atención:&nbsp;</span>Lunes a Viernes 8:00 am - 4:00 pm, Sábado 8:00 am - 12:00 md
          </p>
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d241.91319397188843!2d-88.9363812!3d14.0410515!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8f636570efc5e09d%3A0xe884d67df04d7ff5!2sVASIR!5e0!3m2!1ses-419!2ssv!4v1749418509387!5m2!1ses-419!2ssv"
            width="100%"
            height="350"
            style="border:0;"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            title="Ubicación VASIR">
          </iframe>
        </div>
      </template>
    </div>
  </Catalogo>
</template>
