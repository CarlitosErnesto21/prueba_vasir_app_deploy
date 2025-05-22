<!--<template>
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
</script>-->
<template>
  <div class="max-w-3xl mx-auto mt-10 bg-white rounded-lg shadow p-6">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Catálogo de Métodos de Pago</h2>
    <Toolbar class="mb-6">
      <template #start>
        <Button label="Nuevo" icon="pi pi-plus" class="mr-2" @click="openNew" />
        <Button label="Eliminar" icon="pi pi-trash" severity="danger" outlined @click="confirmDeleteSelected" :disabled="!selectedMetodos.length" />
      </template>
    </Toolbar>

    <DataTable
      ref="dt"
      v-model:selection="selectedMetodos"
      :value="metodos"
      dataKey="id"
      :paginator="true"
      :rows="5"
      :rowsPerPageOptions="[5, 10, 25]"
      currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} métodos"
    >
      <template #header>
        <div class="flex flex-wrap gap-2 items-center justify-between">
          <h4 class="m-0">Gestionar Métodos de Pago</h4>
          <InputText v-model="globalFilter" placeholder="Buscar..." />
        </div>
      </template>

      <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>
      <Column field="nombre" header="Nombre" sortable></Column>
      <Column :exportable="false" style="min-width: 8rem">
        <template #body="slotProps">
          <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editMetodo(slotProps.data)" />
          <Button icon="pi pi-trash" outlined rounded severity="danger" @click="confirmDeleteMetodo(slotProps.data)" />
        </template>
      </Column>
    </DataTable>

    <!-- Dialogo para crear/editar -->
    <Dialog v-model:visible="metodoDialog" :style="{ width: '350px' }" header="Método de Pago" :modal="true">
      <div class="flex flex-col gap-4">
        <div>
          <label for="nombre" class="block font-bold mb-2">Nombre</label>
          <InputText id="nombre" v-model.trim="metodo.nombre" required autofocus :invalid="submitted && !metodo.nombre" />
          <small v-if="submitted && !metodo.nombre" class="text-red-500">El nombre es obligatorio.</small>
        </div>
      </div>
      <template #footer>
        <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
        <Button label="Guardar" icon="pi pi-check" @click="saveMetodo" />
      </template>
    </Dialog>

    <!-- Dialogo para eliminar uno -->
    <Dialog v-model:visible="deleteMetodoDialog" :style="{ width: '350px' }" header="Confirmar" :modal="true">
      <div class="flex items-center gap-4">
        <i class="pi pi-exclamation-triangle text-2xl" />
        <span v-if="metodo">¿Seguro que deseas eliminar <b>{{ metodo.nombre }}</b>?</span>
      </div>
      <template #footer>
        <Button label="No" icon="pi pi-times" text @click="deleteMetodoDialog = false" />
        <Button label="Sí" icon="pi pi-check" @click="deleteMetodo" />
      </template>
    </Dialog>

    <!-- Dialogo para eliminar seleccionados -->
    <Dialog v-model:visible="deleteMetodosDialog" :style="{ width: '350px' }" header="Confirmar" :modal="true">
      <div class="flex items-center gap-4">
        <i class="pi pi-exclamation-triangle text-2xl" />
        <span>¿Seguro que deseas eliminar los métodos seleccionados?</span>
      </div>
      <template #footer>
        <Button label="No" icon="pi pi-times" text @click="deleteMetodosDialog = false" />
        <Button label="Sí" icon="pi pi-check" text @click="deleteSelectedMetodos" />
      </template>
    </Dialog>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Button } from 'primevue/button'
import { DataTable } from 'primevue/datatable'
import { Column } from 'primevue/column'
import { Dialog } from 'primevue/dialog'
import { Toolbar } from 'primevue/toolbar'
import { InputText } from 'primevue/inputtext'

const metodos = ref([
  { id: 1, nombre: 'Efectivo' },
  { id: 2, nombre: 'Tarjeta de crédito' },
  { id: 3, nombre: 'Transferencia' }
])
const metodo = ref({})
const selectedMetodos = ref([])
const metodoDialog = ref(false)
const deleteMetodoDialog = ref(false)
const deleteMetodosDialog = ref(false)
const submitted = ref(false)
const globalFilter = ref('')

function openNew() {
  metodo.value = {}
  submitted.value = false
  metodoDialog.value = true
}

function hideDialog() {
  metodoDialog.value = false
  submitted.value = false
}

function saveMetodo() {
  submitted.value = true
  if (!metodo.value.nombre) return

  if (metodo.value.id) {
    // Editar
    const idx = metodos.value.findIndex(m => m.id === metodo.value.id)
    if (idx !== -1) metodos.value[idx] = { ...metodo.value }
  } else {
    // Crear
    metodo.value.id = Date.now()
    metodos.value.push({ ...metodo.value })
  }
  metodoDialog.value = false
  metodo.value = {}
}

function editMetodo(data) {
  metodo.value = { ...data }
  metodoDialog.value = true
}

function confirmDeleteMetodo(data) {
  metodo.value = data
  deleteMetodoDialog.value = true
}

function deleteMetodo() {
  metodos.value = metodos.value.filter(m => m.id !== metodo.value.id)
  deleteMetodoDialog.value = false
  metodo.value = {}
}

function confirmDeleteSelected() {
  deleteMetodosDialog.value = true
}

function deleteSelectedMetodos() {
  metodos.value = metodos.value.filter(m => !selectedMetodos.value.includes(m))
  deleteMetodosDialog.value = false
  selectedMetodos.value = []
}
</script>