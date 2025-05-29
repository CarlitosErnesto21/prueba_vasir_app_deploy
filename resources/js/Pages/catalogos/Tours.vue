<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { useToast } from 'primevue/usetoast';
import { FilterMatchMode } from '@primevue/core/api';
import Calendar from 'primevue/calendar';

const toast = useToast();

const productos = ref([]);
const producto = ref({ id: null, nombre: '', precio: null, imagen: null, descripcion: '', puntoSalida: '', horaRegreso: null, fecha: null });
const imagenPreview = ref(null);
const selectedProducts = ref();
const dialog = ref(false);
const deleteDialog = ref(false);
const submitted = ref(false);
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});
const btnTitle = ref('Guardar');

onMounted(() => {
    fetchProductos();
});

const fetchProductos = async () => {
    // Simular datos de ejemplo
    productos.value = [
        { id: 1, nombre: 'Producto A', precio: 10.99 },
        { id: 2, nombre: 'Producto B', precio: 20.5 },
        { id: 3, nombre: 'Producto c', descripcion: 'tour a machu pichu', puntoSalida: 'chalatenango',  precio: 10.99 },
        { id: 4, nombre: 'Producto D', descripcion: 'tour a machu pichu', puntoSalida: 'chalatenango',  precio: 10.99 },

    ];
};

const openNew = () => {
    producto.value = { id: null, nombre: '', precio: null };
    submitted.value = false;
    btnTitle.value  = 'Guardar';
    dialog.value = true;
};

const editProduct = (prod) => {
    producto.value = { ...prod };
    imagenPreview.value = prod.imagen || null; // Asignar imagen si existe
    btnTitle.value = 'Actualizar';
    dialog.value = true;
};

const saveOrUpdate = () => {
    submitted.value = true;

    if (!producto.value.nombre || producto.value.precio == null || !imagenPreview.value ) return;

    if (producto.value.id === null) {
        producto.value.id = Date.now(); // Generar ID único (solo ejemplo)
        productos.value.push({ ...producto.value, imagen: imagenPreview.value });
        toast.add({ severity: 'success', summary: 'Producto agregado', life: 3000 });
    } else {
        const index = productos.value.findIndex(p => p.id === producto.value.id);
        if (index !== -1) {
            productos.value[index] = { ...producto.value, imagen: imagenPreview.value };
            toast.add({ severity: 'info', summary: 'Producto actualizado', life: 3000 });
        }
    }

    dialog.value = false;
    producto.value = { id: null, nombre: '', precio: null, imagen: null, descripcion: '', puntoSalida: '', horaRegreso: null, fecha: null };
    imagenPreview.value = null; // Limpiar vista previa de imagen
};

const confirmDeleteProduct = (prod) => {
    producto.value = { ...prod };
    deleteDialog.value = true;
};

const deleteProduct = () => {
    productos.value = productos.value.filter(p => p.id !== producto.value.id);
    deleteDialog.value = false;
    toast.add({ severity: 'warn', summary: 'Producto eliminado', life: 3000 });
};

const hideDialog = () => {
    dialog.value = false;
};

const onImageSelect = (event) => {
    const file = event.files[0];
    if (file) {
        producto.value.imagen = file; // Guardar el archivo para enviar al servidor
        const reader = new FileReader();
        reader.onload = (e) => {
            imagenPreview.value = e.target.result; // Mostrar vista previa de la imagen
        };
        reader.readAsDataURL(file);
    }
};

