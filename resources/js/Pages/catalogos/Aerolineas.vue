<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { useToast } from 'primevue/usetoast';
import { FilterMatchMode } from '@primevue/core/api';
import Calendar from 'primevue/calendar';

const toast = useToast();
const aerolineas = ref([]);
const aerolinea = ref({ 
    id: null,
    nombre: '',
    fecha: null,
    imagenes: [] 
});
const imagenPreviewList = ref([]); // Para previews
const imagenFiles = ref([]);    // Para archivos reales
// Para modal de visualización de imágenes
const showImageModal = ref(false);
const currentImageIndex = ref(0);
const imageModalList = ref([]); // <-- NUEVO: lista de imágenes para el visor
const selectedAerolineas = ref();
const dialog = ref(false);
const deleteDialog = ref(false);
const submitted = ref(false);
const url = '/api/aerolineas'; // URL de la API para aerolíneas
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});
const showImageDialog = ref(false);
const selectedImages = ref([]);
const IMAGE_PATH = "/images/aerolinea/";
const removedImages = ref([]);
const btnTitle = ref('Guardar');
const rowsPerPage = ref(5); // Para paginación dinámica

onMounted(() => {
    fetchAerolineas();
});


const fetchAerolineas = async () => {
    try {
        const response = await axios.get("/api/aerolineas");
        aerolineas.value = response.data;
    } catch (err) {
        console.error("Error al obtener las aerolíneas", err);
    }
};

const openNew = () => {
    aerolinea.value = {
        id: null,
        nombre: "",
        fecha: null,
    };
    imagenFiles.value = [];
    imagenPreviewList.value = [];
    submitted.value = false;
    btnTitle.value = "Guardar";
    dialog.value = true;
};

const editAerolinea = (a) => {
    aerolinea.value = { ...a };
    imagenFiles.value = [];
    btnTitle.value = 'Actualizar';
    dialog.value = true;
    // Construye las rutas de las imágenes existentes
    imagenPreviewList.value = Array.isArray(a.imagenes)
        ? a.imagenes.map((img) => {
            // Si ya es una URL (por ejemplo, base64 o http), úsala tal cual
            if (typeof img === 'string' && (img.startsWith('data:') || img.startsWith('http'))) {
                return img;
            }
            // Si es un objeto con nombre, arma la ruta correcta
            if (img && (img.nombre || img.file_name)) {
                return IMAGE_PATH + (img.nombre || img.file_name);
            }
            return img;
        })
        : [];
    removedImages.value = [];
};

// Guardar o actualizar
const saveOrUpdate = async () => {
    submitted.value = true;

    try {
        const formData = new FormData();
        formData.append("nombre", String(aerolinea.value.nombre));

        // Formatear fecha correctamente para el backend
        let fechaFormateada = aerolinea.value.fecha;
        if (fechaFormateada && typeof fechaFormateada === 'object' && typeof fechaFormateada.toISOString === 'function') {
            fechaFormateada = fechaFormateada.toISOString().split('T')[0];
        } else if (typeof fechaFormateada === 'string') {
            // Si ya es string tipo 'yyyy-mm-dd', úsala tal cual
            fechaFormateada = fechaFormateada;
        } else {
            fechaFormateada = '';
        }
        formData.append("fecha", fechaFormateada);


        // Agregar imágenes nuevas
        if (imagenFiles.value.length > 0) {
            imagenFiles.value.forEach((img) => {
                formData.append("imagenes[]", img);
            });
        }

        // Agregar imágenes eliminadas
        if (removedImages.value.length > 0) {
            removedImages.value.forEach((img) => {
                const imageName = img.split("/").pop(); // Obtén el nombre del archivo
                formData.append("removed_images[]", imageName);
            });
        }

        console.log("Datos enviados:", [...formData.entries()]);

        let response;
        if (!aerolinea.value.id) {
            // Crear aerolínea
            response = await axios.post(url, formData);
            console.log("Respuesta del servidor (crear):", response);
            if (response && response.data) {
                // Refresca la lista desde la base de datos para mantener sincronizado
                await fetchAerolineas();
            }
            toast.add({
                severity: "success",
                summary: "Aerolínea agregada",
                life: 3000,
            });
        } else {
            // Actualizar aerolínea
            formData.append("_method", "PUT");
            response = await axios.post(
                `${url}/${aerolinea.value.id}`,
                formData
            );
            console.log("Respuesta del servidor (actualizar):", response);
            await fetchAerolineas();
            toast.add({
                severity: "info",
                summary: "Aerolínea actualizada",
                life: 3000,
            });
        }

        // Limpiar formulario
        dialog.value = false;
        aerolinea.value = {
            id: null,
            nombre: "",
            fecha: null,
            imagenes: []
        };
        imagenFiles.value = [];
        imagenPreviewList.value = [];
        removedImages.value = []; // Limpia las imágenes eliminadas
    } catch (err) {
        console.error("Error al guardar la aerolínea", err);
        if (err.response?.status === 422) {
            const errores = err.response.data.errors;
            toast.add({
                severity: "error",
                summary: "Error de validación",
                detail: Object.values(errores).flat().join(", "),
                life: 4000,
            });
        } else {
            toast.add({
                severity: "error",
                summary: "Error",
                detail: "No se pudo guardar la aerolínea.",
                life: 3000,
            });
        }
    }
};

