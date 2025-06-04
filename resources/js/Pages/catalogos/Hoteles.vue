<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { useToast } from 'primevue/usetoast';
import { FilterMatchMode } from '@primevue/core/api';
import FileUpload from 'primevue/fileupload';
import Toast from 'primevue/toast';
import Dropdown from 'primevue/dropdown';

const toast = useToast();

const hoteles = ref([]);  
const hotel = ref({ id: null, nombre: '', direccion: '', descripcion: '', estado: '', pais: '', provincia: '', categoria: '', imagenes: [] });
const imagenPreviewList = ref([]);
const selectedHoteles = ref();
const dialog = ref(false);
const deleteDialog = ref(false);
const submitted = ref(false);
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});
const btnTitle = ref('Guardar');

onMounted(() => {
    fetchHoteles();
});

const fetchHoteles = async () => {
    hoteles.value = [
        { id: 1, nombre: 'Hotel Sol de Oro', direccion:'Hotel Sol de Oro, Av. Pardo Miraflores Calle San Martín 610', pais: 'Peru', provincia: 'Lima', imagenes: [] },
        { id: 2, nombre: 'Hotel B', imagenes: [] },
        { id: 3, nombre: 'Hotel C', imagenes: [] },
        { id: 4, nombre: 'Hotel D', imagenes: [] },
        { id: 5, nombre: 'Hotel E', imagenes: [] },
    ];
};

const openNew = () => {
    hotel.value = { id: null, nombre: '', direccion: '', descripcion: '', estado: '', pais: '', provincia: '', categoria: '', imagenes: [] };
    imagenPreviewList.value = [];
    submitted.value = false;
    btnTitle.value = 'Guardar';
    dialog.value = true;
};

const editHotel = (h) => {
    hotel.value = { ...h };
    imagenPreviewList.value = h.imagenes || [];
    btnTitle.value = 'Actualizar';
    dialog.value = true;
};

const saveOrUpdate = () => {
    submitted.value = true;

    if (
        !hotel.value.nombre ||
        !hotel.value.direccion ||
        !hotel.value.descripcion ||
        !hotel.value.estado ||
        !hotel.value.pais ||
        !hotel.value.provincia ||
        !hotel.value.categoria ||
        imagenPreviewList.value.length === 0
    ) return;

    // Verificar si ya existe un hotel con el mismo nombre (excepto en edición del mismo)
    const nombreExiste = hoteles.value.some(h =>
        h.nombre.toLowerCase().trim() === hotel.value.nombre.toLowerCase().trim() &&
        h.id !== hotel.value.id
    );

    if (nombreExiste) {
        toast.add({
            severity: 'error',
            summary: 'Nombre duplicado',
            detail: `Ya tienes un hotel con el nombre "${hotel.value.nombre}".`,
            life: 4000
        });
        return;
    }

    hotel.value.imagenes = [...imagenPreviewList.value];

    if (hotel.value.id === null) {
        hotel.value.id = Date.now();
        hoteles.value.push({ ...hotel.value });
        toast.add({ severity: 'success', summary: 'Hotel agregado', life: 3000 });
    } else {
        const index = hoteles.value.findIndex(h => h.id === hotel.value.id);
        if (index !== -1) {
            hoteles.value[index] = { ...hotel.value };
            toast.add({ severity: 'info', summary: 'Hotel actualizado', life: 3000 });
        }
    }

    dialog.value = false;
    hotel.value = { id: null, nombre: '', direccion: '', descripcion: '', estado: '', pais: '', provincia: '', categoria: '', imagenes: [] };
    imagenPreviewList.value = [];
};

const confirmDeleteHotel = (h) => {
    hotel.value = { ...h };
    deleteDialog.value = true;
};

const deleteHotel = () => {
    hoteles.value = hoteles.value.filter(h => h.id !== hotel.value.id);
    deleteDialog.value = false;
    toast.add({ severity: 'warn', summary: 'Hotel eliminado', life: 3000 });
};

const hideDialog = () => {
    dialog.value = false;
};

const onImageSelect = (event) => {
    for (const file of event.files) {
        const reader = new FileReader();
        reader.onload = (e) => {
            imagenPreviewList.value.push(e.target.result);
        };
        reader.readAsDataURL(file);
    }
};

const removeImage = (index) => {
    imagenPreviewList.value.splice(index, 1);
};

