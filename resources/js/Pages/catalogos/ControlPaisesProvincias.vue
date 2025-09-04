<script setup>
import { ref, computed, onMounted } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Dialog from "primevue/dialog";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import InputText from "primevue/inputtext";
import Select from "primevue/select";
import Toast from "primevue/toast";
import { useToast } from "primevue/usetoast";
import axios from "axios";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faArrowLeft, faCheck, faFilter, faPencil, faPlus, faTrashCan, faXmark } from '@fortawesome/free-solid-svg-icons';

const toast = useToast();

// Datos
const paises = ref([]);
const provincias = ref([]);

// UI
const modoSeleccionado = ref("País");
const busquedaGeneral = ref("");
const modalAgregar = ref(false);
const modalEditar = ref(false);
const modalEliminar = ref(false);

// 📄 Paginación
const rowsPerPage = ref(10);
const rowsPerPageOptions = ref([5, 10, 15, 20, 30]);

// Formularios
const nuevoItem = ref({ id:null, nombre:"", pais_id:null });
const itemEdit = ref({ id:null, nombre:"", pais_id:null });
const itemEliminar = ref(null);

// Opciones para el select
const opcionesMostrar = ref([
  { label: 'Países', value: 'País' },
  { label: 'Provincias', value: 'Provincia' }
]);

// Cargar datos
onMounted(() => {
  cargarPaises();
  cargarProvincias();
});

const cargarPaises = async () => {
  try {
    const res = await axios.get("/api/paises");
    paises.value = res.data;
  } catch (error) {
    toast.add({ severity:"error", summary:"Error", detail:"No se pudieron cargar los países", life: 3000});
  }
};

const cargarProvincias = async () => {
  try {
    const res = await axios.get("/api/provincias");
    provincias.value = res.data;
  } catch {
    toast.add({ severity:"error", summary:"Error", detail:"No se pudieron cargar las provincias", life: 3000 });
  }
};

// Datos filtrados
const datosFiltrados = computed(() => {
  let lista = modoSeleccionado.value==="País"? paises.value : provincias.value;
  if(busquedaGeneral.value){
    lista = lista.filter(i=>i.nombre.toLowerCase().includes(busquedaGeneral.value.toLowerCase()));
  }
  return lista;
});

// Métodos
const tipoAgregar = ref(null);

function abrirModalAgregar(){ 
  tipoAgregar.value = null;
  nuevoItem.value={id:null,nombre:"",pais_id:null}; 
  modalAgregar.value=true; 
}

