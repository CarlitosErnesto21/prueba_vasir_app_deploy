<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, onMounted, watch } from "vue";
import { useToast } from "primevue/usetoast";
import { FilterMatchMode } from "@primevue/core/api";
import FileUpload from "primevue/fileupload";
import Select from "primevue/select";
import Carousel from "primevue/carousel";
import Calendar from "primevue/calendar";
import axios from "axios";

const toast = useToast();

const tours = ref([]);
const tour = ref({
    id: null,
    nombre: "",
    descripcion: "",
    punto_salida: "",
    fecha: null,
    precio: null,
    hora_regreso: null,
    categoria_tour_id: null,
    tipo_transporte_id: null,
    transporte_id: null,
    imagenes: [],
});

const imagenPreviewList = ref([]);
const imagenFiles = ref([]);
const removedImages = ref([]);
const selectedTours = ref(null); // CORRECCIÓN: declarado correctamente
const dialog = ref(false);
const deleteDialog = ref(false);
const submitted = ref(false);
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const btnTitle = ref("Guardar");
const horaRegresoInput = ref("");
const horaRegresoAMPM = ref("am");
const horaRegresoCalendar = ref(null);
const url = "/api/tours";
const IMAGE_PATH = "/images/tours/";
const categoriasTours = ref([]);
const tipoTransportes = ref([]);
const transportes = ref([]);
const showImageDialog = ref(false);
const selectedImages = ref([]);
const carouselIndex = ref(0);

function resetForm() {
    tour.value = {
        id: null,
        nombre: "",
        descripcion: "",
        punto_salida: "",
        fecha: null,
        precio: null,
        hora_regreso: null,
        categoria_tour_id: null,
        tipo_transporte_id: null,
        transporte_id: null,
        imagenes: [],
    };
    imagenPreviewList.value = [];
    imagenFiles.value = [];
    removedImages.value = [];
    horaRegresoInput.value = "";
    horaRegresoAMPM.value = "am";
    horaRegresoCalendar.value = null;
    submitted.value = false;
    transportes.value = [];
}

// Obtener tours, categorías y tipos de transporte
onMounted(() => {
    fetchTours();
    fetchCategoriasTours();
    fetchTipoTransportes();
});

const fetchTours = async () => {
    try {
        const response = await axios.get(url);
        tours.value = response.data.map((t) => ({
            ...t,
            imagenes: (t.imagenes || []).map((img) =>
                typeof img === "string" ? img : img.nombre
            ),
            categoria_nombre: t.categoria?.nombre || "",
            tipo_transporte_nombre: t.transporte?.tipo_transporte?.nombre || "",
            transporte_capacidad: t.transporte?.capacidad || "",
            fecha: t.fecha,
        }));
    } catch (err) {
        console.error("fetchTours error:", err);
        toast.add({ severity: "error", summary: "Error", detail: "No se pudieron cargar los tours.", life: 4000 });
    }
};

const fetchCategoriasTours = async () => {
    try {
        const response = await axios.get("/api/categorias-tours");
        categoriasTours.value = response.data || [];
    } catch {
        categoriasTours.value = [];
        toast.add({ severity: "error", summary: "Error", detail: "No se pudieron cargar las categorías.", life: 4000 });
    }
};

const fetchTipoTransportes = async () => {
    try {
        const response = await axios.get("/api/tipos-transportes");
        tipoTransportes.value = response.data || [];
    } catch {
        tipoTransportes.value = [];
        toast.add({ severity: "error", summary: "Error", detail: "No se pudieron cargar los tipos de transporte.", life: 4000 });
    }
};

const fetchTransportes = async (tipoId) => {
    try {
        let endpoint = "/api/transportes";
        if (tipoId) endpoint += `?tipo_transporte_id=${tipoId}`;
        const response = await axios.get(endpoint);
        transportes.value = response.data || [];
    } catch {
        transportes.value = [];
        toast.add({ severity: "error", summary: "Error", detail: "No se pudieron cargar los transportes.", life: 4000 });
    }
};

watch(() => tour.value.tipo_transporte_id, (newVal) => {
    if (newVal) fetchTransportes(newVal);
    else transportes.value = [];
});

const openNew = () => {
    resetForm();
    btnTitle.value = "Guardar";
    dialog.value = true;
};

