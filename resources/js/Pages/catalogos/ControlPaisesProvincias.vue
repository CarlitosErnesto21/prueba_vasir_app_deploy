<script setup>
import { ref, computed, onMounted } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Dialog from "primevue/dialog";
import Button from "primevue/button";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import InputText from "primevue/inputtext";
import Dropdown from "primevue/dropdown";
import Toast from "primevue/toast";
import { useToast } from "primevue/usetoast";
import axios from "axios";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faArrowLeft, faCheck, faEdit, faFilter, faPencil, faPlus, faTrashCan, faXmark } from '@fortawesome/free-solid-svg-icons';

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

// Formularios
const nuevoItem = ref({ id:null, nombre:"", pais_id:null });
const itemEdit = ref({ id:null, nombre:"", pais_id:null });
const itemEliminar = ref(null);

// Cargar datos
onMounted(() => {
  cargarPaises();
  cargarProvincias();
});

const cargarPaises = async () => {
  try {
    const res = await axios.get("/api/paises");
    paises.value = res.data;
  } catch {
    toast.add({ severity:"error", summary:"Error", detail:"No se pudieron cargar los países", life: 2000});
  }
};

const cargarProvincias = async () => {
  try {
    const res = await axios.get("/api/provincias?_expand=pais");
    provincias.value = res.data;
  } catch {
    toast.add({ severity:"error", summary:"Error", detail:"No se pudieron cargar las provincias", life: 2000 });
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
// Nuevo estado para saber qué se quiere agregar
const tipoAgregar = ref(null);

// Cuando se abre el modal, limpiamos
function abrirModalAgregar(){ 
  tipoAgregar.value = null;
  nuevoItem.value={id:null,nombre:"",pais_id:null}; 
  modalAgregar.value=true; 
}

async function guardarItem(){
  try{
    if(tipoAgregar.value==="País"){
      await axios.post("/api/paises",{nombre:nuevoItem.value.nombre});
      await cargarPaises();
    } else if(tipoAgregar.value==="Provincia"){
      await axios.post("/api/provincias",{nombre:nuevoItem.value.nombre, pais_id:nuevoItem.value.pais_id});
      await cargarProvincias();
    } else {
      toast.add({severity:"warn", summary:"Atención", detail:"Seleccione qué desea agregar", life: 2000});
      return;
    }

    toast.add({severity:"success", summary:"Guardado", detail:`${tipoAgregar.value} agregado correctamente`, life: 2000});
    modalAgregar.value=false;
  }catch{ 
    toast.add({severity:"error", summary:"Error", detail:"No se pudo guardar", life: 2000});
  }
}

function abrirModalEditar(item){ 
  itemEdit.value={...item};
  if(modoSeleccionado.value==="Provincia") itemEdit.value.pais_id=item.pais.id;
  modalEditar.value=true;
}

async function actualizarItem(){
  try{  
    if(modoSeleccionado.value==="País"){
      await axios.put(`/api/paises/${itemEdit.value.id}`, {nombre:itemEdit.value.nombre});
      await cargarPaises();
    } else {
      await axios.put(`/api/provincias/${itemEdit.value.id}`, {nombre:itemEdit.value.nombre, pais_id:itemEdit.value.pais_id});
      await cargarProvincias();
    }
    toast.add({severity:"success", summary:"Actualizado", detail:`${modoSeleccionado.value} actualizado correctamente`, life: 2000});
    modalEditar.value=false;
  }catch{ toast.add({severity:"error", summary:"Error", detail:"No se pudo actualizar", life: 2000});}
}

function confirmarEliminar(item){ itemEliminar.value=item; modalEliminar.value=true; }

async function eliminarItem(){
  try{
    if(modoSeleccionado.value==="País"){
      await axios.delete(`/api/paises/${itemEliminar.value.id}`);
      await cargarPaises();
    } else {
      await axios.delete(`/api/provincias/${itemEliminar.value.id}`);
      await cargarProvincias();
    }
    toast.add({severity:"success", summary:"Eliminado", detail:`${modoSeleccionado.value} eliminado correctamente`, life: 2000});
    modalEliminar.value=false;
  }catch{ toast.add({severity:"error", summary:"Error", detail:"No se pudo eliminar", life: 2000});}
}
</script>

<template>
  <Head title="Países y Provincias" />
  <AuthenticatedLayout>
    <Toast />
    <div class="py-4 sm:py-6 px-4 sm:px-7 mt-6 sm:mt-10 mx-auto bg-red-50 shadow-md rounded-lg h-screen-full">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-2 sm:gap-0">
        <div class="flex items-center gap-3">
          <Link :href="route('hoteles')" class="flex items-center text-blue-600 hover:text-blue-700 transition-colors duration-200 px-4 rounded-lg" title="Regresar a Productos">
            <FontAwesomeIcon :icon="faArrowLeft" class="h-8" />
          </Link>
          <h3 class="text-lg sm:text-2xl text-blue-600 font-bold">Control de Países y Provincias</h3>
        </div>
        <button
          class="bg-red-500 border border-red-500 p-2 text-sm text-white shadow-md hover:shadow-lg rounded-md hover:-translate-y-1 transition-transform duration-300" @click="abrirModalAgregar">
          <FontAwesomeIcon :icon="faPlus" class="h-4 w-4 text-white" /><span>&nbsp;Agregar categoría</span>
        </button>                
      </div>

      <!-- Tabla -->
      <DataTable
        :value="datosFiltrados"
        :rows="5"
        :paginator="datosFiltrados.length > 5"
        :rowsPerPageOptions="[5,15,30]">

        <template #header>
          <div class="bg-blue-50 rounded-lg shadow-sm border px-1 py-2">
            <div class="flex md:flex-row items-center gap-4 p-2">
              <InputText v-model="busquedaGeneral" v-if="modoSeleccionado==='Provincia'" placeholder="Buscar por provincia..." class="w-full h-10 text-sm"/>
              <InputText v-model="busquedaGeneral" v-else="modoSeleccionado==='País'" placeholder="Buscar por país..." class="w-full h-10 text-sm"/>
              <label for="tipo-estado" class="font-semibold mb-0 flex items-center gap-2">
                <FontAwesomeIcon :icon="faFilter" class="h-4 w-4 text-blue-700"/>Filtrar:
              </label>
              <select id="tipo-estado" v-model="modoSeleccionado" class="p-2 rounded border border-gray-300 w-56">
                <option value="País">Países</option>
                <option value="Provincia">Provincias</option>
              </select>
            </div>
          </div>
        </template>

        <Column field="nombre" header="Nombre" class="w-36 min-w-38"/>
        <Column v-if="modoSeleccionado==='Provincia'" field="pais.nombre" header="País" class="w-28 min-w-28"/>
        <Column :exportable="false" class="w-32 min-w-48">
          <template #header>
              <div class="text-center w-full font-bold">
                  Acciones
              </div>
          </template>
          <template #body="slotProps">
            <div class="flex gap-1 justify-center items-center">
                <button
                    class="bg-orange-200/30 border py-2 px-1 text-sm shadow-md hover:shadow-lg rounded-md hover:-translate-y-1 transition-transform duration-300"
                    @click="abrirModalEditar(slotProps.data)">
                    <FontAwesomeIcon :icon="faPencil" class="h-4 w-4 text-orange-600" />
                    &nbsp;Editar
                </button>
                <button
                    class="bg-red-200/30 border py-2 px-1 text-sm shadow-md hover:shadow-lg rounded-md hover:-translate-y-1 transition-transform duration-300"
                    @click="confirmarEliminar(slotProps.data)">
                    <FontAwesomeIcon :icon="faTrashCan" class="h-4 w-4 text-red-600" />
                    &nbsp;Eliminar
                </button>
            </div>
          </template>
        </Column>
      </DataTable>

      <Dialog v-model:visible="modalAgregar" header="Agregar" :modal="true" :closable="false" style="width:350px">
        <div class="flex flex-col gap-3">
          <!-- Selección de qué agregar -->
              <Dropdown
                v-model="tipoAgregar"
                :options="[
                  { label: 'País', value: 'País' },
                  { label: 'Provincia', value: 'Provincia' }
                ]"
                optionLabel="label"
                optionValue="value"
                placeholder="Seleccione qué desea agregar"
                class="w-full"
              />

          <!-- Campo nombre -->
          <InputText 
            v-model="nuevoItem.nombre" 
            placeholder="Nombre" 
            class="w-full" 
            :disabled="!tipoAgregar"
          />

          <!-- Dropdown país SOLO si se elige Provincia -->
          <Dropdown
            v-if="tipoAgregar==='Provincia'"
            v-model="nuevoItem.pais_id"
            :options="paises"
            optionLabel="nombre"
            optionValue="id"
            placeholder="Seleccione un país"
            class="w-full"
          />
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
              class="bg-red-500 hover:bg-red-700 text-white border-none px-6 py-2 rounded-md transition-all duration-200 ease-in-out flex items-center gap-2" 
              @click="guardarItem"
              :disabled="!nuevoItem.nombre || !tipoAgregar">
              <FontAwesomeIcon :icon="faCheck" class="h-5 text-white" />
              Guardar
            </button>
          </div>
        </template>
      </Dialog>

      <!-- Modal Editar -->
      <Dialog v-model:visible="modalEditar" :header="`Editar ${modoSeleccionado}`" :modal="true" :closable="false" style="width:350px">
        <div class="flex flex-col gap-3">
          <Dropdown
            v-if="modoSeleccionado==='Provincia'"
            v-model="itemEdit.pais_id"
            :options="paises"
            optionLabel="nombre"
            optionValue="id"
            placeholder="Seleccione un país"
            class="w-full"
          />
          <InputText v-model="itemEdit.nombre" placeholder="Nombre" class="w-full" />
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
              class="bg-red-500 hover:bg-red-700 text-white border-none px-6 py-2 rounded-md transition-all duration-200 ease-in-out flex items-center gap-2" 
              @click="actualizarItem"
              :disabled="!itemEdit.nombre">
              <FontAwesomeIcon :icon="faCheck" class="h-5 text-white" />
              Actualizar
            </button>
          </div>
        </template> 
      </Dialog>

      <!-- Confirmar eliminar -->
      <Dialog v-model:visible="modalEliminar" :header="`Eliminar ${modoSeleccionado}`" :modal="true" :closable="false" style="width:350px">
        <div class="mb-4">¿Seguro que deseas eliminar <b>{{ itemEliminar?.nombre }}</b>?</div>
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
              :disabled="!itemEliminar.nombre">
              <FontAwesomeIcon :icon="faTrashCan" class="h-5 text-white" />
              Eliminar
            </button>
          </div>
        </template>
      </Dialog>
    </div>
  </AuthenticatedLayout>
</template>