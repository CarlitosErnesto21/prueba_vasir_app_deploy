<template>
  <Head title="Países y Provincias" />
  <AuthenticatedLayout>
    <Toast />
    <div class="py-6 px-2 sm:px-4 md:px-6 mt-10 mx-auto bg-red-50 shadow-md rounded-lg max-w-3xl">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold">Control de Países y Provincias</h3>
        <Button :label="`Agregar ${modoSeleccionado}`" 
              icon="pi pi-plus"
              class="p-button-danger" 
              @click="abrirModalAgregar" />
      </div>

        <!-- Selector de modo y buscador -->
      <div class="flex flex-col md:flex-row items-center gap-4 mt-6 mb-2">
        <label for="tipo-estado" class="font-semibold mb-0">Ver:</label>
        <select
          id="tipo-estado"
          v-model="modoSeleccionado"
          class="p-2 rounded border border-gray-300 w-36"
        >
          <option value="País">Países</option>
          <option value="Provincia">Provincias</option>
        </select>

        <InputText
          v-model="busquedaGeneral"
          placeholder="Buscar por nombre..."
          class="w-64"
        />
      </div>

      <!-- Tabla -->
      <DataTable
        :value="datosFiltrados"
        class="mb-4 min-w-[400px] w-full"
        :rows="8"
        :paginator="datosFiltrados.length > 8"
        :rowsPerPageOptions="[8,16]"
      >
        <Column field="nombre" header="Nombre" />
        <Column v-if="modoSeleccionado==='Provincia'" field="pais.nombre" header="País" />
        <Column header="Acciones">
          <template #body="slotProps">
            <Button label="" icon="pi pi-pencil" class="p-button-rounded p-button-warn mr-2" @click="abrirModalEditar(slotProps.data)" />
            <Button label="" icon="pi pi-trash" class="p-button-rounded p-button-danger" @click="confirmarEliminar(slotProps.data)" />
          </template>
        </Column>
      </DataTable>

      <!-- Modal Agregar -->
      <Dialog v-model:visible="modalAgregar" :header="`Agregar ${modoSeleccionado}`" :modal="true" :closable="false" style="width:350px">
        <div class="flex flex-col gap-3">
          <InputText v-model="nuevoItem.nombre" placeholder="Nombre" class="w-full" />
          <Dropdown
            v-if="modoSeleccionado==='Provincia'"
            v-model="nuevoItem.pais_id"
            :options="paises"
            optionLabel="nombre"
            optionValue="id"
            placeholder="Seleccione un país"
            class="w-full"
          />
        </div>
        <template #footer>
         <Button 
          label="Cancelar" 
          icon="pi pi-times"
          class="p-button-rounded p-button-danger p-button-sm mr-2"
          @click="modalAgregar=false" 
        />

         <Button 
          label="Guardar" 
          icon="pi pi-check" 
          class="p-button-rounded p-button-warn p-button-sm mr-2" 
          @click="guardarItem" 
          :disabled="!nuevoItem.nombre" 
        />

        </template>
      </Dialog>

      <!-- Modal Editar -->
      <Dialog v-model:visible="modalEditar" :header="`Editar ${modoSeleccionado}`" :modal="true" :closable="false" style="width:350px">
        <div class="flex flex-col gap-3">
          <InputText v-model="itemEdit.nombre" placeholder="Nombre" class="w-full" />
          <Dropdown
            v-if="modoSeleccionado==='Provincia'"
            v-model="itemEdit.pais_id"
            :options="paises"
            optionLabel="nombre"
            optionValue="id"
            placeholder="Seleccione un país"
            class="w-full"
          />
        </div>
        <template #footer>
          <Button 
            label="Cancelar" 
            icon="pi pi-times" 
            class="p-button-rounded p-button-danger p-button-md mr-2"
            @click="modalEditar=false" 
            />
          <Button 
            label="Actualizar" 
            icon="pi pi-check" 
            class="p-button-rounded p-button-warn p-button-md mr-2"
            @click="actualizarItem"
            :disabled="!itemEdit.nombre" 
            />
        </template> 
      </Dialog>

      <!-- Confirmar eliminar -->
      <Dialog v-model:visible="modalEliminar" :header="`Eliminar ${modoSeleccionado}`" :modal="true" :closable="false" style="width:350px">
        <div class="mb-4">¿Seguro que deseas eliminar <b>{{ itemEliminar?.nombre }}</b>?</div>
        <template #footer>
          <Button
            label="Cancelar" 
            icon="pi pi-times"
            class="p-button-rounded p-button-danger p-button-md mr-2"
            @click="modalEliminar=false" 
            />
          <Button
            label="Eliminar" 
            icon="pi pi-trash"
            class="p-button-rounded p-button-warn p-button-md mr-2"
            @click="eliminarItem"
            />
        </template>
      </Dialog>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { Head } from "@inertiajs/vue3";
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
    toast.add({ severity:"error", summary:"Error", detail:"No se pudieron cargar los países" });
  }
};

const cargarProvincias = async () => {
  try {
    const res = await axios.get("/api/provincias?_expand=pais");
    provincias.value = res.data;
  } catch {
    toast.add({ severity:"error", summary:"Error", detail:"No se pudieron cargar las provincias" });
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
function abrirModalAgregar(){ nuevoItem.value={id:null,nombre:"",pais_id:null}; modalAgregar.value=true; }

async function guardarItem(){
  try{
    if(modoSeleccionado.value==="País"){
      await axios.post("/api/paises",{nombre:nuevoItem.value.nombre});
      await cargarPaises();
    } else {
      await axios.post("/api/provincias",{nombre:nuevoItem.value.nombre, pais_id:nuevoItem.value.pais_id});
      await cargarProvincias();
    }
    toast.add({severity:"success", summary:"Guardado", detail:`${modoSeleccionado.value} agregado correctamente`});
    modalAgregar.value=false;
  }catch{ toast.add({severity:"error", summary:"Error", detail:"No se pudo guardar"});}
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
    toast.add({severity:"success", summary:"Actualizado", detail:`${modoSeleccionado.value} actualizado correctamente`});
    modalEditar.value=false;
  }catch{ toast.add({severity:"error", summary:"Error", detail:"No se pudo actualizar"});}
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
    toast.add({severity:"warn", summary:"Eliminado", detail:`${modoSeleccionado.value} eliminado correctamente`});
    modalEliminar.value=false;
  }catch{ toast.add({severity:"error", summary:"Error", detail:"No se pudo eliminar"});}
}
</script>