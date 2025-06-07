<script setup>
import Catalogo from '../Catalogo.vue'
import { ref } from 'vue'
import InputText from 'primevue/inputtext'
import Textarea from 'primevue/textarea'
import Button from 'primevue/button'

const nombre = ref('')
const email = ref('')
const mensaje = ref('')

function enviarFormulario(e) {
  e.preventDefault()
  // Aquí puedes manejar el envío real, por ejemplo, usando axios o fetch
  alert('Mensaje enviado correctamente')
  nombre.value = ''
  email.value = ''
  mensaje.value = ''
}
</script>

<template>
  <Catalogo>
    <div class="max-w-xl mx-auto bg-white rounded shadow-lg p-8 mt-12 border border-red-200">
      <h2 class="text-3xl font-bold mb-4 text-red-600">Contactos</h2>
      <p class="mb-6 text-gray-700">Puedes contactarnos a través del siguiente formulario o por nuestras redes sociales.</p>
      <form class="space-y-5" @submit="enviarFormulario">
        <div>
          <label class="block mb-1 font-semibold text-red-700">Nombre</label>
          <InputText v-model="nombre" class="w-full border border-red-300 focus:border-red-600 focus:ring-2 focus:ring-red-200 rounded px-3 py-2" placeholder="Tu nombre" />
        </div>
        <div>
          <label class="block mb-1 font-semibold text-red-700">Correo electrónico</label>
          <InputText v-model="email" type="email" class="w-full border border-red-300 focus:border-red-600 focus:ring-2 focus:ring-red-200 rounded px-3 py-2" placeholder="tu@email.com" />
        </div>
        <div>
          <label class="block mb-1 font-semibold text-red-700">Mensaje</label>
          <Textarea v-model="mensaje" class="w-full border border-red-300 focus:border-red-600 focus:ring-2 focus:ring-red-200 rounded px-3 py-2" rows="4" placeholder="Escribe tu mensaje" autoResize />
        </div>
        <Button type="submit" label="Enviar" class="!bg-red-600 !border-none !px-6 !py-2 !text-white font-semibold rounded shadow hover:!bg-red-700 transition" />
      </form>
    </div>
  </Catalogo>
</template>