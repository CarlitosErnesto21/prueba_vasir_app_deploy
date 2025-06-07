<script setup>
import Catalogo from '../Catalogo.vue'
import Card from 'primevue/card'
import Button from 'primevue/button'
</script>
<template>
  <Catalogo>
    <div class="p-8 max-w-3xl mx-auto bg-white rounded shadow">
      <h1 class="text-3xl font-bold mb-4 text-red-700">Paquetes Turísticos</h1>
      <p class="mb-6 text-gray-700">
        Descubre nuestros paquetes turísticos diseñados para que vivas experiencias inolvidables en El Salvador. Elige el que más se adapte a tus gustos y necesidades.
      </p>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <Card class="bg-red-50 border border-red-200 rounded shadow flex flex-col items-center">
          <template #header>
            <img src="/imagenes/paquete1.jpg" alt="Paquete 1" class="w-full h-40 object-cover rounded-t mb-4" />
          </template>
          <template #title>
            <h2 class="text-xl font-bold text-red-700 mb-2">Aventura en la Naturaleza</h2>
          </template>
          <template #content>
            <p class="text-gray-700 mb-2">Explora volcanes, lagos y bosques con guías expertos. Incluye transporte, alimentación y actividades.</p>
            <span class="text-red-700 font-semibold mb-2 block">Desde $120</span>
          </template>
          <template #footer>
            <Button label="Ver detalles" class="!bg-red-600 !border-none !px-4 !py-2 !text-white font-semibold rounded hover:!bg-red-700 transition w-full" />
          </template>
        </Card>
        <Card class="bg-red-50 border border-red-200 rounded shadow flex flex-col items-center">
          <template #header>
            <img src="/imagenes/paquete2.jpg" alt="Paquete 2" class="w-full h-40 object-cover rounded-t mb-4" />
          </template>
          <template #title>
            <h2 class="text-xl font-bold text-red-700 mb-2">Cultura y Tradición</h2>
          </template>
          <template #content>
            <p class="text-gray-700 mb-2">Visita pueblos históricos, museos y disfruta de la gastronomía local. Ideal para familias y grupos.</p>
            <span class="text-red-700 font-semibold mb-2 block">Desde $95</span>
          </template>
          <template #footer>
            <Button label="Ver detalles" class="!bg-red-600 !border-none !px-4 !py-2 !text-white font-semibold rounded hover:!bg-red-700 transition w-full" />
          </template>
        </Card>
      </div>
      <div class="mt-8 text-center">
        <span class="text-gray-600">¿Buscas algo personalizado? <a href="/contactos" class="text-red-600 hover:underline">Contáctanos</a></span>
      </div>
    </div>
  </Catalogo>
</template>
