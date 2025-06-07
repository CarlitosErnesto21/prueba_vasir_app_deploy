<script setup>
import Catalogo from '../Catalogo.vue'
import Card from 'primevue/card'
import Button from 'primevue/button'
import { ref, onMounted } from 'vue'
import axios from 'axios'

const products = ref([])

onMounted(async () => {
    try {
        const response = await axios.get('/api/productos')
        products.value = response.data
    } catch (error) {
        products.value = []
    }
})
</script>
<template>
  <Catalogo>
    <div class="p-8">
      <h1 class="text-2xl font-bold mb-4 text-red-700">Tienda</h1>
      <p class="mb-4 text-gray-700">Esta es la vista de Tienda. Aquí puedes mostrar los productos disponibles para la venta.</p>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        <Card
          v-for="producto in products"
          :key="producto.id"
          class="border border-red-200 bg-white shadow hover:shadow-lg transition-shadow duration-200"
        >
          <template #header>
            <div class="w-full flex justify-center items-center h-40 bg-red-50 rounded mb-2 overflow-hidden">
              <img
                v-if="producto.imagenes && producto.imagenes.length > 0"
                :src="`/images/productos/${producto.imagenes[0].nombre}`"
                alt="Producto"
                class="object-contain h-full max-h-36 w-auto"
              />
              <img
                v-else
                src="/storage/no-image.png"
                alt="Sin imagen"
                class="object-contain h-full max-h-36 w-auto opacity-40"
              />
            </div>
          </template>
          <template #title>
            <span class="text-lg font-bold text-red-700">{{ producto.nombre }}</span>
          </template>
          <template #content>
            <div class="text-gray-600 text-sm mb-2">{{ producto.descripcion }}</div>
            <div class="text-red-700 text-base font-semibold mb-2">Precio: ${{ producto.precio }}</div>
          </template>
          <template #footer>
            <Button
              label="Agregar al carrito"
              class="!bg-red-600 !border-none !px-4 !py-1 !text-white font-semibold rounded hover:!bg-red-700 transition w-full"
            />
          </template>
        </Card>
      </div>
    </div>
  </Catalogo>
</template>
