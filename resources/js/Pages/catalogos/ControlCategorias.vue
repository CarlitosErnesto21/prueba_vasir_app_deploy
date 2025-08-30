
<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Dialog from 'primevue/dialog'
import Button from 'primevue/button'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Dropdown from 'primevue/dropdown'
import InputText from 'primevue/inputtext'
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast'
import axios from 'axios'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faEdit, faTrashCan } from '@fortawesome/free-solid-svg-icons';

const toast = useToast()

// Datos
const categorias = ref([])
const tiposCategoria = [
  { label: 'Productos', value: 'productos' }, 
  { label: 'Hoteles', value: 'hoteles' }
]

// NUEVAS variables para filtro y búsqueda
const tipoEstadoSeleccionado = ref('productos')
const busquedaNombreGeneral = ref('')

const tiposEstado = [
  { label: 'Productos', value: 'productos' },
  { label: 'Hoteles', value: 'hoteles' }
]

// UI 
const modalAgregar = ref(false)
const modalEditar = ref(false)
const modalEliminar = ref(false)

// Formularios
const nuevaCategoria = ref({ id: null, nombre: '', tipo: null })
const categoriaEdit = ref({ id: null, nombre: '', tipo: null })
const categoriaEliminar = ref(null)

// Cargar datos según tipo seleccionado
const cargarCategoriasPorTipo = async (tipo) => {
  try {
    const ruta = tipo === 'hoteles' ? 'categorias-hoteles' : 
                 'categorias-productos';
    console.log('Cargando categorías de:', `/api/${ruta}`)
    const response = await axios.get(`/api/${ruta}`)
    console.log('Datos recibidos:', response.data)
    categorias.value = response.data.map(cat => ({
      ...cat,
      tipo: tipo,
      categoria_id: cat.id // Aseguramos que tenemos el ID correcto
    }))
    console.log('Categorías procesadas:', categorias.value)
  } catch (error) {
    console.error('Error al cargar categorías:', error.response || error)
    toast.add({ severity: 'error', summary: 'Error', detail: `No se pudieron cargar las categorías de ${tipo}.`, life: 3000 })
  }
}

// Cargar al inicio
onMounted(() => {
  cargarCategoriasPorTipo(tipoEstadoSeleccionado.value)
})

// Recargar cuando cambia el tipo seleccionado
watch(tipoEstadoSeleccionado, (nuevoTipo) => {
  cargarCategoriasPorTipo(nuevoTipo)
})

// Filtro de categorías por tipo y nombre
const categoriasFiltradas = computed(() => {
  let lista = categorias.value.filter(c => c.tipo === tipoEstadoSeleccionado.value)
  if (busquedaNombreGeneral.value) {
    lista = lista.filter(c => c.nombre.toLowerCase().includes(busquedaNombreGeneral.value.toLowerCase()))
  }
  return lista
})

// Métodos
function abrirModalAgregar() {
  nuevaCategoria.value = { id: null, nombre: '', tipo: tipoEstadoSeleccionado.value }
  modalAgregar.value = true
}

async function guardarCategoria() {
  if (!nuevaCategoria.value.nombre || !nuevaCategoria.value.tipo) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Por favor completa todos los campos.', life: 3000 })
    return
  }

  try {
    const tipo = nuevaCategoria.value.tipo;
    const ruta = tipo === 'hoteles' ? 'categorias-hoteles' : 
                 'categorias-productos';
    console.log('Enviando petición a:', `/api/${ruta}`, 'con datos:', { nombre: nuevaCategoria.value.nombre })
    const response = await axios.post(`/api/${ruta}`, { nombre: nuevaCategoria.value.nombre })
    console.log('Respuesta:', response)
    toast.add({ severity: 'success', summary: 'Éxito', detail: 'Categoría agregada correctamente.', life: 3000 })
    modalAgregar.value = false
    cargarCategoriasPorTipo(tipoEstadoSeleccionado.value)
  } catch (error) {
    console.error('Error al guardar categoría:', error.response || error)
    const mensaje = error.response?.data?.message || 'No se pudo agregar la categoría.'
    toast.add({ severity: 'error', summary: 'Error', detail: mensaje, life: 3000 })
  }
}

function abrirModalEditar(categoria) {
  console.log('Editando categoría:', categoria)
  categoriaEdit.value = { 
    id: categoria.id,
    nombre: categoria.nombre,
    tipo: categoria.tipo
  }
  modalEditar.value = true
} 

async function actualizarCategoria() {
  if (!categoriaEdit.value.nombre || !categoriaEdit.value.tipo) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Por favor completa todos los campos.', life: 3000 })
    return
  }

  try {
    const tipo = categoriaEdit.value.tipo;
    const ruta = tipo === 'hoteles' ? 'categorias-hoteles' : 
                'categorias-productos';
    const response = await axios({
      method: 'POST',
      url: `/api/${ruta}/${categoriaEdit.value.id}`,
      data: {
        _method: 'PUT',
        nombre: categoriaEdit.value.nombre
      }
    });

    if (response.data.categoria) {
      toast.add({ severity: 'success', summary: 'Éxito', detail: 'Categoría actualizada correctamente', life: 3000 });
      modalEditar.value = false;
      await cargarCategoriasPorTipo(tipoEstadoSeleccionado.value);
    }
  } catch (error) {
    console.error('Error al actualizar:', error);
    toast.add({ severity: 'error', summary: 'Error', detail: error.response?.data?.message || 'No se pudo actualizar la categoría', life: 3000 });
  }
}

function confirmarEliminar(categoria) {
  console.log('Confirmando eliminación de categoría:', categoria)
  categoriaEliminar.value = { 
    id: categoria.id,
    nombre: categoria.nombre,
    tipo: categoria.tipo
  }
  modalEliminar.value = true
}

