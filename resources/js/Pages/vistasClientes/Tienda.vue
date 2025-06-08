<script setup>
import Catalogo from '../Catalogo.vue'
import Card from 'primevue/card'
import Button from 'primevue/button'
import { ref, onMounted } from 'vue'
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

const placeholders = Array.from({ length: 6 })
</script>
<template>
  <Catalogo>
    <div class="p-8">
      <h1 class="text-2xl font-bold mb-4 text-red-700">Tienda</h1>
      <p class="mb-4 text-gray-700">Productos disponibles para la venta.</p>
      <div
        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 transition-all"
        :style="{ minHeight: loading ? '340px' : undefined }"
      >
        <template v-if="loading">
          <div v-for="(_, n) in placeholders" :key="n" class="animate-pulse bg-red-50 rounded h-72 border border-red-100"></div>
        </template>
        <template v-else>
          <Card
            v-for="{ id, nombre, descripcion, precio, imagenes } in products"
            :key="id"
            class="border border-red-200 bg-white shadow hover:shadow-lg transition-shadow duration-200 flex flex-col h-full"
          >
            <template #header>
              <div class="w-full flex justify-center items-center h-40 bg-red-50 rounded mb-2 overflow-hidden">
                <img
                  :src="imagenes && imagenes.length > 0 ? `/images/productos/${imagenes[0].nombre}` : '/storage/no-image.png'"
                  :alt="imagenes && imagenes.length > 0 ? nombre : 'Sin imagen'"
                  class="object-contain h-full max-h-36 w-auto"
                  :class="{ 'opacity-40': !imagenes || imagenes.length === 0 }"
                />
              </div>
            </template>
            <template #title>
              <span class="text-lg font-bold text-red-700">{{ nombre }}</span>
            </template>
            <template #content>
              <div class="text-gray-600 text-sm mb-2 min-h-[60px] max-h-[60px] overflow-hidden">
                {{ descripcion }}
              </div>
              <div class="text-red-700 text-base font-semibold mb-2">Precio: ${{ precio }}</div>
            </template>
            <template #footer>
              <Button
                label="Agregar al carrito"
                class="!bg-red-600 !border-none !px-4 !py-1 !text-white font-semibold rounded hover:!bg-red-700 transition w-full"
              />
            </template>
          </Card>
        </template>
      </div>
    </div>
  </Catalogo>
</template>
