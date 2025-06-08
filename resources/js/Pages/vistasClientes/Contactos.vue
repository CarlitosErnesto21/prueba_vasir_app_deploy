<script setup>
import Catalogo from '../Catalogo.vue'
import { ref, onMounted } from 'vue'
import InputText from 'primevue/inputtext'
import Textarea from 'primevue/textarea'
import Button from 'primevue/button'

const nombre = ref('')
const email = ref('')
const mensaje = ref('')
const loading = ref(true)

onMounted(() => setTimeout(() => { loading.value = false }, 600))

function enviarFormulario(e) {
  e.preventDefault()
  alert('Mensaje enviado correctamente')
  nombre.value = ''
  email.value = ''
  mensaje.value = ''
}

const placeholders = Array.from({ length: 6 })
</script>

<template>
  <Catalogo>
    <div class="max-w-xl mx-auto bg-white rounded shadow-lg p-8 border border-red-200">
      <template v-if="loading">
        <div class="animate-pulse space-y-6">
          <div v-for="(_, i) in placeholders" :key="i" :class="[
            'bg-red-50 rounded',
            i === 0 ? 'h-10 w-1/2' :
            i === 1 ? 'h-6 w-full' :
            i < 4 ? 'h-12 w-full' :
            i === 4 ? 'h-24 w-full' : 'h-10 w-1/3'
          ]"></div>
        </div>
      </template>
      <template v-else>
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
      </template>
    </div>
  </Catalogo>
</template>