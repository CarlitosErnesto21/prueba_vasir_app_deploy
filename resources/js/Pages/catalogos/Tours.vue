<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';
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
const horaRegresoInput = ref('');
const horaRegresoAMPM = ref('AM');
const horaRegresoCalendar = ref(null);

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
    horaRegresoInput.value = '';
    horaRegresoAMPM.value = 'AM';
    horaRegresoCalendar.value = null;
    submitted.value = false;
    btnTitle.value  = 'Agregar';
    dialog.value = true;
};

const editTour = (t) => {
    tour.value = { ...t };
    if (t.horaRegreso) {
        const [hora, ampm] = t.horaRegreso.split(' ');
        horaRegresoInput.value = hora || '';
        horaRegresoAMPM.value = ampm || 'AM';
        syncCalendarFromInput();
    } else {
        horaRegresoInput.value = '';
        horaRegresoAMPM.value = 'AM';
        horaRegresoCalendar.value = null;
    }
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

// Cuando el usuario selecciona en el Calendar, sincroniza los inputs manuales
function onHoraRegresoCalendarChange(e) {
    if (!horaRegresoCalendar.value) return;
    const date = new Date(horaRegresoCalendar.value);
    let hours = date.getHours();
    let minutes = date.getMinutes();
    let ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    if (hours === 0) hours = 12;
    const hh = hours.toString().padStart(2, '0');
    const mm = minutes.toString().padStart(2, '0');
    horaRegresoInput.value = `${hh}:${mm}`;
    horaRegresoAMPM.value = ampm;
}

// Cuando el usuario edita manualmente, sincroniza el Calendar (opcional, solo si el input es válido)
function syncCalendarFromInput() {
    const [hhmm, ampm] = [horaRegresoInput.value, horaRegresoAMPM.value];
    if (!hhmm || !ampm) return;
    const [hh, mm] = hhmm.split(':');
    let hours = parseInt(hh, 10);
    if (ampm === 'PM' && hours < 12) hours += 12;
    if (ampm === 'AM' && hours === 12) hours = 0;
    const date = new Date();
    date.setHours(hours);
    date.setMinutes(parseInt(mm, 10) || 0);
    date.setSeconds(0);
    horaRegresoCalendar.value = date;
}

const saveOrUpdate = () => {
    submitted.value = true;

    // Validar todos los campos requeridos y al menos una imagen
    if (!tour.value.nombre || !tour.value.descripcion || !tour.value.puntoSalida || !horaRegresoInput.value || !horaRegresoAMPM.value || !tour.value.fecha || !tour.value.precio || imagenPreviewList.value.length === 0 || tour.value.precio <= 0 || tour.value.precio > 9999.99) {
        return;
    }
    tour.value.horaRegreso = horaRegresoInput.value + ' ' + horaRegresoAMPM.value;

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
    horaRegresoInput.value = '';
    horaRegresoAMPM.value = 'AM';
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
    tour.value = { id: null, nombre: '', descripcion: '', precio: null, imagen: null, puntoSalida: '', horaRegreso: null, fecha: null, imagenes: [] };
    horaRegresoInput.value = '';
    horaRegresoAMPM.value = 'AM';
    horaRegresoCalendar.value = null;
    // Limpiar todos los campos y arrays de imágenes al cerrar/cancelar
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

// Utilidad para formatear hora a HH:mm
function formatTimeInput(value) {
    // Elimina todo lo que no sea dígito
    let digits = value.replace(/\D/g, '');
    if (digits.length === 0) return '';
    if (digits.length <= 2) return digits;
    if (digits.length <= 4) return digits.slice(0, 2) + ':' + digits.slice(2, 4);
    // Limita a 4 dígitos (HHmm)
    return digits.slice(0, 2) + ':' + digits.slice(2, 4);
}

// Handler para input manual en horaRegreso
function onHoraRegresoInput(e) {
    let formatted = formatTimeInput(e.target.value);
    horaRegresoInput.value = formatted;
    syncCalendarFromInput();
}

// Modifica el Dropdown para sincronizar el Calendar
watch(horaRegresoAMPM, () => {
    syncCalendarFromInput();
});
</script>

<template>
    <Head title="Tours" />
    <AuthenticatedLayout>
        <div class="py-6 px-6 mt-10 mx-auto bg-red-50 shadow-md rounded-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Gestión de Tours</h3>
                <Button label="Agregar tour" icon="pi pi-plus" class="p-button-sm p-button-danger" @click="openNew" />
            </div>

            <DataTable :value="tours" v-model:selection="selectedTours" dataKey="id" :filters="filters" :paginator="true" :rows="4 ">
                <template #header>
                    <InputText v-model="filters['global'].value" placeholder="Buscar..." class="w-full" />
                </template>
                <Column field="nombre" header="Nombre" sortable />
                <Column field="descripcion" header="Descripción">
                    <template #body="slotProps">
                        <div class="max-h-14 max-w-52 overflow-y-auto whitespace-pre-line px-2 py-1 break-words">
                            {{ slotProps.data.descripcion }}
                        </div>
                    </template>
                </Column>
                <Column field="puntoSalida" header="Punto de salida"  />
                <Column field="fecha" header="Fecha y hora de salida">
                    <template #body="slotProps">
                        {{ slotProps.data.fecha ? new Date(slotProps.data.fecha).toLocaleString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true }) : '' }}
                    </template>
                </Column>
                <Column field="horaRegreso" header="Hora de regreso" bodyStyle="min-width:120px;">
                    <template #body="slotProps">
                        {{ slotProps.data.horaRegreso || '' }}
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
                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="nombre" class="w-24">Nombre:</label>
                            <InputText v-model.trim="tour.nombre" id="nombre" :class="{ 'p-invalid': submitted && !tour.nombre }" class="flex-1" />
                        </div>
                        <small class="text-red-500 ml-28" v-if="submitted && !tour.nombre">El nombre es obligatorio.</small>
                    </div>
                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="descripcion" class="w-24">Descripción:</label>
                            <Textarea v-model.trim="tour.descripcion" id="descripcion" :class="{ 'p-invalid': submitted && !tour.descripcion }" class="flex-1" />
                        </div>
                        <small class="text-red-500 ml-28" v-if="submitted && !tour.descripcion">La descripción es obligatoria.</small>
                    </div>
                    <div>
                        <label for="puntoSalida" class="block mb-2">Punto de salida</label>
                        <InputText v-model.trim="tour.puntoSalida" id="puntoSalida" required :class="{ 'p-invalid': submitted && !tour.puntoSalida}" class="w-full" />
                        <small class="text-red-500" v-if="submitted && !tour.puntoSalida">Punto de salida es requerido.</small>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label for="fecha" class="block mb-2">Fecha y hora de salida</label>
                            <Calendar v-model="tour.fecha" id="fecha" :showIcon="true" :showTime="true" hourFormat="12" dateFormat="yy-mm-dd" :class="{ 'p-invalid': submitted && !tour.fecha }" class="w-full" :manualInput="false" />
                            <small class="text-red-500" v-if="submitted && !tour.fecha">Fecha y hora requerida.</small>
                        </div>
                        <div style="width: 90px; min-width: 95px;">
                            <label for="horaRegreso" class="block mb-2">Hora regreso</label>
                            <Calendar
                                v-model="horaRegresoCalendar"
                                id="horaRegresoCalendar"
                                :showTime="true"
                                :timeOnly="true"
                                hourFormat="12"
                                :manualInput="false"
                                :class="{ 'p-invalid': submitted && !horaRegresoInput }"
                                style="width: 100%; min-width: 95px;"
                                @change="onHoraRegresoCalendarChange"
                            />
                            <small class="text-red-500" v-if="submitted && (!horaRegresoInput || !horaRegresoAMPM)">Hora de regreso y AM/PM son requeridos.</small>
                        </div>
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