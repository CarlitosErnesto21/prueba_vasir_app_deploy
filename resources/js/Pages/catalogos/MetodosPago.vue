<template>
  <div class="max-w-xl mx-auto mt-10 bg-white rounded-lg shadow p-6">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Catálogo de Métodos de Pago</h2>
    <form @submit.prevent="agregarMetodo" class="flex gap-2 mb-6">
      <input
        v-model="nuevoMetodo"
        type="text"
        placeholder="Nuevo método de pago"
        class="flex-1 border rounded px-3 py-2"
        required
      />
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        Agregar
      </button>
    </form>
    <ul>
      <li
        v-for="(metodo, idx) in metodos"
        :key="idx"
        class="flex justify-between items-center border-b py-2"
      >
        <div v-if="editIndex === idx" class="flex flex-1 gap-2">
          <input
            v-model="editValue"
            type="text"
            class="flex-1 border rounded px-2 py-1"
            required
          />
          <button @click="guardarEdicion(idx)" class="text-green-600 hover:underline text-sm">Guardar</button>
          <button @click="cancelarEdicion" class="text-gray-500 hover:underline text-sm">Cancelar</button>
        </div>
        <div v-else class="flex flex-1 justify-between items-center">
          <span>{{ metodo }}</span>
          <div class="flex gap-2">
            <button @click="editarMetodo(idx)" class="text-blue-500 hover:underline text-sm">Editar</button>
            <button @click="eliminarMetodo(idx)" class="text-red-500 hover:underline text-sm">Eliminar</button>
          </div>
        </div>
      </li>
      <li v-if="metodos.length === 0" class="text-gray-400 text-center py-4">Sin métodos registrados.</li>
    </ul>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const metodos = ref(['Efectivo', 'Tarjeta de crédito', 'Transferencia'])
const nuevoMetodo = ref('')

const editIndex = ref(null)
const editValue = ref('')

function agregarMetodo() {
  const value = nuevoMetodo.value.trim()
  if (value && !metodos.value.includes(value)) {
    metodos.value.push(value)
    nuevoMetodo.value = ''
  }
}

function eliminarMetodo(idx) {
  metodos.value.splice(idx, 1)
  cancelarEdicion()
}

function editarMetodo(idx) {
  editIndex.value = idx
  editValue.value = metodos.value[idx]
}

function guardarEdicion(idx) {
  const value = editValue.value.trim()
  if (value && !metodos.value.includes(value)) {
    metodos.value[idx] = value
    cancelarEdicion()
  }
}

function cancelarEdicion() {
  editIndex.value = null
  editValue.value = ''
}
</script>