async function eliminarCategoria() {
  try {
    const tipo = categoriaEliminar.value.tipo;
    const ruta = tipo === 'hoteles' ? 'categorias-hoteles' : 
              'categorias-productos';
    console.log('Intentando eliminar:', {
      tipo,
      ruta,
      id: categoriaEliminar.value.id
    });
    
    const response = await axios({
      method: 'POST',
      url: `/api/${ruta}/${categoriaEliminar.value.id}`,
      data: {
        _method: 'DELETE'
      },
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    });

    console.log('Respuesta del servidor:', response.data);
    
    if (response.data.success || response.status === 200) {
      toast.add({ severity: 'success', summary: 'Éxito', detail: response.data.message || 'Categoría eliminada correctamente', life: 3000 });
      modalEliminar.value = false;
      await cargarCategoriasPorTipo(tipoEstadoSeleccionado.value);
    }
  } catch (error) {
    console.error('Error al eliminar:', error);
    toast.add({ severity: 'error', summary: 'Error', detail: error.response?.data?.message || 'No se pudo eliminar la categoría', life: 3000 });
  }
}
</script>

<template>
  <Head title="Control de categorías" />
  <AuthenticatedLayout>
    <Toast />
    <div class="py-6 px-2 sm:px-4 md:px-6 mt-10 mx-auto bg-red-50 shadow-md rounded-lg max-w-3xl">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold">Control de Categorías</h3>
        <Button 
          label="Agregar Categoría" 
          icon="pi pi-plus"
          class="p-button-danger" 
          @click="abrirModalAgregar" />
      </div>

      <div class="flex flex-col md:flex-row items-center gap-4 mt-10 mb-2">
        <label for="tipo-estado" class="font-semibold mb-0">Ver categorías:</label>
        <Dropdown
          id="tipo-estado"
          v-model="tipoEstadoSeleccionado"
          :options="tiposEstado"
          optionValue="value"
          optionLabel="label"
          class="w-36"
        />

        <label for="busqueda-nombre-general" class="sr-only">Buscar por nombre</label>
        <InputText
          id="busqueda-nombre-general"
          name="busqueda-nombre-general"
          v-model="busquedaNombreGeneral"
          placeholder="Buscar por nombre..."
          class="w-64"
        />
      </div>
      <DataTable :value="categoriasFiltradas" class="mb-4 min-w-[400px] w-full" :rows="8" :paginator="categoriasFiltradas.length > 8" :rowsPerPageOptions="[8, 16]">
        <Column field="nombre" header="Nombre" />
        <Column header="Acciones" :exportable="false">
          <template #body="slotProps">
            <div class="flex gap-3">
              <!-- Botón Editar -->
              <button
                class="text-blue-600 hover:text-blue-900 transition-colors"
                @click="abrirModalEditar(slotProps.data)"
                title="Editar"
              >
                <FontAwesomeIcon :icon="faEdit" class="h-5 w-5" />
              </button>

              <!-- Botón Eliminar -->
              <button
                class="text-red-600 hover:text-red-900 transition-colors"
                @click="confirmarEliminar(slotProps.data)"
                title="Eliminar"
              >
                <FontAwesomeIcon :icon="faTrashCan" class="h-5 w-5" />
              </button>
            </div>
          </template>
        </Column>
      </DataTable>
      <!-- Modal Agregar -->
      <Dialog v-model:visible="modalAgregar" header="Agregar Categoría" :modal="true" :closable="false" style="width:350px">
        <div class="flex flex-col gap-3">
          <Dropdown v-model="nuevaCategoria.tipo" :options="tiposCategoria" optionLabel="label" optionValue="value" placeholder="Tipo de categoría" class="w-full" />
          <InputText v-model="nuevaCategoria.nombre" placeholder="Nombre" class="w-full" />
        </div>
        <template #footer>
          <Button label="Guardar" icon="pi pi-check" class="p-button-rounded p-button-warn mr-2" @click="guardarCategoria" :disabled="!nuevaCategoria.nombre" />
          <Button label="Cerrar" icon="pi pi-times" class="p-button-rounded p-button-danger mr-2" @click="modalAgregar = false" />
        </template>
      </Dialog>
     
      <Dialog v-model:visible="modalEditar" header="Editar Categoría" :modal="true" :closable="false" style="width:350px">
        <div class="flex flex-col gap-3">
          <Dropdown v-model="categoriaEdit.tipo" :options="tiposCategoria" optionLabel="label" optionValue="value" placeholder="Tipo de categoría" class="w-full" />
          <InputText v-model="categoriaEdit.nombre" placeholder="Nombre" class="w-full" />
        </div>
        <template #footer>
          <Button label="Actualizar" icon="pi pi-check" @click="actualizarCategoria" :disabled="!categoriaEdit.nombre" />
          <Button label="Cerrar" icon="pi pi-times" class="p-button-text" @click="modalEditar = false" />
        </template>
      </Dialog>
      
      <Dialog v-model:visible="modalEliminar" header="Eliminar Categoría" :modal="true" :closable="false" style="width:350px">
        <div class="mb-4">¿Seguro que deseas eliminar la categoría <b>{{ categoriaEliminar?.nombre }}</b>?</div>
        <template #footer>
          <Button label="Eliminar" icon="pi pi-trash" class="p-button-danger" @click="eliminarCategoria" />
          <Button label="Cancelar" icon="pi pi-times" class="p-button-text" @click="modalEliminar = false" />
        </template>
      </Dialog>
    </div>
  </AuthenticatedLayout>
</template>