const onImageClear = () => {
    producto.value.imagen = null; // Limpiar el archivo
    imagenPreview.value = null; // Limpiar la vista previa
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

            <DataTable :value="productos" v-model:selection="selectedProducts" dataKey="id" :filters="filters" :paginator="true" :rows="4 ">
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
                          @click="editProduct(slotProps.data)" 
                        />
                        <Button
                         icon="pi pi-trash" 
                         class="p-button-rounded p-button-danger p-button-md mr-2" 
                         @click="confirmDeleteProduct(slotProps.data)" 
                        />
                    </template>
                </Column>
            </DataTable>

            <!-- Modal Producto -->
            <Dialog v-model:visible="dialog" :header="btnTitle + ' Tour'" :modal="true" :style="{ width: '400px' }">
                <div class="p-fluid space-y-6">
                    <div>
                        <label for="nombre" class="block mb-2">Nombre</label>
                        <InputText v-model.trim="producto.nombre" id="nombre" required :class="{ 'p-invalid': submitted && !producto.nombre }" class="w-full" />
                        <small class="text-red-500" v-if="submitted && !producto.nombre">Nombre requerido.</small>
                    </div>
                    <div>
                        <label for="descripcion" class="block mb-2">Descripción</label>
                        <InputText v-model.trim="producto.descripcion" id="descripcion" required :class="{ 'p-invalid': submitted && !producto.descripcion}" class="w-full" />
                        <small class="text-red-500" v-if="submitted && !producto.descripcion">Descripcion es requerida.</small>
                    </div>
                    <div>
                        <label for="puntoSalida" class="block mb-2">Punto de salida</label>
                        <InputText v-model.trim="producto.puntoSalida" id="puntoSalida" required :class="{ 'p-invalid': submitted && !producto.puntoSalida}" class="w-full" />
                        <small class="text-red-500" v-if="submitted && !producto.puntoSalida">Punto de salida es requerido.</small>
                    </div>
                    <div>
                        <label for="horaRegreso" class="block mb-2">Hora de regreso</label>
                        <Calendar v-model="producto.horaRegreso" id="horaRegreso" :showIcon="true" timeOnly hourFormat="12" :class="{ 'p-invalid': submitted && !producto.horaRegreso }" class="w-full">
                            <template #inputicon>
                                <i class="pi pi-clock" />
                            </template>
                        </Calendar>
                        <small class="text-red-500" v-if="submitted && !producto.horaRegreso">Hora de regreso es requerido.</small>
                    </div>
                    <div>
                        <label for="fecha" class="block mb-2">Fecha y hora de salida</label>
                        <Calendar v-model="producto.fecha" id="fecha" :showIcon="true" :showTime="true" hourFormat="12" dateFormat="yy-mm-dd" :class="{ 'p-invalid': submitted && !producto.fecha }" class="w-full" />
                        <small class="text-red-500" v-if="submitted && !producto.fecha">Fecha y hora requerida.</small>
                    </div>
                    <div>
                        <label for="precio" class="block mb-2">Precio</label>
                        <InputNumber v-model="producto.precio" id="precio" mode="currency" currency="USD" :locale="'en-US'" :class="{ 'p-invalid': submitted && producto.precio == null }" class="w-full" />
                        <small class="text-red-500" v-if="submitted && producto.precio == null">Precio requerido.</small>
                    </div>
                    <div>
                        <label for="imagen" class="block mb-2">Imagen</label>
                        <FileUpload
                            mode="basic"
                            name="imagen"
                            accept="image/*"
                            :auto="true"
                            chooseLabel="Seleccionar imagen"
                            @select="onImageSelect"
                            @clear="onImageClear"
                            :customUpload="true"
                            class="w-full"
                        />
                        <div v-if="imagenPreview" class="mt-2">
                            <img :src="imagenPreview" alt="Vista previa" class="w-32 h-32 object-cover rounded border" />
                        </div>
                        <small class="text-red-500" v-if="submitted && !producto.imagen">Imagen requerida.</small>
                    </div>
                </div>
                <!-- ...dentro de tu Dialog, después del campo de precio... -->
                <template #footer>
                    <Button  label="Cancelar " icon="pi pi-times " class="p-button-sm p-button-danger" text @click="hideDialog" />
                    <Button :label="btnTitle" icon="pi pi-check" class="p-button-sm p-button-warning" @click="saveOrUpdate" />
                </template>
            </Dialog>

            <!-- Confirmación de eliminación -->
            <Dialog v-model:visible="deleteDialog" header="Confirmar" :modal="true" :style="{ width: '350px' }">
                <span>¿Eliminar el tour <b>{{ producto.nombre }}</b>?</span>
                <template #footer>
                    <Button label="No" icon="pi pi-times" text @click="deleteDialog = false" />
                    <Button label="Sí" icon="pi pi-check" severity="danger" @click="deleteProduct" />
                </template>
            </Dialog>
        </div>
    </AuthenticatedLayout>
</template>