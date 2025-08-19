<template>
  <Head title="Categorias de Tours" />
  <AuthenticatedLayout>
    <Toast />
    <div class="py-6 px-2 sm:px-4 md:px-6 mt-10 mx-auto bg-red-50 shadow-md rounded-lg max-w-full">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold text-center w-full">Control de Categorias</h3>
        <Button label="Agregar Categoria" icon="pi pi-plus" class="p-button-danger" @click="abrirModalAgregar" />
      </div>
      <div class="flex flex-col md:flex-row items-center gap-4 mt-10 mb-2">
        <label for="tipo-estado" class="font-semibold mb-0">Ver categorias:</label>
        <select
          id="tipo-estado"
          name="tipo-estado"
          v-model="tipoEstadoSeleccionado"
          class="p-2 rounded border border-gray-300 appearance-none w-36"
          style="background-position: right 0.1rem center; background-repeat: no-repeat; background-size: 1.5em 1.5em;">
          <option value="tours">Tours</option>
          <option value="productos">Productos</option>
          <option value="hoteles">Hoteles</option>
        </select>


        <!-- Buscador global por nombre de tour/hotel/aerolínea -->
        <label for="busqueda-nombre-general" class="sr-only">Buscar por nombre</label>
        <InputText
          id="busqueda-nombre-general"
          name="busqueda-nombre-general"
          v-model="busquedaNombreGeneral"
          placeholder="Buscar por nombre..."
          class="w-64"
        />
      </div>
      <div class="overflow-x-auto">
       
      </div>
      <DataTable :value="categoriasFiltradas" class="mb-4 min-w-[400px] w-full" :rows="8" :paginator="categoriasFiltradas.length > 8" :rowsPerPageOptions="[8, 16]">
        <Column field="nombre" header="Nombre" />
        <Column field="tipo" header="Tipo" />
        <Column header="Acciones">
          <template #body="slotProps">
            <Button label="Editar" icon="pi pi-pencil" class="p-button-sm p-button-info mr-2" @click="abrirModalEditar(slotProps.data)" />
            <Button label="Eliminar" icon="pi pi-trash" class="p-button-sm p-button-danger" @click="confirmarEliminar(slotProps.data)" />
          </template>
        </Column>
      </DataTable>
      <!-- Modal Agregar -->
      <Dialog v-model:visible="modalAgregar" header="Agregar Categoria" :modal="true" :closable="false" style="width:350px">
        <div class="flex flex-col gap-3">
          <Dropdown v-model="nuevaCategoria.tipo" :options="tiposCategoria" optionLabel="label" optionValue="value" placeholder="Tipo de categoría" class="w-full" />
          <InputText v-model="nuevaCategoria.nombre" placeholder="Nombre" class="w-full" />
        </div>
        <template #footer>
          <Button label="Guardar" icon="pi pi-check" class="p-button-rounded p-button-warn mr-2" @click="guardarCategoria" :disabled="!nuevaCategoria.nombre" />
          <Button label="Cerrar" icon="pi pi-times" class="p-button-rounded p-button-danger mr-2" @click="modalAgregar = false" />
        </template>
  <!-- Estilos eliminados para claridad y funcionalidad -->
      </Dialog>
     
      <Dialog v-model:visible="modalEditar" header="Editar Categoria" :modal="true" :closable="false" style="width:350px">
        <div class="flex flex-col gap-3">
          <Dropdown v-model="categoriaEdit.tipo" :options="tiposCategoria" optionLabel="label" optionValue="value" placeholder="Tipo de categoría" class="w-full" />
          <InputText v-model="categoriaEdit.nombre" placeholder="Nombre" class="w-full" />
        </div>
        <template #footer>
          <Button label="Actualizar"
           icon="pi pi-check"
           @click="actualizarCategoria"
           :disabled="!categoriaEdit.nombre"
          />
          <Button label="Cerrar"
           icon="pi pi-times" 
           class="p-button-text"
           @click="modalEditar = false" 
          />
        </template>
      </Dialog>
      <!-- Confirmación Eliminar -->
      <Dialog v-model:visible="modalEliminar" header="Eliminar Categoria" :modal="true" :closable="false" style="width:350px">
        <div class="mb-4">¿Seguro que deseas eliminar la categoría <b>{{ categoriaEliminar?.nombre }}</b>?</div>
        <template #footer>
          <Button label="Eliminar" icon="pi pi-trash" class="p-button-danger" @click="eliminarCategoria" />
          <Button label="Cancelar" icon="pi pi-times" class="p-button-text" @click="modalEliminar = false" />
        </template>
      </Dialog>

      <!-- Modal único para categorias por tour/hotel/aerolínea -->
      <Dialog
        v-model:visible="mostrarModalCategoriasTour"
        modal
        header="Categorias"
        :style="{ width: '98vw', maxWidth: '1100px' }"
        :draggable="false"
        :position="'center'"
      >
        <div>
          <!-- Buscador en el modal -->
          <div class="flex flex-col md:flex-row gap-2 mb-3">
            <InputText
              id="busqueda-modal-cliente"
              name="busqueda-modal-cliente"
              v-model="busquedaModalCliente"
              placeholder="Buscar por cliente"
              class="w-full md:w-1/2"
            />
            <DatePicker
              id="busqueda-modal-fecha"
              name="busqueda-modal-fecha"
              v-model="busquedaModalFecha"
              placeholder="Buscar por fecha"
              dateFormat="dd/mm/yy"
              class="w-full md:w-1/2"
              showIcon
            />
          </div>
          <div class="overflow-y-auto" style="max-height: 350px;">
            <DataTable
              :value="categoriasFiltradasModal"
              class="min-w-[350px] w-full"
              :rows="5"
              :paginator="categoriasFiltradasModal.length > 5"
              :rowsPerPageOptions="[5, 10]"
              scrollable
              scrollHeight="320px"
              v-if="categoriasFiltradasModal.length"
            >
              <Column header="Acción">
                <template #body="slotProps">
                  <Button
                    v-if="slotProps.data.estado === 'Pendiente'"
                    label="Confirmar"
                    icon="pi pi-check"
                    class="p-button-xs p-button-success ml-2"
                    @click="confirmarCategoria(slotProps.data)"
                  />
                  <Button
                    v-if="slotProps.data.estado === 'Pendiente'"
                    label="Rechazar"
                    icon="pi pi-times"
                    class="p-button-xs p-button-danger ml-2"
                    @click="cambiarEstado(slotProps.data, 'Rechazada')"
                  />
                </template>
              </Column>
            </DataTable>
            <div v-else class="text-gray-500 text-center py-4">No hay categorias para este elemento.</div>
          </div>
        </div>
      </Dialog>
      <!-- Modal para reprogramar reserva -->
      <Dialog
        v-model:visible="mostrarModalReprogramar"
        modal
        header="Reprogramar reserva"
        :style="{ width: '350px' }"
        :draggable="false"
        :position="'center'"
      >
        <div v-if="reservaAReprogramar">
          <div class="mb-4">
            <label class="block font-semibold mb-1" for="cliente-reprogramar">Cliente:</label>
            <span id="cliente-reprogramar">{{ reservaAReprogramar.cliente }}</span>
          </div>
        </div>
        <div class="flex justify-end gap-2 mt-4">
          <Button label="Cerrar" icon="pi pi-times" class="p-button-danger" @click="cerrarModalReprogramar" />
          <Button label="Guardar" icon="pi pi-save" class="p-button-warn" @click="guardarReprogramacion" :disabled="!nuevaFechaReprogramar" />
        </div>
      </Dialog>
      
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Dialog from 'primevue/dialog'
import Button from 'primevue/button'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import DatePicker from 'primevue/datepicker'
import InputText from 'primevue/inputtext'
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast'
import axios from 'axios'