async function guardarItem(){
  try{
    // 🐛 DEBUGGING: Ver qué datos se están enviando
    console.log('Datos a enviar:', {
      tipoAgregar: tipoAgregar.value,
      nombre: nuevoItem.value.nombre,
      pais_id: nuevoItem.value.pais_id
    });

    // ✅ VALIDACIÓN MEJORADA: Verificar si no hay tipo seleccionado
    if (!tipoAgregar.value) {
      toast.add({severity:"warn", summary:"Atención", detail:"Debe seleccionar qué desea agregar (País o Provincia)", life: 4000});
      return;
    }

    // ✅ VALIDACIÓN MEJORADA: Verificar nombre vacío o solo espacios
    if (!nuevoItem.value.nombre || nuevoItem.value.nombre.trim() === "") {
      toast.add({severity:"warn", summary:"Campo requerido", detail:"El nombre es obligatorio", life: 4000});
      return;
    }

    // ✅ VALIDACIÓN: Longitud máxima
    if (nuevoItem.value.nombre.trim().length > 50) {
      toast.add({severity:"warn", summary:"Límite excedido", detail:"El nombre no puede tener más de 50 caracteres", life: 4000});
      return;
    }
    // ✅ VALIDACIÓN ESPECÍFICA PARA PROVINCIAS
    if(tipoAgregar.value === "Provincia" && !nuevoItem.value.pais_id) {
      toast.add({severity:"warn", summary:"Campo requerido", detail:"Debe seleccionar un país para la provincia", life: 4000});
      return;
    }

    if(tipoAgregar.value==="País"){
      const response = await axios.post("/api/paises",{nombre:nuevoItem.value.nombre.trim()});
      console.log('✅ País guardado exitosamente:', response.data);
      await cargarPaises();
      toast.add({severity:"success", summary:"Guardado", detail:"País agregado correctamente", life: 3000});
    } else if(tipoAgregar.value==="Provincia"){
      const response = await axios.post("/api/provincias",{
        nombre:nuevoItem.value.nombre.trim(), 
        pais_id:nuevoItem.value.pais_id
      });
      console.log('✅ Provincia guardada exitosamente:', response.data);
      await cargarProvincias();
      toast.add({severity:"success", summary:"Guardado", detail:"Provincia agregada correctamente", life: 3000});
    }

    modalAgregar.value=false;
    nuevoItem.value = { id:null, nombre:"", pais_id:null };
    tipoAgregar.value = null;
  } catch(error) { 
    // 🐛 DEBUGGING: Ver el error completo
    console.error('❌ Error completo:', error);
    console.error('📋 Respuesta del error:', error.response?.data);
    
    if (error.response?.status === 422) {
      const errors = error.response.data.errors;
      console.log('⚠️ Errores de validación:', errors);
      
      if (errors?.nombre) {
        // Mostrar mensaje específico del backend
        toast.add({
          severity:"error", 
          summary:"Ya existe", 
          detail: errors.nombre[0], 
          life: 5000
        });
      } else {
        toast.add({
          severity:"error", 
          summary:"Error de validación", 
          detail: error.response.data.message || "Datos inválidos", 
          life: 5000
        });
      }
    } else {
      toast.add({
        severity:"error", 
        summary:"Error", 
        detail:"No se pudo guardar. Intente nuevamente.", 
        life: 4000
      });
    }
  }
}

function abrirModalEditar(item){ 
  itemEdit.value={...item};
  if(modoSeleccionado.value==="Provincia" && item.pais) {
    itemEdit.value.pais_id=item.pais.id;
  }
  modalEditar.value=true;
}

async function actualizarItem(){
  try {
    // ✅ VALIDACIÓN MEJORADA: Verificar nombre vacío
    if (!itemEdit.value.nombre || itemEdit.value.nombre.trim() === "") {
      toast.add({severity:"warn", summary:"Campo requerido", detail:"El nombre es obligatorio", life: 4000});
      return;
    }

    // ✅ VALIDACIÓN: Longitud máxima
    if (itemEdit.value.nombre.trim().length > 50) {
      toast.add({severity:"warn", summary:"Límite excedido", detail:"El nombre no puede tener más de 50 caracteres", life: 4000});
      return;
    }

    // ✅ VALIDACIÓN ESPECÍFICA PARA PROVINCIAS
    if(modoSeleccionado.value === "Provincia" && !itemEdit.value.pais_id) {
      toast.add({severity:"warn", summary:"Campo requerido", detail:"Debe seleccionar un país para la provincia", life: 4000});
      return;
    }

    if(modoSeleccionado.value === "País"){
      await axios.put(`/api/paises/${itemEdit.value.id}`, {
        nombre: itemEdit.value.nombre.trim()
      });
      
      await cargarPaises();
      toast.add({severity:"success", summary:"Actualizado", detail:"País actualizado correctamente", life: 3000});
      modalEditar.value = false;
    } else {
      await axios.put(`/api/provincias/${itemEdit.value.id}`, {
        nombre: itemEdit.value.nombre.trim(),
        pais_id: itemEdit.value.pais_id
      });
      await cargarProvincias();
      toast.add({severity:"success", summary:"Actualizado", detail:"Provincia actualizada correctamente", life: 3000});
      modalEditar.value = false;
    }
  } catch (error) {
    if (error.response?.status === 422) {
      const errors = error.response.data.errors;
      if (errors?.nombre) {
        toast.add({severity:"error", summary:"Error de validación", detail: errors.nombre[0], life: 5000});
      } else {
        toast.add({severity:"error", summary:"Error de validación", detail: error.response.data.message || "Datos inválidos", life: 5000});
      }
    } else {
      const mensaje = error.response?.data?.message || "No se pudo actualizar";
      toast.add({severity:"error", summary:"Error", detail: mensaje, life: 4000});
    }
  }
}