const editTour = (t) => {
    resetForm();
    tour.value = {
        ...t,
        categoria_tour_id: t.categoria?.id || t.categoria_tour_id || null,
        tipo_transporte_id: t.transporte?.tipoTransporte?.id || t.tipo_transporte_id || null,
        transporte_id: t.transporte?.id || t.transporte_id || null,
    };
    if (tour.value.tipo_transporte_id) fetchTransportes(tour.value.tipo_transporte_id);

    if (t.hora_regreso) {
        const dateObj = new Date(`1970-01-01T${t.hora_regreso}`);
        let hours = dateObj.getHours();
        let minutes = dateObj.getMinutes();
        horaRegresoAMPM.value = hours >= 12 ? "pm" : "am";
        hours = hours % 12 || 12;
        horaRegresoInput.value = `${hours.toString().padStart(2,"0")}:${minutes.toString().padStart(2,"0")}`;
        syncCalendarFromInput();
    }

    imagenPreviewList.value = (t.imagenes || []).map(img => typeof img === "string" ? img : img.nombre);
    btnTitle.value = "Actualizar";
    dialog.value = true;
};

function formatHoraInput(hora) {
    const parts = hora.split(":");
    if (parts.length !== 2) return null;
    const hh = parts[0].padStart(2,"0");
    const mm = parts[1].padStart(2,"0");
    return `${hh}:${mm}`;
}

const saveOrUpdate = async () => {
    submitted.value = true;

    if (!horaRegresoInput.value || !["am","pm"].includes(horaRegresoAMPM.value.toLowerCase())) {
        toast.add({ severity: "error", summary: "Error", detail: "Hora de regreso inválida.", life: 4000 });
        return;
    }

    tour.value.hora_regreso = `${formatHoraInput(horaRegresoInput.value)} ${horaRegresoAMPM.value.toLowerCase()}`;

    if (!tour.value.nombre || !tour.value.descripcion || !tour.value.punto_salida || !tour.value.fecha || !tour.value.precio || !tour.value.categoria_tour_id || !tour.value.tipo_transporte_id || imagenPreviewList.value.length === 0) {
        toast.add({ severity: "error", summary: "Error", detail: "Completa todos los campos obligatorios.", life: 4000 });
        return;
    }

    try {
        const formData = new FormData();
        formData.append("nombre", tour.value.nombre);
        formData.append("descripcion", tour.value.descripcion);
        formData.append("punto_salida", tour.value.punto_salida);
        formData.append("fecha", tour.value.fecha instanceof Date ? tour.value.fecha.toISOString().slice(0,10) : tour.value.fecha);
        formData.append("precio", tour.value.precio);
        formData.append("hora", formatHoraInput(horaRegresoInput.value));
        formData.append("ampm", horaRegresoAMPM.value.toLowerCase());
        formData.append("categoria_tour_id", tour.value.categoria_tour_id);
        formData.append("tipo_transporte_id", tour.value.tipo_transporte_id);

        imagenFiles.value.forEach(f => formData.append("imagenes[]", f));
        removedImages.value.forEach(img => formData.append("removed_images[]", img.split("/").pop()));

        let response;
        if (!tour.value.id) {
            response = await axios.post(url, formData, { headers: {"Content-Type":"multipart/form-data"} });
            toast.add({ severity: "success", summary: "Éxito", detail: "Tour agregado.", life: 4000 });
        } else {
            formData.append("_method","PUT");
            response = await axios.post(`${url}/${tour.value.id}`, formData, { headers: {"Content-Type":"multipart/form-data"} });
            toast.add({ severity: "info", summary: "Éxito", detail: "Tour actualizado.", life: 4000 });
        }

        await fetchTours();
        dialog.value = false;
        resetForm();
    } catch (err) {
        console.error("saveOrUpdate error:", err);
        toast.add({ severity: "error", summary: "Error", detail: err.response?.data?.message || "No se pudo guardar el tour.", life: 4000 });
    }
};

