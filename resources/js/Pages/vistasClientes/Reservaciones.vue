<script setup>
import Catalogo from '../Catalogo.vue'
import Card from 'primevue/card'
import Button from 'primevue/button'
import { ref, onMounted } from 'vue'

const loading = ref(true)
const reservaciones = ref([])

onMounted(() => {
  setTimeout(() => {
    reservaciones.value = [
      {
        id: 1,
        titulo: 'Tour de Aventura',
        descripcion: 'Reserva tu lugar en nuestro tour de aventura por volcanes y lagos. Incluye transporte y guía.',
        precio: 60,
        imagen: '/imagenes/reserva1.jpg'
      },
      {
        id: 2,
        titulo: 'Paquete Familiar',
        descripcion: 'Disfruta de un paquete especial para familias, con actividades y visitas guiadas a pueblos históricos.',
        precio: 80,
        imagen: '/imagenes/reserva2.jpg'
      }
    ]
    loading.value = false
  }, 600)
})

const placeholders = Array.from({ length: 2 })
</script>
<template>
  <Catalogo>
    <div class="p-8 max-w-3xl mx-auto bg-white rounded shadow">
      <h1 class="text-3xl font-bold mb-4 text-red-700">Reservaciones</h1>
      <p class="mb-6 text-gray-700">
        Reserva tus tours y paquetes de forma fácil y segura. Selecciona la opción que más te interese y completa el formulario.
      </p>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <template v-if="loading">
          <div v-for="(_, n) in placeholders" :key="n" class="animate-pulse bg-red-50 rounded h-80 border border-red-100"></div>
        </template>
        <template v-else>
          <Card
            v-for="{ id, titulo, descripcion, precio, imagen } in reservaciones"
            :key="id"
            class="bg-red-50 border border-red-200 rounded shadow flex flex-col items-center"
          >
            <template #header>
              <img :src="imagen || '/storage/no-image.png'" alt="Reservación" class="w-full h-40 object-cover rounded-t mb-4" />
            </template>
            <template #title>
              <h2 class="text-xl font-bold text-red-700 mb-2">{{ titulo }}</h2>
            </template>
            <template #content>
              <p class="text-gray-700 mb-2">{{ descripcion }}</p>
              <span class="text-red-700 font-semibold mb-2 block">Desde ${{ precio }}</span>
            </template>
            <template #footer>
              <Button label="Reservar ahora" class="!bg-red-600 !border-none !px-4 !py-2 !text-white font-semibold rounded hover:!bg-red-700 transition w-full" />
            </template>
          </Card>
        </template>
      </div>
      <div class="mt-8 text-center">
        <span class="text-gray-600">¿Tienes dudas? <a href="/contactos" class="text-red-600 hover:underline">Contáctanos</a></span>
      </div>
    </div>
  </Catalogo>
</template>