async function eliminarItem(){
  try {
    if (!itemEliminar.value?.id) {
      toast.add({severity:"error", summary:"Error", detail:"No se puede eliminar: ID no válido", life: 3000});
      return;
    }

    if(modoSeleccionado.value === "País"){
      await axios.delete(`/api/paises/${itemEliminar.value.id}`);
      await cargarPaises();
      toast.add({severity:"success", summary:"Eliminado", detail:"País eliminado correctamente", life: 3000});
    } else {
      await axios.delete(`/api/provincias/${itemEliminar.value.id}`);
      await cargarProvincias();
      toast.add({severity:"success", summary:"Eliminado", detail:"Provincia eliminada correctamente", life: 3000});
    }
    
    modalEliminar.value = false;
    itemEliminar.value = null;
  } catch (error) {
    // 🎯 Manejar casos específicos como países con provincias asociadas
    if (error.response?.status === 422) {
      toast.add({severity:"warn", summary:"No se puede eliminar", detail: error.response.data.message, life: 5000});
    } else {
      const mensaje = error.response?.data?.message || "No se pudo eliminar";
      toast.add({severity:"error", summary:"Error", detail: mensaje, life: 4000});
    }
  }
}

function confirmarEliminar(item) { 
  itemEliminar.value = item; 
  modalEliminar.value = true; 
}

// ✅ MEJORAR VALIDACIÓN EN TIEMPO REAL
const validateNombre = (item, isEdit = false) => {
  const target = isEdit ? itemEdit : nuevoItem;
  if (target.value.nombre && target.value.nombre.length > 50) {
    target.value.nombre = target.value.nombre.substring(0, 50);
  }
};
</script>

