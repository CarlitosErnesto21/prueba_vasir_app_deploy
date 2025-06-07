<script setup>
import Catalogo from './Catalogo.vue'
import Carousel from 'primevue/carousel'
import Card from 'primevue/card'
import Button from 'primevue/button'
import { Link } from '@inertiajs/vue3'

const slides = [
  {
    titulo: 'Bienvenido a Vasir',
    descripcion: 'Explora los mejores destinos turísticos con nosotros.',
    imagen: '/imagenes/slider1.jpg'
  },
  {
    titulo: 'Paquetes exclusivos',
    descripcion: 'Aprovecha nuestras ofertas y vive experiencias únicas.',
    imagen: '/imagenes/slider2.jpg'
  },
  {
    titulo: 'Reserva fácil y rápido',
    descripcion: 'Reserva tus tours y paquetes en pocos pasos.',
    imagen: '/imagenes/slider3.jpg'
  }
]
</script>

<template>
  <Catalogo>
    <div class="mt-32">
      <div class="w-full">
        <Carousel :value="slides" :numVisible="1" :numScroll="1" circular :autoplayInterval="4000" class="mb-8 w-full">
          <template #item="slotProps">
            <Card class="bg-white shadow-lg border border-red-200 w-full">
              <template #header>
                <img :src="slotProps.data.imagen" alt="slide" class="w-full h-[400px] object-cover rounded-t" />
              </template>
              <template #title>
                <h2 class="text-3xl font-bold text-red-700 mb-2 text-center">{{ slotProps.data.titulo }}</h2>
              </template>
              <template #content>
                <p class="text-gray-700 text-center text-lg">{{ slotProps.data.descripcion }}</p>
              </template>
            </Card>
          </template>
        </Carousel>
      </div>
      <div class="text-center mt-8 px-6">
        <h1 class="text-3xl font-bold text-red-700 mb-4">¡Bienvenido a Vasir!</h1>
        <p class="text-lg text-gray-700 mb-6">
          Descubre los mejores paquetes turísticos, tours y experiencias únicas en El Salvador. 
          Reserva fácil, seguro y rápido con nosotros.
        </p>
        <Link :href="route('paquetes')">
          <Button
            label="Ver Paquetes"
            class="!bg-red-600 !border-none !px-6 !py-3 !text-white font-semibold rounded hover:!bg-red-700 transition"
          />
        </Link>
      </div>
    </div>
  </Catalogo>
</template>