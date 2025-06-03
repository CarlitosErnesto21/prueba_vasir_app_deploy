<script setup>
import axios from "axios";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import { useToast } from "primevue/usetoast";
import { FilterMatchMode } from "@primevue/core/api";
import Carousel from 'primevue/carousel';

const toast = useToast();

// Refs
const productos = ref([]);
const producto = ref({
    id: null,
    nombre: "",
    descripcion: "",
    precio: null,
    inventario_id: null,
    categoria_id: null,
});
const imagenPreviewList = ref([]);
const imagenFiles = ref([]);
const selectedProducts = ref();
const dialog = ref(false);
const deleteDialog = ref(false);
const submitted = ref(false);
const btnTitle = ref("Guardar");
const inventarios = ref([]);
const categorias = ref([]);
const url = "/api/productos";
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const showImageDialog = ref(false);
const selectedImages = ref([]);
const IMAGE_PATH = "/images/productos/";
const removedImages = ref([]);

// Lifecycle
onMounted(() => {
    fetchProductos();
    fetchInventarios();
    fetchCategorias();
});

// Métodos para obtener datos
const fetchProductos = async () => {
    try {
        const response = await axios.get(url);
        console.log("Productos recibidos:", response.data);
        productos.value = response.data;
    } catch (err) {
        console.error("Error al obtener los productos", err);
    }
};

const fetchInventarios = async () => {
    try {
        const response = await axios.get("/api/inventarios");
        inventarios.value = response.data;
    } catch (err) {
        console.error("Error al obtener los inventarios", err);
    }
};

const fetchCategorias = async () => {
    try {
        const response = await axios.get("/api/categorias-productos");
        categorias.value = response.data;
    } catch (err) {
        console.error("Error al obtener las categorías", err);
    }
};

// Nuevo producto
const openNew = () => {
    producto.value = {
        id: null,
        nombre: "",
        descripcion: "",
        precio: null,
        inventario_id: null,
        categoria_id: null,
    };
    imagenFiles.value = [];
    imagenPreviewList.value = [];
    submitted.value = false;
    btnTitle.value = "Guardar";
    dialog.value = true;
};

// Editar producto
const editProduct = (prod) => {
    producto.value = { ...prod };

    // Construye las rutas de las imágenes existentes
    imagenPreviewList.value = Array.isArray(prod.imagenes)
        ? prod.imagenes.map((img) => `/images/productos/${img.nombre}`)
        : [];

    imagenFiles.value = []; // Limpia las imágenes nuevas
    removedImages.value = []; // Limpia las imágenes eliminadas
    btnTitle.value = "Actualizar";
    dialog.value = true;
};

// Guardar o actualizar
const saveOrUpdate = async () => {
    submitted.value = true;

    try {
        const formData = new FormData();
        formData.append("nombre", String(producto.value.nombre));
        formData.append("descripcion", String(producto.value.descripcion));
        formData.append("precio", String(producto.value.precio));
        formData.append("inventario_id", String(producto.value.inventario_id));
        formData.append("categoria_id", String(producto.value.categoria_id));

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
        if (!producto.value.id) {
            // Crear producto
            response = await axios.post(url, formData);
            console.log("Respuesta del servidor (crear):", response);
            if (response && response.data) {
                productos.value.push(response.data);
            }
            toast.add({
                severity: "success",
                summary: "Producto agregado",
                life: 3000,
            });
        } else {
            // Actualizar producto
            formData.append("_method", "PUT");
            response = await axios.post(
                `${url}/${producto.value.id}`,
                formData
            );
            console.log("Respuesta del servidor (actualizar):", response);
            const index = productos.value.findIndex(
                (p) => p.id === producto.value.id
            );
            if (index !== -1) productos.value[index] = response.data;
            toast.add({
                severity: "info",
                summary: "Producto actualizado",
                life: 3000,
            });
        }

        // Limpiar formulario
        dialog.value = false;
        producto.value = {
            id: null,
            nombre: "",
            descripcion: "",
            precio: null,
            inventario_id: null,
            categoria_id: null,
        };
        imagenFiles.value = [];
        imagenPreviewList.value = [];
        removedImages.value = []; // Limpia las imágenes eliminadas
    } catch (err) {
        console.error("Error al guardar el producto", err);
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
                detail: "No se pudo guardar el producto.",
                life: 3000,
            });
        }
    }
};

// Confirmar eliminación
const confirmDeleteProduct = (prod) => {
    producto.value = { ...prod };
    deleteDialog.value = true;
};