// Funciones para eliminar tours, manejar imágenes y sincronizar hora
const confirmDeleteTour = (t) => { tour.value = { ...t }; deleteDialog.value = true; };
const deleteTour = async () => {
    try {
        await axios.delete(`${url}/${tour.value.id}`);
        await fetchTours();
        deleteDialog.value = false;
        toast.add({ severity: "warn", summary: "Eliminado", detail: `El tour "${tour.value.nombre}" fue eliminado.`, life: 4000 });
    } catch (err) {
        toast.add({ severity: "error", summary: "Error", detail: "No se pudo eliminar el tour.", life: 4000 });
    }
};
const hideDialog = () => { dialog.value = false; imagenPreviewList.value = []; imagenFiles.value = []; removedImages.value = []; submitted.value = false; };
const onImageSelect = (event) => {
    for (const file of event.files) {
        if (file instanceof File) {
            imagenFiles.value.push(file);
            const reader = new FileReader();
            reader.onload = (e) => imagenPreviewList.value.push(e.target.result);
            reader.readAsDataURL(file);
        }
    }
};
const removeImage = (index) => {
    const removed = imagenPreviewList.value[index];
    if (typeof removed === "string" && removed.startsWith("data:image")) {
        imagenPreviewList.value.splice(index,1);
        imagenFiles.value.splice(index,1);
    } else {
        removedImages.value.push(removed);
        imagenPreviewList.value.splice(index,1);
    }
};
const viewImages = (imagenesTour) => {
    selectedImages.value = (imagenesTour||[]).map(img=>IMAGE_PATH+(typeof img==="string"?img:img.nombre));
    showImageDialog.value = true;
};
function syncCalendarFromInput() {
    if (!horaRegresoInput.value || !horaRegresoAMPM.value) return;
    const [hh, mm] = horaRegresoInput.value.split(":");
    let hours = parseInt(hh)||0;
    if (horaRegresoAMPM.value.toLowerCase()==="pm" && hours<12) hours+=12;
    if (horaRegresoAMPM.value.toLowerCase()==="am" && hours===12) hours=0;
    const date = new Date();
    date.setHours(hours);
    date.setMinutes(parseInt(mm)||0);
    horaRegresoCalendar.value = date;
}
function onHoraRegresoCalendarChange() {
    if (!horaRegresoCalendar.value) return;
    const date = new Date(horaRegresoCalendar.value);
    let hours = date.getHours();
    let minutes = date.getMinutes();
    let ampm = hours>=12?"pm":"am";
    hours = hours%12||12;
    horaRegresoInput.value = `${hours.toString().padStart(2,"0")}:${minutes.toString().padStart(2,"0")}`;
    horaRegresoAMPM.value = ampm;
}
watch(horaRegresoAMPM,syncCalendarFromInput);
</script>


