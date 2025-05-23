<template>
  <div>
    <h2>Métodos de Pago</h2>
    <Button label="Nuevo método" icon="pi pi-plus" @click="abrirDialogoNuevo" class="mb-3" />

    <DataTable :value="metodos">
      <Column field="nombre" header="Nombre"></Column>
      <Column field="descripcion" header="Descripción"></Column>
      <Column header="Acciones" :body="accionesTemplate"></Column>
    </DataTable>

    <Dialog v-model:visible="dialogoVisible" :header="editando ? 'Editar método' : 'Nuevo método'" :modal="true">
      <div class="p-fluid">
        <div class="p-field">
          <label for="nombre">Nombre</label>
          <InputText id="nombre" v-model="metodo.nombre" required autofocus />
        </div>
        <div class="p-field">
          <label for="descripcion">Descripción</label>
          <InputText id="descripcion" v-model="metodo.descripcion" />
        </div>
      </div>
      <template #footer>
        <Button label="Cancelar" icon="pi pi-times" @click="cerrarDialogo" class="p-button-text" />
        <Button label="Guardar" icon="pi pi-check" @click="guardarMetodo" autofocus />
      </template>
    </Dialog>
  </div>
</template>

<script setup>
import { ref, h } from 'vue'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import Dialog from 'primevue/dialog'
import InputText from 'primevue/inputtext'

const metodos = ref([
  { id: 1, nombre: 'Efectivo', descripcion: 'Pago en efectivo' },
  { id: 2, nombre: 'Tarjeta', descripcion: 'Pago con tarjeta de crédito/débito' }
])
const dialogoVisible = ref(false)
const editando = ref(false)
const metodo = ref({ id: null, nombre: '', descripcion: '' })

function abrirDialogoNuevo() {
  metodo.value = { id: null, nombre: '', descripcion: '' }
  editando.value = false
  dialogoVisible.value = true
}

function cerrarDialogo() {
  dialogoVisible.value = false
}

function guardarMetodo() {
  if (!metodo.value.nombre) return
  if (editando.value) {
    const idx = metodos.value.findIndex(m => m.id === metodo.value.id)
    if (idx !== -1) metodos.value[idx] = { ...metodo.value }
  } else {
    metodo.value.id = Date.now()
    metodos.value.push({ ...metodo.value })
  }
  cerrarDialogo()
}

function editarMetodo(rowData) {
  metodo.value = { ...rowData }
  editando.value = true
  dialogoVisible.value = true
}

function eliminarMetodo(rowData) {
  metodos.value = metodos.value.filter(m => m.id !== rowData.id)
}

function accionesTemplate(rowData) {
  return [
    h(Button, {
      icon: 'pi pi-pencil',
      class: 'p-button-text p-button-sm',
      onClick: () => editarMetodo(rowData)
    }),
    h(Button, {
      icon: 'pi pi-trash',
      class: 'p-button-text p-button-sm p-button-danger',
      onClick: () => eliminarMetodo(rowData)
    })
  ]
}
</script>