<template>
  <Head title="Países y Provincias" />
  <AuthenticatedLayout>
    <Toast />
    <div class="py-4 sm:py-6 px-4 sm:px-7 mt-6 sm:mt-10 mx-auto bg-red-50 shadow-md rounded-lg">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-2 sm:gap-0">
        <div class="flex items-center gap-3">
          <Link :href="route('hoteles')" class="flex items-center text-blue-600 hover:text-blue-700 transition-colors duration-200 px-4 rounded-lg" title="Regresar a Hoteles">
            <FontAwesomeIcon :icon="faArrowLeft" class="h-8" />
          </Link>
          <h3 class="text-lg sm:text-2xl text-blue-600 font-bold">Control de Países y Provincias</h3>
        </div>
        <button
          class="bg-red-500 border border-red-500 p-2 text-sm text-white shadow-md hover:shadow-lg rounded-md hover:-translate-y-1 transition-transform duration-300" @click="abrirModalAgregar">
          <FontAwesomeIcon :icon="faPlus" class="h-4 w-4 text-white" /><span>&nbsp;Agregar</span>
        </button>                
      </div>

      <!-- 📊 TABLA OPTIMIZADA -->
      <DataTable
        :value="datosFiltrados"
        dataKey="id"
        :paginator="true"
        :rows="rowsPerPage"
        :rowsPerPageOptions="rowsPerPageOptions"
        v-model:rowsPerPage="rowsPerPage"
        paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
        :currentPageReportTemplate="`Mostrando {first} a {last} de {totalRecords} ${modoSeleccionado === 'País' ? 'países' : 'provincias'}`"
        class="overflow-x-auto"
        responsiveLayout="scroll"
        :pt="{
          root: { class: 'text-sm' },
          wrapper: { class: 'text-sm' },
          table: { class: 'text-sm' },
          thead: { class: 'text-sm' },
          headerRow: { class: 'text-sm' },
          headerCell: { class: 'text-sm font-medium py-3 px-2' },
          tbody: { class: 'text-sm' },
          bodyRow: { class: 'h-16 text-sm' },
          bodyCell: { class: 'py-3 px-2 text-sm' },
          paginator: { class: 'text-xs sm:text-sm' },
          paginatorWrapper: { class: 'flex flex-wrap justify-center sm:justify-between items-center gap-2 p-2' }
        }"
      >
        <template #header>
          <div class="bg-blue-50 p-3 rounded-lg shadow-sm border mb-4">
            <div class="flex items-center justify-between mb-3">
              <div class="flex items-center gap-3">
                <h3 class="text-base font-medium text-gray-800 flex items-center gap-2">
                  <FontAwesomeIcon :icon="faFilter" class="text-blue-600 text-sm" />
                  <span>Filtros</span>
                </h3>
                <div class="bg-blue-100 border border-blue-200 text-blue-700 px-3 py-1 rounded text-sm font-medium">
                  {{ datosFiltrados.length }} resultado{{ datosFiltrados.length !== 1 ? 's' : '' }}
                </div>
              </div>
              <div class="flex items-center gap-2">
                <label for="tipo-estado" class="text-sm font-medium text-gray-700">Mostrar:</label>
                <Select
                  id="tipo-estado"
                  v-model="modoSeleccionado"
                  :options="opcionesMostrar"
                  optionValue="value"
                  optionLabel="label"
                  class="w-32 h-8 text-sm"
                  style="background-color: white; border-color: #93c5fd;"
                />
              </div>
            </div>
            <div class="space-y-3">
              <div>
                <InputText 
                  v-model="busquedaGeneral" 
                  v-if="modoSeleccionado==='Provincia'" 
                  placeholder="🔍 Buscar provincias..." 
                  class="w-full h-9 text-sm"
                  style="background-color: white; border-color: #93c5fd;"
                />
                <InputText 
                  v-model="busquedaGeneral" 
                  v-else 
                  placeholder="🔍 Buscar países..." 
                  class="w-full h-9 text-sm"
                  style="background-color: white; border-color: #93c5fd;"
                />
              </div>
            </div>
          </div>
        </template>

        <Column field="nombre" header="Nombre" sortable>
          <template #body="slotProps">
            <div class="text-sm font-medium leading-relaxed">
              {{ slotProps.data.nombre }}
            </div>
          </template>
        </Column>

        <Column v-if="modoSeleccionado==='Provincia'" field="pais.nombre" header="País" sortable>
          <template #body="slotProps">
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
              {{ slotProps.data.pais?.nombre || 'Sin país' }}
            </span>
          </template>
        </Column>

        <Column :exportable="false">
          <template #header>
            <div class="text-center w-full font-bold">
              Acciones
            </div>
          </template>
          <template #body="slotProps">
            <div class="flex gap-1 justify-center items-center">
              <button
                class="bg-orange-200/30 border py-2 px-3 text-sm shadow-md hover:shadow-lg rounded-md hover:-translate-y-1 transition-transform duration-300"
                @click="abrirModalEditar(slotProps.data)">
                <FontAwesomeIcon :icon="faPencil" class="h-4 w-4 text-orange-600" />
                &nbsp;Editar
              </button>
              <button
                class="bg-red-200/30 border py-2 px-3 text-sm shadow-md hover:shadow-lg rounded-md hover:-translate-y-1 transition-transform duration-300"
                @click="confirmarEliminar(slotProps.data)">
                <FontAwesomeIcon :icon="faTrashCan" class="h-4 w-4 text-red-600" />
                &nbsp;Eliminar
              </button>
            </div>
          </template>
        </Column>
      </DataTable>

      <!-- 📝 Modal Agregar CON VALIDACIÓN VISUAL MEJORADA -->
      <Dialog v-model:visible="modalAgregar" header="Agregar" :modal="true" :closable="false" style="width:400px">
        <div class="flex flex-col gap-4">
          <div class="w-full flex flex-col">
            <label class="text-sm font-medium text-gray-700 mb-2">
              Tipo: <span class="text-red-500">*</span>
            </label>
            <Select
              v-model="tipoAgregar"
              :options="[
                { label: 'País', value: 'País' },
                { label: 'Provincia', value: 'Provincia' }
              ]"
              optionLabel="label"
              optionValue="value"
              placeholder="Seleccione qué desea agregar"
              class="w-full"
              :class="{ 'border-red-300': !tipoAgregar }"
            />
            <small class="text-red-500 mt-1" v-if="!tipoAgregar">
              ⚠️ Debe seleccionar el tipo
            </small>
          </div>

          <div class="w-full flex flex-col">
            <label class="text-sm font-medium text-gray-700 mb-2">
              Nombre: <span class="text-red-500">*</span>
            </label>
            <InputText 
              v-model="nuevoItem.nombre" 
              placeholder="Nombre (máximo 50 caracteres)" 
              class="w-full" 
              :class="{ 'border-red-300': !nuevoItem.nombre || nuevoItem.nombre.trim() === '' }"
              :disabled="!tipoAgregar"
              maxlength="50"
              @keypress="e => { if (!/[A-Za-zÀ-ÿ\s]/.test(e.key)) e.preventDefault() }"
            />
            <small class="text-red-500 mt-1" v-if="!nuevoItem.nombre || nuevoItem.nombre.trim() === ''">
              ⚠️ El nombre es obligatorio
            </small>
            <small class="text-orange-500 mt-1" v-else-if="nuevoItem.nombre && nuevoItem.nombre.length >= 40 && nuevoItem.nombre.length <= 50">
              Caracteres restantes: {{ 50 - nuevoItem.nombre.length }}
            </small>
          </div>

          <div v-if="tipoAgregar==='Provincia'" class="w-full flex flex-col">
            <label class="text-sm font-medium text-gray-700 mb-2">
              País: <span class="text-red-500">*</span>
            </label>
            <Select
              v-model="nuevoItem.pais_id"
              :options="paises"
              optionLabel="nombre"
              optionValue="id"
              placeholder="Seleccione un país"
              class="w-full"
              :class="{ 'border-red-300': tipoAgregar === 'Provincia' && !nuevoItem.pais_id }"
            />
            <small class="text-red-500 mt-1" v-if="tipoAgregar === 'Provincia' && !nuevoItem.pais_id">
              ⚠️ Debe seleccionar un país
            </small>
          </div>
        </div>
        <template #footer>
          <div class="flex justify-center gap-4 w-full">
            <button 
              type="button" 
              class="bg-white hover:bg-green-100 text-green-600 border border-green-600 px-6 py-2 rounded-md transition-all duration-200 ease-in-out flex items-center gap-2" 
              @click="modalAgregar=false">
              <FontAwesomeIcon :icon="faXmark" class="h-5 text-green-600" />
              Cancelar
            </button>
            <button 
              class="bg-red-500 hover:bg-red-700 text-white border-none px-6 py-2 rounded-md transition-all duration-200 ease-in-out flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" 
              @click="guardarItem"
              :disabled="!nuevoItem.nombre?.trim() || !tipoAgregar || nuevoItem.nombre.trim().length > 50 || (tipoAgregar === 'Provincia' && !nuevoItem.pais_id)">
              <FontAwesomeIcon :icon="faCheck" class="h-5 text-white" />
              Guardar
            </button>
          </div>
        </template>
      </Dialog>

      <!-- ✏️ Modal Editar CON VALIDACIÓN VISUAL MEJORADA -->
      <Dialog v-model:visible="modalEditar" :header="`Editar ${modoSeleccionado}`" :modal="true" :closable="false" style="width:400px">
        <div class="flex flex-col gap-4">
          <div v-if="modoSeleccionado==='Provincia'" class="w-full flex flex-col">
            <label class="text-sm font-medium text-gray-700 mb-2">
              País: <span class="text-red-500">*</span>
            </label>
            <Select
              v-model="itemEdit.pais_id"
              :options="paises"
              optionLabel="nombre"
              optionValue="id"
              placeholder="Seleccione un país"
              class="w-full"
              :class="{ 'border-red-300': modoSeleccionado === 'Provincia' && !itemEdit.pais_id }"
            />
            <small class="text-red-500 mt-1" v-if="modoSeleccionado === 'Provincia' && !itemEdit.pais_id">
              ⚠️ Debe seleccionar un país
            </small>
          </div>

          <div class="w-full flex flex-col">
            <label class="text-sm font-medium text-gray-700 mb-2">
              Nombre: <span class="text-red-500">*</span>
            </label>
            <InputText 
              v-model="itemEdit.nombre" 
              placeholder="Nombre (máximo 50 caracteres)" 
              class="w-full" 
              :class="{ 'border-red-300': !itemEdit.nombre || itemEdit.nombre.trim() === '' }"
              maxlength="50"
              @keypress="e => { if (!/[A-Za-zÀ-ÿ\s]/.test(e.key)) e.preventDefault() }"
            />
            <small class="text-red-500 mt-1" v-if="!itemEdit.nombre || itemEdit.nombre.trim() === ''">
              ⚠️ El nombre es obligatorio
            </small>
            <small class="text-orange-500 mt-1" v-else-if="itemEdit.nombre && itemEdit.nombre.length >= 40 && itemEdit.nombre.length <= 50">
              Caracteres restantes: {{ 50 - itemEdit.nombre.length }}
            </small>
          </div>
        </div>
        <template #footer>
          <div class="flex justify-center gap-4 w-full">
            <button 
              type="button" 
              class="bg-white hover:bg-green-100 text-green-600 border border-green-600 px-6 py-2 rounded-md transition-all duration-200 ease-in-out flex items-center gap-2" 
              @click="modalEditar=false">
              <FontAwesomeIcon :icon="faXmark" class="h-5 text-green-600" />
              Cancelar
            </button>
            <button 
              class="bg-red-500 hover:bg-red-700 text-white border-none px-6 py-2 rounded-md transition-all duration-200 ease-in-out flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" 
              @click="actualizarItem"
              :disabled="!itemEdit.nombre?.trim() || itemEdit.nombre.trim().length > 50 || (modoSeleccionado === 'Provincia' && !itemEdit.pais_id)">
              <FontAwesomeIcon :icon="faCheck" class="h-5 text-white" />
              Actualizar
            </button>
          </div>
        </template> 
      </Dialog>

      <!-- 🗑️ Modal Confirmar eliminar -->
      <Dialog v-model:visible="modalEliminar" :header="`Eliminar ${modoSeleccionado}`" :modal="true" :closable="false" style="width:400px">
        <div class="flex items-center gap-3 mb-4">
          <FontAwesomeIcon :icon="faTrashCan" class="h-8 w-8 text-red-500" />
          <div class="flex flex-col">
            <span>¿Estás seguro de eliminar <b>{{ itemEliminar?.nombre }}</b>?</span>
            <span class="text-red-600 text-sm font-medium mt-1">Esta acción es irreversible.</span>
          </div>
        </div>
        <template #footer>
          <div class="flex justify-center gap-4 w-full">
            <button 
              type="button" 
              class="bg-white hover:bg-green-100 text-green-600 border border-green-600 px-6 py-2 rounded-md transition-all duration-200 ease-in-out flex items-center gap-2" 
              @click="modalEliminar=false">
              <FontAwesomeIcon :icon="faXmark" class="h-5 text-green-600" />
              Cancelar
            </button>
            <button 
              class="bg-red-500 hover:bg-red-700 text-white border-none px-6 py-2 rounded-md transition-all duration-200 ease-in-out flex items-center gap-2" 
              @click="eliminarItem"
              :disabled="!itemEliminar || !itemEliminar.nombre">
              <FontAwesomeIcon :icon="faTrashCan" class="h-5 text-white" />
              Eliminar
            </button>
          </div>
        </template>
      </Dialog>
    </div>
  </AuthenticatedLayout>
</template>