const toast = useToast()

// --- Variables de filtrado ---
const tipoEstadoSeleccionado = ref('tours')
const busquedaNombreGeneral = ref('')

// --- Categorías ---
const categorias = ref([])
const tiposCategoria = [
  { label: 'Producto', value: 'productos' },
  { label: 'Hotel', value: 'hoteles' },
  { label: 'Tour', value: 'tours' },
]

// --- Modales y formularios ---
const modalAgregar = ref(false)
const modalEditar = ref(false)
const modalEliminar = ref(false)
const nuevaCategoria = ref({ tipo: null, nombre: '' })
const categoriaEdit = ref({ id: null, tipo: null, nombre: '' })
const categoriaEliminar = ref(null)

// --- Modal Categorias Tour/Hotel/Producto ---
const mostrarModalCategoriasTour = ref(false)
const categoriasTourSeleccionado = ref([])
const busquedaModalCliente = ref('')
const busquedaModalFecha = ref('')

const categoriasFiltradasModal = computed(() => {
  let lista = categoriasTourSeleccionado.value || []
  if (busquedaModalCliente.value) {
    lista = lista.filter(r =>
      r.cliente.toLowerCase().includes(busquedaModalCliente.value.toLowerCase())
    )
  }
  if (busquedaModalFecha.value) {
    lista = lista.filter(r =>
      r.fecha.includes(busquedaModalFecha.value)
    )
  }
  return lista
})

