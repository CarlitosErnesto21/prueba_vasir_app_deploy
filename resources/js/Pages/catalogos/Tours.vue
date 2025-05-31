<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { useToast } from 'primevue/usetoast';
import { FilterMatchMode } from '@primevue/core/api';
import Calendar from 'primevue/calendar';

const toast = useToast();

const tours = ref([]);
const tour = ref({ id: null, nombre: '', precio: null, imagen: null, descripcion: '', puntoSalida: '', horaRegreso: null, fecha: null });
const imagenPreview = ref(null);
const imagenPreviewList = ref([]); // Para previews
const imagenFileList = ref([]);    // Para archivos reales
const selectedTours = ref();
const dialog = ref(false);
const deleteDialog = ref(false);
const submitted = ref(false);
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});
const btnTitle = ref('Guardar');

onMounted(() => {
    fetchTours();
});

const fetchTours = async () => {
    // Simular datos de ejemplo
    tours.value = [
        { id: 1, nombre: 'Tour A', precio: 10.99 },
        { id: 2, nombre: 'Tour B', precio: 20.5 },
        { id: 3, nombre: 'Tour C', descripcion: 'tour a machu pichu', puntoSalida: 'chalatenango',  precio: 10.99 },
        { id: 4, nombre: 'Tour D', descripcion: 'tour a machu pichu', puntoSalida: 'chalatenango',  precio: 10.99 },
    ];
};

const openNew = () => {
    tour.value = { id: null, nombre: '', precio: null };
    submitted.value = false;
    btnTitle.value  = 'Guardar';
    dialog.value = true;
};

const editTour = (t) => {
    tour.value = { ...t };
    // Si el tour ya tiene imágenes, mostrarlas en el preview
    if (t.imagenes && Array.isArray(t.imagenes) && t.imagenes.length > 0) {
        imagenPreviewList.value = [...t.imagenes];
    } else if (t.imagen) {
        imagenPreviewList.value = [t.imagen];
    } else {
        imagenPreviewList.value = [];
    }
    imagenFileList.value = [];
    btnTitle.value = 'Actualizar';
    dialog.value = true;
};

const saveOrUpdate = () => {
    submitted.value = true;

    // Validar todos los campos requeridos y al menos una imagen
    if (!tour.value.nombre || !tour.value.descripcion || !tour.value.puntoSalida || !tour.value.horaRegreso || !tour.value.fecha || !tour.value.precio || imagenPreviewList.value.length === 0 || tour.value.precio <= 0 || tour.value.precio > 9999.99) {
        return;
    }

    // Verificar si ya existe un tour con el mismo nombre (excepto en edición del mismo)
    const nombreExiste = tours.value.some(p =>
        p.nombre.toLowerCase().trim() === tour.value.nombre.toLowerCase().trim() &&
        p.id !== tour.value.id
    );
    if (nombreExiste) {
        toast.add({
            severity: 'error',
            summary: 'Nombre duplicado',
            detail: `Ya tienes un tour con el nombre "${tour.value.nombre}".`,
            life: 4000
        });
        return;
    }

    // Guardar imágenes en el tour
    tour.value.imagenes = [...imagenPreviewList.value];
    // Para mostrar una imagen principal en la tabla
    tour.value.imagen = imagenPreviewList.value[0] || null;

    if (tour.value.id === null) {
        tour.value.id = Date.now();
        tours.value.push({ ...tour.value });
        toast.add({ severity: 'success', summary: 'Tour agregado', detail: 'El tour fue agregado correctamente.', life: 4000 });
    } else {
        const index = tours.value.findIndex(p => p.id === tour.value.id);
        if (index !== -1) {
            tours.value[index] = { ...tour.value };
            toast.add({ severity: 'info', summary: 'Tour actualizado', detail: 'El tour fue actualizado correctamente.', life: 4000 });
        }
    }

    dialog.value = false;
    tour.value = { id: null, nombre: '', descripcion: '', precio: null, imagen: null, puntoSalida: '', horaRegreso: null, fecha: null, imagenes: [] };
    imagenPreview.value = null;
    imagenPreviewList.value = [];
    imagenFileList.value = [];
    submitted.value = false;
};

