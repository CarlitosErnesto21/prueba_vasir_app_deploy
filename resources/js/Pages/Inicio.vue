<script setup>
import Catalogo from './Catalogo.vue'
import Carousel from 'primevue/carousel'
import Card from 'primevue/card'
import Button from 'primevue/button'
import { Link, Head } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const products = ref([])
const loading = ref(true)

onMounted(async () => {
    try {
        const { data } = await axios.get('/api/productos')
        products.value = data
    } catch {
        products.value = []
    } finally {
        loading.value = false
    }
})

const slides = computed(() =>
    products.value.map(({ nombre, descripcion, imagenes }) => ({
        titulo: nombre,
        descripcion,
        imagen: imagenes?.[0]?.nombre || null
    }))
)
</script>

<template>
  <Catalogo>
    <div class="p-4 sm:p-6 md:p-8 max-w-7xl mx-auto bg-white rounded shadow min-h-[60vh] flex flex-col justify-center">
      <template v-if="loading">
        <div class="animate-pulse space-y-6">
          <div class="h-40 bg-red-50 rounded w-full"></div>
          <div class="h-10 bg-red-50 rounded w-2/3 mx-auto"></div>
          <div class="h-6 bg-red-50 rounded w-1/2 mx-auto"></div>
          <div class="h-12 bg-red-50 rounded w-1/3 mx-auto"></div>
        </div>
      </template>
      <template v-else>
        <div class="w-full">
          <Carousel
            :value="slides"
            :numVisible="1"
            :numScroll="1"
            circular
            :autoplayInterval="4000"
            class="mb-8 w-full"
            :responsiveOptions="[
              { breakpoint: '1024px', numVisible: 1, numScroll: 1 },
              { breakpoint: '768px', numVisible: 1, numScroll: 1 }
            ]"
          >
            <template #item="{ data }">
              <Card class="bg-white shadow-lg border border-red-200 w-full">
                <template #header>
                  <div class="w-full flex justify-center items-center h-40 sm:h-56 md:h-64 bg-red-50 rounded mb-2 overflow-hidden">
                    <img
                      :src="data.imagen ? `/images/productos/${data.imagen}` : '/storage/no-image.png'"
                      :alt="data.imagen ? data.titulo : 'Sin imagen'"
                      class="object-contain h-full max-h-36 sm:max-h-48 md:max-h-56 w-auto"
                      :class="{ 'opacity-40': !data.imagen }"
                    />
                  </div>
                </template>
                <template #title>
                  <h2 class="text-2xl sm:text-3xl font-bold text-red-700 mb-2 text-center break-words">{{ data.titulo }}</h2>
                </template>
                <template #content>
                  <p class="text-gray-700 text-center text-base sm:text-lg break-words">{{ data.descripcion }}</p>
                </template>
              </Card>
            </template>
          </Carousel>
        </div>
        <div class="text-center mt-8 px-2 sm:px-6">
          <h1 class="text-2xl sm:text-3xl font-bold text-red-700 mb-4">¡Bienvenido a Vasir!</h1>
          <p class="text-base sm:text-lg text-gray-700 mb-6">
            Descubre los mejores paquetes turísticos, tours y experiencias únicas en El Salvador. 
            Reserva fácil, seguro y rápido con nosotros.
          </p>
          <Link :href="route('paquetes')">
            <Button
              label="Ver Paquetes"
              class="!bg-red-600 !border-none !px-4 sm:!px-6 !py-2 sm:!py-3 !text-white font-semibold rounded hover:!bg-red-700 transition w-full sm:w-auto"
            />
          </Link>
        </div>
      </template>
    </div>
  </Catalogo>
</template>