const categoriasFiltradas = computed(() => {
  return categorias.value.filter(cat =>
    cat.tipo === tipoEstadoSeleccionado.value &&
    (!busquedaNombreGeneral.value || cat.nombre.toLowerCase().includes(busquedaNombreGeneral.value.toLowerCase()))
  );
})

// --- Cargar categorías desde la DB ---
async function cargarCategorias() {
  try {
    let url = ''
    switch (tipoEstadoSeleccionado.value) {
      case 'tours': url = '/api/categorias_tours'; break
      case 'productos': url = '/api/categorias_productos'; break
      case 'hoteles': url = '/api/categorias_hoteles'; break
    }
    const response = await axios.get(url)
    categorias.value = response.data
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudieron cargar las categorías', life: 3000 })
  }
}

// --- Observador para recargar al cambiar tipo ---
watch(tipoEstadoSeleccionado, () => {
  cargarCategorias()
})

// --- Inicial ---
cargarCategorias()

// --- Abrir Modal Agregar ---
function abrirModalAgregar() {
  nuevaCategoria.value = { tipo: tipoEstadoSeleccionado.value, nombre: '' }
  modalAgregar.value = true
}

// --- Guardar Categoría ---
async function guardarCategoria() {
  try {
    let url = ''
    switch (nuevaCategoria.value.tipo) {
      case 'tours': url = '/api/categorias_tours'; break
      case 'productos': url = '/api/categorias_productos'; break
      case 'hoteles': url = '/api/categorias_hoteles'; break
    }
    const response = await axios.post(url, { nombre: nuevaCategoria.value.nombre })
    categorias.value.push(response.data)
    modalAgregar.value = false
    toast.add({ severity: 'success', summary: 'Agregado', detail: 'Categoría agregada', life: 2000 })
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo agregar la categoría', life: 3000 })
  }
}

// --- Abrir Modal Editar ---
function abrirModalEditar(cat) {
  categoriaEdit.value = { ...cat }
  modalEditar.value = true
}

// --- Actualizar Categoría ---
async function actualizarCategoria() {
  try {
    let url = ''
    switch (categoriaEdit.value.tipo) {
      case 'tours': url = `/api/categorias_tours/${categoriaEdit.value.id}`; break
      case 'productos': url = `/api/categorias_productos/${categoriaEdit.value.id}`; break
      case 'hoteles': url = `/api/categorias_hoteles/${categoriaEdit.value.id}`; break
    }
    await axios.put(url, { nombre: categoriaEdit.value.nombre })
    const idx = categorias.value.findIndex(c => c.id === categoriaEdit.value.id)
    if (idx !== -1) categorias.value[idx] = { ...categoriaEdit.value }
    modalEditar.value = false
    toast.add({ severity: 'success', summary: 'Actualizado', detail: 'Categoría actualizada', life: 2000 })
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo actualizar', life: 3000 })
  }
}

// --- Confirmar Eliminar ---
function confirmarEliminar(cat) {
  categoriaEliminar.value = cat
  modalEliminar.value = true
}

// --- Eliminar Categoría ---
async function eliminarCategoria() {
  try {
    let url = ''
    switch (categoriaEliminar.value.tipo) {
      case 'tours': url = `/api/categorias_tours/${categoriaEliminar.value.id}`; break
      case 'productos': url = `/api/categorias_productos/${categoriaEliminar.value.id}`; break
      case 'hoteles': url = `/api/categorias_hoteles/${categoriaEliminar.value.id}`; break
    }
    await axios.delete(url)
    categorias.value = categorias.value.filter(c => c.id !== categoriaEliminar.value.id)
    modalEliminar.value = false
    toast.add({ severity: 'success', summary: 'Eliminado', detail: 'Categoría eliminada', life: 2000 })
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo eliminar', life: 3000 })
  }
}

</script>