const paises = ref([
    { nombre: 'Peru' },
    { nombre: 'Argentina' },
    { nombre: 'Chile' },
    { nombre: 'Colombia' },
    { nombre: 'México' },
    { nombre: 'España' },
    // Agrega más países según necesidad
]);

const provincias = ref([
    { nombre: 'Lima' },
    { nombre: 'Buenos Aires' },
    { nombre: 'Santiago' },
    { nombre: 'Bogotá' },
    { nombre: 'CDMX' },
    { nombre: 'Madrid' },
    // Agrega más provincias según necesidad
]);

function sanitizeDropdownInput(value) {
    // Solo letras, tildes y sin espacios iniciales
    return value.replace(/^\s+/, '').replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]/g, '');
}

function onInputDropdown(field, event) {
    if (typeof event === 'string') {
        hotel.value[field] = sanitizeDropdownInput(event);
    }
}

function onFilterDropdown(field, event) {
    if (event && event.value) {
        event.value = sanitizeDropdownInput(event.value);
    }
}
</script>

<template>
    <Head title="Hoteles" />
    <AuthenticatedLayout>
    <Toast />
        <div class="py-6 px-7 mt-10 mx-auto bg-red-100 shadow-md rounded-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Catálogo hoteles</h3>
                <Button label="Agregar hotel" icon="pi pi-plus" class="p-button-sm p-button-danger" @click="openNew" />
            </div>

            <DataTable :value="hoteles" v-model:selection="selectedHoteles" dataKey="id" :filters="filters" :paginator="true" :rows="4" class="overflow-x-auto max-w-full" style="display:block; max-width:84vw;">
                <template #header>
                    <InputText v-model="filters['global'].value" placeholder="Buscar..." class="w-full" />
                </template>
                <Column field="nombre" header="Nombre" sortable />
                <Column field="direccion" header="Dirección">
                    <template #body="slotProps">
                       <div style="max-height: 80px; width: 300px; overflow-y: auto; overflow-x: auto; background: #fff; border-radius: 4px; padding: 4px 8px; font-size: 0.95em; box-shadow: 0 1px 2px rgba(0,0,0,0.03); white-space: pre-line; word-break: break-word; scrollbar-width: thin;">
                            {{ slotProps.data.direccion }}
                        </div>
                    </template>
                </Column>
                <Column field="descripcion" header="Descripción">
                    <template #body="slotProps">
                        <div style="max-height: 80px; width: 300px; overflow-y: auto; overflow-x: auto; background: #fff; border-radius: 4px; padding: 4px 8px; font-size: 0.95em; box-shadow: 0 1px 2px rgba(0,0,0,0.03); white-space: pre-line; word-break: break-word; scrollbar-width: thin;">
                            {{ slotProps.data.descripcion }}
                        </div>
                    </template>
                </Column>
                <Column field="estado" header="Estado" />
                <Column field="categoria" header="Categoría" />
                <Column field="pais" header="País" />
                <Column field="provincia" header="Provincia" />
                <Column header="Imágenes">
                    <template #body="slotProps">
                        <div class="flex gap-1">
                            <img v-for="(img, index) in slotProps.data.imagenes" :key="index" :src="img" alt="img" class="w-10 h-10 object-cover rounded" />
                        </div>
                    </template>
                </Column>
                <Column header="Acciones" :exportable="false">
                    <template #body="slotProps">
                        <div class="flex gap-2">
                         <Button icon="pi pi-pencil" class="p-button-rounded p-button-warn p-button-md" @click="editHotel(slotProps.data)" />
                         <Button icon="pi pi-trash" class="p-button-rounded p-button-danger p-button-md" @click="confirmDeleteHotel(slotProps.data)" />
                        </div>
                    </template>
                </Column>
            </DataTable>

            <Dialog v-model:visible="dialog" :header="btnTitle + ' Hotel'" :modal="true" :style="{ width: '500px' }">
                <div class="space-y-4">
                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="nombre" class="w-24">Nombre:</label>
                            <InputText v-model.trim="hotel.nombre" id="nombre" :class="{ 'p-invalid': submitted && !hotel.nombre }" class="flex-1" />
                        </div>
                         <small class="text-red-500 ml-28" v-if="submitted && !hotel.nombre">El nombre es obligatorio.</small>
                    </div>

                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="direccion" class="w-24">Dirección:</label>
                            <textarea v-model.trim="hotel.direccion" id="direccion" :class="{ 'p-invalid': submitted && !hotel.direccion }" class="flex-1" />
                        </div>
                        <small class="text-red-500 ml-28" v-if="submitted && !hotel.direccion">La dirección es obligatoria.</small>
                    </div>

                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="descripcion" class="w-24">Descripción:</label>
                            <textarea v-model.trim="hotel.descripcion" id="descripcion" :class="{ 'p-invalid': submitted && !hotel.descripcion }" class="flex-1" />
                        </div>
                        <small class="text-red-500 ml-28" v-if="submitted && !hotel.descripcion">La descripción es obligatoria.</small>
                    </div>

                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="estado" class="w-24">Estado:</label>
                            <InputText v-model.trim="hotel.estado" id="estado" :class="{ 'p-invalid': submitted && !hotel.estado }" class="flex-1" />
                        </div>
                        <small class="text-red-500 ml-28" v-if="submitted && !hotel.estado">El estado es obligatorio.</small>
                    </div>

                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="categoria" class="w-24">Categoría:</label>
                            <InputText v-model.trim="hotel.categoria" id="categoria" :class="{ 'p-invalid': submitted && !hotel.categoria }" class="flex-1" />
                        </div>
                        <small class="text-red-500 ml-28" v-if="submitted && !hotel.categoria">La categoría es obligatoria.</small>
                    </div>

                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="pais" class="w-24">País:</label>
                            <Dropdown v-model="hotel.pais" :options="paises" optionLabel="nombre" optionValue="nombre" id="pais" :filter="true" filterPlaceholder="Buscar país..." :showClear="true" :class="{ 'p-invalid': submitted && !hotel.pais }" class="flex-1" placeholder="Selecciona un país"
    @input="onInputDropdown('pais', $event)"
    @filter="onFilterDropdown('pais', $event)"