// Eliminar producto
const deleteProduct = async () => {
    try {
        await axios.delete(`${url}/${producto.value.id}`);
        productos.value = productos.value.filter(
            (p) => p.id !== producto.value.id
        );
        deleteDialog.value = false;
        toast.add({
            severity: "success",
            summary: "Producto eliminado",
            life: 3000,
        });
    } catch (err) {
        console.error("Error al eliminar el producto", err);
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "No se pudo eliminar el producto.",
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

const viewImages = (imagenesDeProducto) => {
    selectedImages.value = (imagenesDeProducto || []).map(
        (img) => `${IMAGE_PATH}${img.nombre}`
    );
    showImageDialog.value = true;
};
</script>

<template>
    <Head title="Productos" />
    <AuthenticatedLayout>
        <Toast />
        <div class="py-6 px-6 mt-10 mx-auto bg-red-50 shadow-md rounded-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Catálogo productos</h3>
                <Button
                    label="Agregar producto"
                    icon="pi pi-plus"
                    class="p-button-sm p-button-danger"
                    @click="openNew"
                />
            </div>
            
            <DataTable :value="productos" dataKey="id">
                <Column field="nombre" header="Nombre" />
                <Column field="descripcion" header="Descripción" />
                <Column field="precio" header="Precio" />
                <Column field="inventario.estado" header="Inventario" />
                <Column field="categoria.nombre" header="Categoría" />
                <Column header="Acciones">
                    <template #body="slotProps">
                        <Button
                            icon="pi pi-eye"
                            class="p-button-rounded p-button-info p-button-md mr-2"
                            @click="viewImages(slotProps.data.imagenes)"
                        />
                        <Button
                            icon="pi pi-pencil"
                            class="p-button-rounded p-button-warn p-button-md mr-2"
                            @click="editProduct(slotProps.data)"
                        />
                        <Button
                            icon="pi pi-trash"
                            class="p-button-rounded p-button-danger p-button-md"
                            @click="confirmDeleteProduct(slotProps.data)"
                        />
                    </template>
                </Column>
            </DataTable>

            <Dialog
                v-model:visible="dialog"
                :header="btnTitle + ' Producto'"
                :modal="true"
                :style="{ width: '500px' }"
            >
                <div class="space-y-4">
                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="nombre" class="w-24">Nombre:</label>
                            <InputText
                                v-model.trim="producto.nombre"
                                id="nombre"
                                :class="{
                                    'p-invalid': submitted && !producto.nombre,
                                }"
                                class="flex-1"
                            />
                        </div>
                        <small
                            class="text-red-500 ml-28"
                            v-if="submitted && !producto.nombre"
                            >El nombre es obligatorio.</small
                        >
                    </div>

                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="descripcion" class="w-24"
                                >Descripción:</label
                            >
                            <InputText
                                v-model.trim="producto.descripcion"
                                id="descripcion"
                                :class="{
                                    'p-invalid':
                                        submitted && !producto.descripcion,
                                }"
                                class="flex-1"
                            />
                        </div>
                        <small
                            class="text-red-500 ml-28"
                            v-if="submitted && !producto.descripcion"
                            >La descripción es obligatoria.</small
                        >
                    </div>

                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="precio" class="w-24">Precio:</label>
                            <InputNumber
                                v-model="producto.precio"
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
                                        (!producto.precio ||
                                            producto.precio <= 0 ||
                                            producto.precio > 999999.99),
                                }"
                                class="flex-1"
                            />
                        </div>
                        <small
                            class="text-red-500 ml-28"
                            v-if="
                                submitted &&
                                (producto.precio == null ||
                                    producto.precio <= 0 ||
                                    producto.precio > 999999.99)
                            "
                        >
                            El precio es obligatorio, debe ser mayor a 0 y menor
                            o igual a 9999.99.
                        </small>
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
                        <small class="text-red-500 ml-28" v-if="false">
                            Al menos una imagen es obligatoria.
                        </small>
                    </div>

                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-6">
                            <label for="inventario">Inventario</label>
                            <Select
                                placeholder="Seleccionar..."
                                v-model="producto.inventario_id"
                                :options="inventarios"
                                option-label="estado"
                                option-value="id"
                                class="w-full"
                            ></Select>
                            <small
                                v-if="submitted && !producto.inventario_id"
                                class="text-red-500"
                                >Seleccione un inventario</small
                            >
                        </div>
                        <div class="col-span-6">
                            <label for="categoria">Categorías</label>
                            <Select
                                placeholder="Seleccionar..."
                                v-model="producto.categoria_id"
                                :options="categorias"
                                option-label="nombre"
                                option-value="id"
                                class="w-full"
                            ></Select>
                            <small
                                v-if="submitted && !producto.categoria_id"
                                class="text-red-500"
                                >Seleccione una categoría</small
                            >
                        </div>
                    </div>

                    <div class="flex gap-4 flex-wrap mt-4 ml-28">
                        <div
                            v-for="(img, index) in imagenPreviewList"
                            :key="index"
                            class="relative w-32 h-32"
                        >
                            <img
                                :src="img"
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
                    >¿Eliminar el producto <b>{{ producto.nombre }}</b
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
                        @click="deleteProduct"
                    />
                </template>
            </Dialog>

            <Dialog
                v-model:visible="showImageDialog"
                header="Imágenes del producto"
                :modal="true"
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
                                alt="Producto"
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