const confirmDeleteTour = (t) => {
    tour.value = { ...t };
    deleteDialog.value = true;
};

const deleteTour = () => {
    const tourEliminado = { ...tour.value };
    tours.value = tours.value.filter(p => p.id !== tour.value.id);
    deleteDialog.value = false;
    toast.add({ 
        severity: 'warn', 
        summary: 'Tour eliminado', 
        detail: `El tour "${tourEliminado.nombre}" fue eliminado correctamente.`, 
        life: 4000 
    });
};

const hideDialog = () => {
    dialog.value = false;
    // Limpiar todos los campos y arrays de imágenes al cerrar/cancelar
    tour.value = { id: null, nombre: '', descripcion: '', precio: null, imagen: null, puntoSalida: '', horaRegreso: null, fecha: null, imagenes: [] };
    imagenPreview.value = null;
    imagenPreviewList.value = [];
    imagenFileList.value = [];
    submitted.value = false;
};

const onImageSelect = (event) => {
    for (const file of event.files) {
        imagenFileList.value.push(file);
        const reader = new FileReader();
        reader.onload = (e) => {
            imagenPreviewList.value.push(e.target.result);
        };
        reader.readAsDataURL(file);
    }
};

const onImageClear = () => {
    imagenPreview.value = null;
    imagenPreviewList.value = [];
    imagenFileList.value = [];
};

const removeImage = (index) => {
    imagenPreviewList.value.splice(index, 1);
    imagenFileList.value.splice(index, 1);
};

</script>