<template>
    <Head title="Tours" />
    <AuthenticatedLayout>
        <div class="py-6 px-7 mt-10 mx-auto bg-red-100 shadow-md rounded-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Catálogo de Tours</h3>
                <Button
                    label="Agregar tour"
                    icon="pi pi-plus"
                    class="p-button-sm p-button-danger"
                    @click="openNew"
                />
            </div>

            <DataTable
                :value="tours"
                v-model:selection="selectedTours"
                dataKey="id"
                :filters="filters"
                :paginator="true"
                :rows="10"
                class="overflow-x-auto max-w-full"
                style="display: block; max-width: 84vw"
            >
                <template #header>
                    <InputText
                        v-model="filters['global'].value"
                        placeholder="Buscar..."
                        class="w-full"
                    />
                </template>
                <Column field="nombre" header="Nombre" sortable />
                <Column field="descripcion" header="Descripción" />
                <Column field="punto_salida" header="Punto de salida" />
                <Column field="fecha" header="Fecha y hora de salida">
                    <template #body="slotProps">
                        {{
                            slotProps.data.fecha
                                ? new Date(slotProps.data.fecha).toLocaleString(
                                      "es-ES",
                                      {
                                          day: "2-digit",
                                          month: "2-digit",
                                          year: "numeric",
                                          hour: "2-digit",
                                          minute: "2-digit",
                                          hour12: true,
                                      }
                                  )
                                : ""
                        }}
                    </template>
                </Column>
                <Column field="hora_regreso" header="Hora de regreso">
                    <template #body="slotProps">
                        {{ slotProps.data.hora_regreso || "" }}
                    </template>
                </Column>
                <Column field="precio" header="Precio">
                    <template #body="slotProps">
                        {{
                            isNaN(Number(slotProps.data.precio))
                                ? ""
                                : `$${Number(slotProps.data.precio).toFixed(2)}`
                        }}
                    </template>
                </Column>
                <Column field="categoria_nombre" header="Categoría" />
                <Column
                    field="tipo_transporte_nombre"
                    header="Tipo de Transporte"
                />
                <Column
                    field="transporte_capacidad"
                    header="Capacidad del Transporte"
                />
                <Column header="Acciones" :exportable="false">
                    <template #body="slotProps">
                        <div class="flex gap-2">
                            <Button
                                icon="pi pi-eye"
                                class="p-button-rounded p-button-info p-button-md"
                                @click="viewImages(slotProps.data.imagenes)"
                            />
                            <Button
                                icon="pi pi-pencil"
                                class="p-button-rounded p-button-warn p-button-md"
                                @click="editTour(slotProps.data)"
                            />
                            <Button
                                icon="pi pi-trash"
                                class="p-button-rounded p-button-danger p-button-md"
                                @click="confirmDeleteTour(slotProps.data)"
                            />
                        </div>
                    </template>
                </Column>
            </DataTable>

            <Dialog
                v-model:visible="dialog"
                :header="btnTitle + ' Tour'"
                :modal="true"
                :style="{ width: '500px' }"
            >
                <div class="space-y-4">
                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="nombre" class="w-24">Nombre:</label>
                            <InputText
                                v-model.trim="tour.nombre"
                                id="nombre"
                                :class="{
                                    'p-invalid': submitted && !tour.nombre,
                                }"
                                class="flex-1"
                            />
                        </div>
                        <small
                            class="text-red-500 ml-28"
                            v-if="submitted && !tour.nombre"
                            >El nombre es obligatorio.</small
                        >
                    </div>
                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="descripcion" class="w-24"
                                >Descripción:</label
                            >
                            <textarea
                                v-model.trim="tour.descripcion"
                                id="descripcion"
                                :class="{
                                    'p-invalid': submitted && !tour.descripcion,
                                }"
                                class="flex-1"
                            />
                        </div>
                        <small
                            class="text-red-500 ml-28"
                            v-if="submitted && !tour.descripcion"
                            >La descripción es obligatoria.</small
                        >
                    </div>
                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="punto_salida" class="w-24"
                                >Punto de salida:</label
                            >
                            <InputText
                                v-model.trim="tour.punto_salida"
                                id="punto_salida"
                                :class="{
                                    'p-invalid':
                                        submitted && !tour.punto_salida,
                                }"
                                class="flex-1"
                            />
                        </div>
                        <small
                            class="text-red-500 ml-28"
                            v-if="submitted && !tour.punto_salida"
                            >El punto de salida es obligatorio.</small
                        >
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label for="fecha" class="block mb-2"
                                >Fecha y hora de salida</label
                            >
                            <Calendar
                                v-model="tour.fecha"
                                id="fecha"
                                :showIcon="true"
                                :showTime="true"
                                hourFormat="12"
                                dateFormat="yy-mm-dd"
                                :class="{
                                    'p-invalid': submitted && !tour.fecha,
                                }"
                                class="w-full"
                                :manualInput="false"
                            />
                            <small
                                class="text-red-500"
                                v-if="submitted && !tour.fecha"
                                >Fecha y hora requerida.</small
                            >
                        </div>
                        <div style="width: 150px; min-width: 150px">
                            <label for="hora_regreso" class="block mb-2"
                                >Hora regreso</label
                            >
                            <div class="flex gap-2 items-center">
                                <Calendar
                                    v-model="horaRegresoCalendar"
                                    id="horaRegresoCalendar"
                                    :showTime="true"
                                    :timeOnly="true"
                                    hourFormat="12"
                                    :manualInput="false"
                                    :class="{
                                        'p-invalid':
                                            submitted && !horaRegresoInput,
                                    }"
                                    style="width: 85px; min-width: 85px"
                                    @change="onHoraRegresoCalendarChange"
                                />
                            </div>
                            <small
                                class="text-red-500"
                                v-if="
                                    submitted &&
                                    (!horaRegresoInput || !horaRegresoAMPM)
                                "
                                >Hora de regreso y AM/PM son requeridos.</small
                            >
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
                                :class="{
                                    'p-invalid':
                                        submitted &&
                                        (!tour.precio ||
                                            tour.precio <= 0 ||
                                            tour.precio > 9999.99),
                                }"
                                class="flex-1"
                            />
                        </div>
                        <small
                            class="text-red-500 ml-28"
                            v-if="
                                submitted &&
                                (tour.precio == null ||
                                    tour.precio <= 0 ||
                                    tour.precio > 9999.99)
                            "
                        >
                            El precio es obligatorio, debe ser mayor a 0 y menor
                            o igual a 9999.99.
                        </small>
                    </div>
                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="categoria_tour_id" class="w-24"
                                >Categoría:</label
                            >
                            <Select
                                v-model="tour.categoria_tour_id"
                                :options="categoriasTours"
                                optionLabel="nombre"
                                optionValue="id"
                                id="categoria_tour_id"
                                class="flex-1"
                                placeholder="Selecciona una categoría"
                                :class="{
                                    'p-invalid':
                                        submitted && !tour.categoria_tour_id,
                                }"
                            />
                        </div>
                        <small
                            class="text-red-500 ml-28"
                            v-if="submitted && !tour.categoria_tour_id"
                            >La categoría es obligatoria.</small
                        >
                    </div>
                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="tipo_transporte_id" class="w-24"
                                >Tipo de transporte:</label
                            >
                            <Select
                                v-model="tour.tipo_transporte_id"
                                :options="tipoTransportes"
                                optionLabel="nombre"
                                optionValue="id"
                                id="tipo_transporte_id"
                                class="flex-1"
                                placeholder="Selecciona un tipo de transporte"
                                :class="{
                                    'p-invalid':
                                        submitted && !tour.tipo_transporte_id,
                                }"
                            />
                        </div>
                        <small
                            class="text-red-500 ml-28"
                            v-if="submitted && !tour.tipo_transporte_id"
                            >El tipo de transporte es obligatorio.</small
                        >
                    </div>
                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="imagen" class="w-24">Imágenes:</label>
                            <FileUpload
                                mode="basic"
                                name="imagenes[]"
                                accept="image/*"
                                :auto="true"
                                chooseLabel="Seleccionar imágenes"
                                @select="onImageSelect"
                                :customUpload="true"
                                :multiple="true"
                                class="flex-1 p-button-rounded p-button-warn p-button-md mr-2"
                            />
                        </div>
                        <small
                            class="text-red-500 ml-28"
                            v-if="submitted && imagenPreviewList.length === 0"
                            >Al menos una imagen es obligatoria.</small
                        >
                    </div>
                    <div class="flex gap-4 flex-wrap mt-4 ml-28">
                        <div
                            v-for="(img, index) in imagenPreviewList"
                            :key="index"
                            class="relative w-32 h-32"
                        >
                            <img
                                :src="
                                    img.startsWith('data:image')
                                        ? img
                                        : IMAGE_PATH + img
                                "
                                alt="Vista previa"
                                class="w-full h-full object-cover rounded border"
                            />
                            <button
                                @click="removeImage(index)"
                                class="absolute top-0 right-0 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 shadow"
                                style="transform: translate(50%, -50%)"
                            >
                                <i class="pi pi-times text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <template #footer>
                    <div class="flex justify-center gap-4 w-full">
                        <Button
                            label="Cancelar"
                            icon="pi pi-times"
                            class="p-button-rounded p-button-danger p-button-md mr-2"
                            text
                            @click="hideDialog"
                        />
                        <Button
                            :label="btnTitle"
                            icon="pi pi-check"
                            class="p-button-rounded p-button-warn p-button-md mr-2"
                            @click="saveOrUpdate"
                        />
                    </div>
                </template>
            </Dialog>

            <Dialog
                v-model:visible="deleteDialog"
                header="Confirmar"
                :modal="true"
                :style="{ width: '350px' }"
            >
                <span
                    >¿Eliminar el tour <b>{{ tour.nombre }}</b
                    >?</span
                >
                <template #footer>
                    <Button
                        label="No"
                        icon="pi pi-times"
                        text
                        @click="deleteDialog = false"
                        class="p-button-rounded p-button-warn p-button-md mr-2"
                    />
                    <Button
                        label="Sí"
                        icon="pi pi-check"
                        severity="danger"
                        @click="deleteTour"
                    />
                </template>
            </Dialog>

            <Dialog
                v-model:visible="showImageDialog"
                header="Imágenes del tour"
                :modal="true"
                :style="{ width: '700px' }"
            >
                <div
                    v-if="selectedImages.length"
                    class="flex flex-col items-center justify-center"
                >
                    <!-- Carousel for images -->
                    <Carousel
                        :value="selectedImages"
                        :numVisible="1"
                        :numScroll="1"
                        :circular="true"
                        v-model:page="carouselIndex"
                        class="w-full"
                        :showIndicators="selectedImages.length > 1"
                        :showNavigators="selectedImages.length > 1"
                        style="max-width: 610px"
                    >
                        <template #item="slotProps">
                            <div
                                class="flex justify-center items-center w-full h-96"
                            >
                                <img
                                    :src="slotProps.data"
                                    alt="Imagen tour"
                                    class="w-auto h-full max-h-96 object-contain rounded shadow"
                                />
                            </div>
                        </template>
                    </Carousel>
                </div>
                <div v-else class="text-center text-gray-500 py-8">
                    No hay imágenes para este tour.
                </div>
                <template #footer>
                    <Button
                        label="Cerrar"
                        icon="pi pi-times"
                        class="p-button-rounded-md p-button-danger"
                        @click="showImageDialog = false"
                    />
                </template>
            </Dialog>
        </div>
    </AuthenticatedLayout>
</template>