/>
                        </div>
                        <small class="text-red-500 ml-28" v-if="submitted && !hotel.pais">El país es obligatorio.</small>
                    </div>

                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="provincia" class="w-24">Provincia:</label>
                            <Dropdown v-model="hotel.provincia" :options="provincias" optionLabel="nombre" optionValue="nombre" id="provincia" :filter="true" filterPlaceholder="Buscar provincia..." :showClear="true" :class="{ 'p-invalid': submitted && !hotel.provincia }" class="flex-1" placeholder="Selecciona una provincia"
    @input="onInputDropdown('provincia', $event)"
    @filter="onFilterDropdown('provincia', $event)"
/>
                        </div>
                        <small class="text-red-500 ml-28" v-if="submitted && !hotel.provincia">La provincia es obligatoria.</small>
                    </div>

                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="imagen" class="w-24">Imágenes:</label>
                            <FileUpload mode="basic" name="imagenes[]" accept="image/*" :auto="true" chooseLabel="Seleccionar imágenes"  @select="onImageSelect" :customUpload="true" :multiple="true" class="flex-1  p-button-rounded p-button-warn p-button-md mr-2" />
                        </div>
                        <small class="text-red-500 ml-28" v-if="submitted && imagenPreviewList.length === 0">Al menos una imagen es obligatoria.</small>
                    </div>

                    <div class="flex gap-4 flex-wrap mt-4 ml-28">
                        <div v-for="(img, index) in imagenPreviewList" :key="index" class="relative w-32 h-32">
                            <img :src="img" alt="Vista previa" class="w-full h-full object-cover rounded border" />
                            <button @click="removeImage(index)" class="absolute top-0 right-0 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 shadow" style="transform: translate(50%, -50%)">
                                <i class="pi pi-times text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <template #footer>
                    <div class="flex justify-center gap-4 w-full">
                        <Button label="Cancelar" icon="pi pi-times" class="p-button-rounded p-button-danger p-button-md mr-2" text @click="hideDialog" />
                        <Button :label="btnTitle" icon="pi pi-check"  class="p-button-rounded p-button-warn p-button-md mr-2" @click="saveOrUpdate" />
                    </div>
                </template>
            </Dialog>

            <Dialog v-model:visible="deleteDialog" header="Confirmar" :modal="true" :style="{ width: '350px' }">
                <span>¿Eliminar el hotel <b>{{ hotel.nombre }}</b>?</span>
                <template #footer>
                    <Button label="No" icon="pi pi-times" text @click="deleteDialog = false" class="p-button-rounded p-button-warn p-button-md mr-2" />
                    <Button label="Sí" icon="pi pi-check" severity="danger" @click="deleteHotel" />
                </template>
            </Dialog>
        </div>
    </AuthenticatedLayout>
</template>