const confirmDeleteAerolinea = (a) => {
    aerolinea.value = { ...a };
    deleteDialog.value = true;
};

// Confirmar eliminación
const confirmDeleteProduct = (prod) => {
    producto.value = { ...prod };
    deleteDialog.value = true;
};

// Eliminar producto
const deleteAerolinea = async () => {
    try {
        await axios.delete(`${url}/${aerolinea.value.id}`);
        await fetchAerolineas();
        deleteDialog.value = false;
        toast.add({
            severity: "success",
            summary: "Aerolínea eliminada",
            life: 3000,
        });
    } catch (err) {
        console.error("Error al eliminar la aerolínea", err);
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "No se pudo eliminar la aerolínea.",
            life: 3000,
        });
    }
};

// Cerrar modal
const hideDialog = () => {
    dialog.value = false;
};

// Seleccionar imágenes
const onImageSelect = (event) => {
    for (const file of event.files) {
        if (file instanceof File) {
            // Verifica que sea un archivo válido
            imagenFiles.value.push(file); // Guarda el archivo real para el backend

            const reader = new FileReader();
            reader.onload = (e) => {
                imagenPreviewList.value.push(e.target.result); // Solo para mostrar la vista previa
            };
            reader.readAsDataURL(file);
        } else {
            console.error("El archivo seleccionado no es válido:", file);
        }
    }
};

// Quitar imagen
const removeImage = (index) => {
    const removedImage = imagenPreviewList.value[index];
    removedImages.value.push(removedImage); // Agrega la imagen eliminada a la lista
    imagenPreviewList.value.splice(index, 1); // Elimina la imagen de la vista previa
};

const viewImages = (imagenes) => {
    selectedImages.value = (imagenes || []).map(
        (img) => {
           if (typeof img === 'string' && (img.startsWith('data:') || img.startsWith('http') || img.startsWith(IMAGE_PATH))) {
                return img;
            }
            if (img && (img.nombre || img.file_name)) {
                return IMAGE_PATH + (img.nombre || img.file_name);
            }
            return img;
        }
    );
    showImageDialog.value = true;
};
</script>