<template>
    <Head title="Tours" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Tours</h2>
        </template>

        <div class="py-6 px-6 mt-10 mx-auto bg-red-100 shadow-md rounded-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Gestión de Tours</h3>
                <Button label="Agregar tour" icon="pi pi-plus" class="p-button-sm p-button-danger" @click="openNew" />
            </div>

            <DataTable :value="tours" v-model:selection="selectedTours" dataKey="id" :filters="filters" :paginator="true" :rows="4 ">
                <template #header>
                    <InputText v-model="filters['global'].value" placeholder="Buscar..." class="w-full" />
                </template>
                <Column field="nombre" header="Nombre" sortable />
                <Column field="descripcion" header="Descripcion" />
                <Column field="puntoSalida" header="Punto de salida"  />
                <Column field="fecha" header="Fecha y hora de salida">
                    <template #body="slotProps">
                        {{ slotProps.data.fecha ? new Date(slotProps.data.fecha).toLocaleString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true }) : '' }}
                    </template>
                </Column>
                <Column field="horaRegreso" header="Hora de regreso">
                    <template #body="slotProps">
                        {{ slotProps.data.horaRegreso ? new Date(slotProps.data.horaRegreso).toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', hour12: true }) : '' }}
                    </template>
                </Column>
                <Column field="precio" header="Precio" >
                    <template #body="slotProps">
                        ${{ slotProps.data.precio.toFixed(2) }}
                    </template>
                </Column>
                <Column header="Imagen">
                    <template #body="slotProps">
                        <img v-if="slotProps.data.imagen" :src="slotProps.data.imagen" alt="Imagen" class="w-16 h-16 object-cover rounded" />
                        <span v-else class="text-gray-400">Sin imagen</span>
                    </template>
                </Column>
                <Column header="Acciones" :exportable="false">
                    <template #body="slotProps">
                        <Button 
                          icon="pi pi-pencil" 
                          class="p-button-rounded p-button-warn p-button-md mr-2"
                          @click="editTour(slotProps.data)" 
                        />
                        <Button
                         icon="pi pi-trash" 
                         class="p-button-rounded p-button-danger p-button-md mr-2" 
                         @click="confirmDeleteTour(slotProps.data)" 
                        />
                    </template>
                </Column>
            </DataTable>

            <!-- Modal Producto -->
            <Dialog v-model:visible="dialog" :header="btnTitle + ' Tour'" :modal="true" :style="{ width: '400px' }">
                <div class="p-fluid space-y-6">
                    <div>
                        <label for="nombre" class="block mb-2">Nombre</label>
                        <InputText v-model.trim="tour.nombre" id="nombre" required :class="{ 'p-invalid': submitted && !tour.nombre }" class="w-full" />
                        <small class="text-red-500" v-if="submitted && !tour.nombre">El nombre es obligatorio</small>
                    </div>
                    <div>
                        <label for="descripcion" class="block mb-2">Descripción</label>
                        <InputText v-model.trim="tour.descripcion" id="descripcion" required :class="{ 'p-invalid': submitted && !tour.descripcion}" class="w-full" />
                        <small class="text-red-500" v-if="submitted && !tour.descripcion">La descripcion es obligatoria</small>
                    </div>
                    <div>
                        <label for="puntoSalida" class="block mb-2">Punto de salida</label>
                        <InputText v-model.trim="tour.puntoSalida" id="puntoSalida" required :class="{ 'p-invalid': submitted && !tour.puntoSalida}" class="w-full" />
                        <small class="text-red-500" v-if="submitted && !tour.puntoSalida">Punto de salida es requerido.</small>
                    </div>
                    <div>
                        <label for="horaRegreso" class="block mb-2">Hora de regreso</label>
                        <Calendar v-model="tour.horaRegreso" id="horaRegreso" :showIcon="true" timeOnly hourFormat="12" :class="{ 'p-invalid': submitted && !tour.horaRegreso }" class="w-full">
                            <template #inputicon>
                                <i class="pi pi-clock" />
                            </template>
                        </Calendar>
                        <small class="text-red-500" v-if="submitted && !tour.horaRegreso">Hora de regreso es requerido.</small>
                    </div>
                    <div>
                        <label for="fecha" class="block mb-2">Fecha y hora de salida</label>
                        <Calendar v-model="tour.fecha" id="fecha" :showIcon="true" :showTime="true" hourFormat="12" dateFormat="yy-mm-dd" :class="{ 'p-invalid': submitted && !tour.fecha }" class="w-full" />
                        <small class="text-red-500" v-if="submitted && !tour.fecha">Fecha y hora requerida.</small>
                    </div>
                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="precio" class="w-24">Precio:</label>
                            <InputNumber
                            v-model="tour.precio"
                            id="precio"
                            mode="currency"
                            currency="USD"
                            :locale="'en-US'"
                            :min="0.01"
                            :max="9999.99"
                            :maxFractionDigits="2"
                            :minFractionDigits="2"
                            :class="{ 'p-invalid': submitted && (!tour.precio || tour.precio <= 0 || tour.precio > 999999.99) }"
                            class="flex-1"
                            />

                        </div>
                        <small
                        class="text-red-500 ml-28"
                        v-if="submitted && (tour.precio == null || tour.precio <= 0 || tour.precio > 999999.99)"
                        >
                        El precio es obligatorio, debe ser mayor a 0 y menor o igual a 9999.99.
                        </small>
                    </div>
                    <div>     
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
                </div>
                <!-- ...dentro de tu Dialog, después del campo de precio... -->
                <template #footer>
                    <div class="flex justify-center gap-4 w-full">
                        <Button label="Cancelar" icon="pi pi-times" class="p-button-rounded p-button-danger p-button-md mr-2" text @click="hideDialog" />
                        <Button :label="btnTitle" icon="pi pi-check"  class="p-button-rounded p-button-warn p-button-md mr-2" @click="saveOrUpdate" />
                    </div>
                </template>
            </Dialog>

            <!-- Confirmación de eliminación -->
            <Dialog v-model:visible="deleteDialog" header="Confirmar" :modal="true" :style="{ width: '350px' }">
                <span>¿Eliminar el tour <b>{{ tour.nombre }}</b>?</span>
                 <template #footer>
                    <Button label="No" icon="pi pi-times" text @click="deleteDialog = false" class="p-button-rounded p-button-warn p-button-md mr-2" />
                    <Button label="Sí" icon="pi pi-check" severity="danger" @click="deleteTour" />
                </template>
            </Dialog>
        </div>
    </AuthenticatedLayout>
</template>