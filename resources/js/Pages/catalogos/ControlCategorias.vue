<template>
  <Head title="Categorias de Tours" />
  <AuthenticatedLayout>
    <Toast />
    <div class="py-6 px-2 sm:px-4 md:px-6 mt-10 mx-auto bg-red-50 shadow-md rounded-lg max-w-full">
      <div class="flex justify-between items-center mb-4">
  <h3 class="text-xl font-bold">Control de Categorias</h3>
  <Button label="Agregar Categoria" icon="pi pi-plus" class="p-button-sm p-button-danger ml-4" @click="abrirModalAgregar" />
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
        <Column header="Acciones">
          <template #body="slotProps">
            <Button label="Editar" icon="pi pi-pencil" class="p-button-sm p-button-secondary mr-2" @click="abrirModalEditar(slotProps.data)" style="border: none; color: #2563eb; --icon-color: #2563eb;" />
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
          <Button label="Guardar" icon="pi pi-check" @click="guardarCategoria" :disabled="!nuevaCategoria.nombre" />
          <Button label="Cerrar" icon="pi pi-times" class="p-button-text" @click="modalAgregar = false" />
        </template>
  <!-- Estilos eliminados para claridad y funcionalidad -->
      </Dialog>
      <!-- Modal Editar -->
      <Dialog v-model:visible="modalEditar" header="Editar Categoria" :modal="true" :closable="false" style="width:350px">
        <div class="flex flex-col gap-3">
          <Dropdown v-model="categoriaEdit.tipo" :options="tiposCategoria" optionLabel="label" optionValue="value" placeholder="Tipo de categoría" class="w-full" />
          <InputText v-model="categoriaEdit.nombre" placeholder="Nombre" class="w-full" />
        </div>
        <template #footer>
          <Button label="Actualizar" icon="pi pi-check" @click="actualizarCategoria" :disabled="!categoriaEdit.nombre" />
          <Button label="Cerrar" icon="pi pi-times" class="p-button-text" @click="modalEditar = false" />
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
import InputText from 'primevue/inputtext'
import Toast from 'primevue/toast'


// Simulación de categorias de distintos tipos y tours
const categorias = ref([
  { id: 1, nombre: 'Ejemplo Producto 1', tipo: 'productos' },
  { id: 2, nombre: '1 ESTRELLA', tipo: 'hoteles' },
  { id: 3, nombre: 'Ejemplo Tour 1', tipo: 'tours' },
]);

const tiposCategoria = [
  { label: 'Producto', value: 'producto' },
  { label: 'Hotel', value: 'hotel' },
  { label: 'Tour', value: 'tour' },
];

// Estados para modales y formularios
const modalAgregar = ref(false);
const modalEditar = ref(false);
const modalEliminar = ref(false);
const nuevaCategoria = ref({ tipo: null, nombre: '' });
const categoriaEdit = ref({ id: null, tipo: null, nombre: '' });
const categoriaEliminar = ref(null);

const tipoEstadoSeleccionado = ref('tours');
const busquedaNombreGeneral = ref('');

  const categoriasFiltradas = computed(() => {
    return categorias.value.filter(cat =>
      cat.tipo === tipoEstadoSeleccionado.value &&
      (!busquedaNombreGeneral.value || cat.nombre.toLowerCase().includes(busquedaNombreGeneral.value.toLowerCase()))
    );
  });

function abrirModalAgregar() {
  nuevaCategoria.value = { tipo: tipoEstadoSeleccionado.value, nombre: '' };
  modalAgregar.value = true;
}
function guardarCategoria() {
  const nuevoId = categorias.value.length ? Math.max(...categorias.value.map(c => c.id)) + 1 : 1;
  categorias.value.push({ id: nuevoId, nombre: nuevaCategoria.value.nombre, tipo: nuevaCategoria.value.tipo });
  modalAgregar.value = false;
}
function abrirModalEditar(cat) {
  categoriaEdit.value = { ...cat };
  modalEditar.value = true;
}
function actualizarCategoria() {
  const idx = categorias.value.findIndex(c => c.id === categoriaEdit.value.id);
  if (idx !== -1) {
    categorias.value[idx] = { ...categoriaEdit.value };
  }
  modalEditar.value = false;
}
function confirmarEliminar(cat) {
  categoriaEliminar.value = cat;
  modalEliminar.value = true;
}
function eliminarCategoria() {
  categorias.value = categorias.value.filter(c => c.id !== categoriaEliminar.value.id);
  modalEliminar.value = false;
}
</script>