<template>
    <Head title="Aerolíneas" />
    <AuthenticatedLayout>
        <Toast />
        <div class="py-6 px-6 mt-10 mx-auto bg-red-50 shadow-md rounded-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Gestión de Aerolíneas</h3>
                <Button label="Agregar aerolínea" icon="pi pi-plus" class="p-button-sm p-button-danger" @click="openNew" />
            </div>

            <DataTable :value="aerolineas" v-model:selection="selectedAerolineas" dataKey="id" :filters="filters" :paginator="true" :rows="5" :rowsPerPageOptions="[5,10,15]" class="w-full table-fixed">
                <template #header>
                    <div class="flex justify-between items-center w-full">
                        <InputText v-model="filters['global'].value" placeholder="Buscar..." class="w-full mr-4" />
                    </div>
                </template>
                <Column field="nombre" header="Nombre" sortable :style="{ width: '30%' }" />
                <Column field="fecha" header="Fecha" :style="{ width: '30%' }">
                    <template #body="slotProps">
                        {{ slotProps.data.fecha ? new Date(slotProps.data.fecha).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' }) : '' }}
                    </template>
                </Column>
                <Column header="Acciones" :exportable="false" :style="{ width: '20%', textAlign: 'left' }">
                    <template #body="slotProps">
                        <div class="flex justify-letf gap-2 w-full">
                            <Button
                                icon="pi pi-eye"
                                class="p-button-rounded p-button-info p-button-md mr-2"
                                @click="viewImages(slotProps.data.imagenes)"
                            />
                            <Button
                                icon="pi pi-pencil"
                                class="p-button-rounded p-button-warn p-button-md mr-2"
                                @click="editAerolinea(slotProps.data)"
                            />
                            <Button
                                icon="pi pi-trash"
                                class="p-button-rounded p-button-danger p-button-md"
                                @click="confirmDeleteAerolinea(slotProps.data)"
                            />
                        </div>
                    </template>
                </Column>
            </DataTable>

            <!-- Modal Aerolínea -->
            <Dialog v-model:visible="dialog" :header="btnTitle + ' Aerolínea'" :modal="true" :style="{ width: '400px' }">
                <div class="p-fluid space-y-6">
                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="nombre" class="w-24">Nombre:</label>
                            <InputText v-model.trim="aerolinea.nombre" id="nombre" :class="{ 'p-invalid': submitted && !aerolinea.nombre }" class="flex-1" />
                        </div>
                        <small class="text-red-500 ml-28" v-if="submitted && !aerolinea.nombre">El nombre es obligatorio.</small>
                    </div>
                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="fecha" class="w-24">Fecha fundación:</label>
                            <Calendar v-model="aerolinea.fecha" id="fecha" :showIcon="true" dateFormat="yy-mm-dd" :class="{ 'p-invalid': submitted && !aerolinea.fecha }" class="flex-1" :manualInput="false" />
                        </div>
                        <small class="text-red-500 ml-28" v-if="submitted && !aerolinea.fecha">La fecha es obligatoria.</small>
                    </div>
                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="imagenes" class="w-24">Imágenes:</label>
                            <FileUpload mode="basic" name="imagenes[]" accept="image/*" :auto="true" chooseLabel="Seleccionar imágenes"  @select="onImageSelect" :customUpload="true" :multiple="true" class="flex-1  p-button-rounded p-button-warn p-button-md mr-2" />
                        </div>
                        <small class="text-red-500 ml-28" v-if="submitted && imagenPreviewList.length === 0">Al menos una imagen es obligatoria.</small>
                    </div>
                    <div class="flex gap-4 flex-wrap mt-4 ml-28">
                        <div v-for="(img, index) in imagenPreviewList" :key="index" class="relative w-24 h-24 group">
                            <img :src="img" alt="Vista previa" class="w-full h-full object-cover rounded border" />
                            <!-- Botón para eliminar imagen -->
                            <button @click="removeImage(index)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 shadow" style="z-index:2">
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

            <!-- Confirmación de eliminación -->
            <Dialog v-model:visible="deleteDialog" header="Confirmar" :modal="true" :style="{ width: '350px' }">
                <span>¿Eliminar la aerolínea <b>{{ aerolinea.nombre }}</b>?</span>
                 <template #footer>
                    <Button label="No" icon="pi pi-times" text @click="deleteDialog = false" class="p-button-rounded p-button-warn p-button-md mr-2" />
                    <Button label="Sí" icon="pi pi-check" severity="danger" @click="deleteAerolinea" />
                </template>
            </Dialog>

            <!-- Modal para ver imagen grande y navegar -->
            <Dialog
                v-model:visible="showImageDialog"
                header="Imágenes del producto"
                :modal="true"
                :closable="false"
                :style="{ width: '700px' }"
            >
                <Carousel
                    :value="selectedImages"
                    :numVisible="1"
                    :numScroll="1"
                    circular
                    :responsiveOptions="[
                        {
                            breakpoint: '1024px',
                            numVisible: 1,
                            numScroll: 1,
                        },
                        {
                            breakpoint: '600px',
                            numVisible: 1,
                            numScroll: 1,
                        },
                    ]"
                    :showNavigators="true"
                    :showIndicators="true"
                >
                    <template #item="slotProps">
                        <div class="relative flex justify-center items-center h-96 w-full">
                            <img
                                :src="slotProps.data"
                                alt="aerrolinea"
                                class="max-h-full max-w-full object-contain rounded shadow"
                            />
                        </div>
                    </template>
                </Carousel>
                <template #footer>
                    <div class="flex justify-center">
                        <Button
                            label="Cerrar"
                            icon="pi pi-times"
                            class="p-button-rounded-md p-button-danger"
                            @click="showImageDialog = false"
                        />
                    </div>
                </template>
            </Dialog>
        </div>
    </AuthenticatedLayout>
</template>