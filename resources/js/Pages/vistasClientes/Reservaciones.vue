<script setup>
import Catalogo from '../Catalogo.vue'
import Card from 'primevue/card'
import Button from 'primevue/button'
</script>
<template>
  <Catalogo>
    <div class="p-8 max-w-3xl mx-auto bg-white rounded shadow">
      <h1 class="text-3xl font-bold mb-4 text-red-700">Reservaciones</h1>
      <p class="mb-6 text-gray-700">
        Reserva tus tours y paquetes de forma fácil y segura. Selecciona la opción que más te interese y completa el formulario.
      </p>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <Card class="bg-red-50 border border-red-200 rounded shadow flex flex-col items-center">
          <template #header>
            <img src="/imagenes/reserva1.jpg" alt="Tour de Aventura" class="w-full h-40 object-cover rounded-t mb-4" />
          </template>
          <template #title>
            <h2 class="text-xl font-bold text-red-700 mb-2">Tour de Aventura</h2>
          </template>
          <template #content>
            <p class="text-gray-700 mb-2">Reserva tu lugar en nuestro tour de aventura por volcanes y lagos. Incluye transporte y guía.</p>
            <span class="text-red-700 font-semibold mb-2 block">Desde $60</span>
          </template>
          <template #footer>
            <Button label="Reservar ahora" class="!bg-red-600 !border-none !px-4 !py-2 !text-white font-semibold rounded hover:!bg-red-700 transition w-full" />
          </template>
        </Card>
        <Card class="bg-red-50 border border-red-200 rounded shadow flex flex-col items-center">
          <template #header>
            <img src="/imagenes/reserva2.jpg" alt="Paquete Familiar" class="w-full h-40 object-cover rounded-t mb-4" />
          </template>
          <template #title>
            <h2 class="text-xl font-bold text-red-700 mb-2">Paquete Familiar</h2>
          </template>
          <template #content>
            <p class="text-gray-700 mb-2">Disfruta de un paquete especial para familias, con actividades y visitas guiadas a pueblos históricos.</p>
            <span class="text-red-700 font-semibold mb-2 block">Desde $80</span>
          </template>
          <template #footer>
            <Button label="Reservar ahora" class="!bg-red-600 !border-none !px-4 !py-2 !text-white font-semibold rounded hover:!bg-red-700 transition w-full" />
          </template>
        </Card>
      </div>
      <div class="mt-8 text-center">
        <span class="text-gray-600">¿Tienes dudas? <a href="/contactos" class="text-red-600 hover:underline">Contáctanos</a></span>
      </div>
    </div>
  </Catalogo>
</template>
