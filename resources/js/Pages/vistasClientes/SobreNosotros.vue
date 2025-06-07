<script setup>
import Catalogo from '../Catalogo.vue'
import Card from 'primevue/card'
import { ref, onMounted } from 'vue'

const loading = ref(true)

onMounted(() => {
  setTimeout(() => { loading.value = false }, 600)
})
</script>
<template>
  <Catalogo>
    <div class="p-8 max-w-3xl mx-auto bg-white rounded shadow">
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
          <img src="/imagenes/equipo.jpg" alt="Equipo Vasir" class="w-full md:w-1/2 rounded shadow border border-red-100" />
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
      </template>
    </div>
  </Catalogo>
</template>
