<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { useToast } from 'primevue/usetoast';
import { FilterMatchMode } from '@primevue/core/api';
import FileUpload from 'primevue/fileupload';

const toast = useToast();

const productos = ref([]);
const producto = ref({ id: null, nombre: '', precio: null, imagen: null });
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
        { id: 3, nombre: 'Producto C', precio: 32.99 },
        { id: 4, nombre: 'Producto D', precio: 60.25 },
        { id: 5, nombre: 'Producto E', precio: 15.75 },
        { id: 6, nombre: 'Producto F', precio: 45.0 },
        { id: 7, nombre: 'Producto G', precio: 22.5 },
        { id: 8, nombre: 'Producto H', precio: 18.99 },
        { id: 9, nombre: 'Producto I', precio: 30.0 },
        { id: 10, nombre: 'Producto J', precio: 25.5 }
    ];
};

const openNew = () => {
    producto.value = { id: null, nombre: '', precio: null };
    submitted.value = false;
    btnTitle.value = 'Guardar';
    dialog.value = true;
};

const editProduct = (prod) => {
    producto.value = { ...prod };
    imagenPreview.value = prod.imagen || null;
    btnTitle.value = 'Actualizar';
    dialog.value = true;
};

const saveOrUpdate = () => {
    submitted.value = true;

    if (!producto.value.nombre || !producto.value.descripcion || producto.value.precio == null || !imagenPreview.value) return;

    if (producto.value.id === null) {
        producto.value.id = Date.now();
        productos.value.push({ ...producto.value, imagen: imagenPreview.value }); // Guarda la base64
        toast.add({ severity: 'success', summary: 'Producto agregado', life: 3000 });
    } else {
        const index = productos.value.findIndex(p => p.id === producto.value.id);
        if (index !== -1) {
            productos.value[index] = { ...producto.value, imagen: imagenPreview.value };
            toast.add({ severity: 'info', summary: 'Producto actualizado', life: 3000 });
        }
    }

    dialog.value = false;
    producto.value = { id: null, nombre: '', descripcion: '', precio: null, imagen: null };
    imagenPreview.value = null;
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
        producto.value.imagen = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagenPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const onImageClear = () => {
    producto.value.imagen = null;
    imagenPreview.value = null;
};
</script>

 <template>
    <Head title="Productos" />
    <AuthenticatedLayout>

        <div class="py-6 px-6 mt-10 mx-auto bg-red-100 shadow-md rounded-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Catálogo productos</h3>
                <Button label="Agregar producto" icon="pi pi-plus" class="p-button-sm p-button-danger" @click="openNew" />
            </div>

            <DataTable :value="productos" v-model:selection="selectedProducts" dataKey="id" :filters="filters" :paginator="true" :rows="4">
                <template #header>
                    <InputText v-model="filters['global'].value" placeholder="Buscar..." class="w-full" />
                </template>
                <Column field="nombre" header="Nombre"  />
                <Column field="descripcion" header="Descripción"  />
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
                            class="p-button-rounded p-button-danger p-button-md"
                            @click="confirmDeleteProduct(slotProps.data)"
                        />

                    </template>
                </Column>
            </DataTable>

            <!-- Modal Producto -->
            <Dialog v-model:visible="dialog" :header="btnTitle + ' Producto'" :modal="true" :style="{ width: '400px' }">
                <div class="p-fluid space-y-4">
                    <div>
                        <label for="nombre">Nombre</label>
                        <InputText v-model.trim="producto.nombre" id="nombre" required :class="{ 'p-invalid': submitted && !producto.nombre }" />
                        <small class="text-red-500" v-if="submitted && !producto.nombre">Nombre requerido.</small>
                    </div>
                    <div>
                        <label for="descripcion">Descripción</label>
                        <InputText v-model.trim="producto.descripcion" id="descripcion" required :class="{ 'p-invalid': submitted && !producto.descripcion }" />
                        <small class="text-red-500" v-if="submitted && !producto.descripcion">Descripción requerida.</small>
                    </div>
                    <div>
                        <label for="precio">Precio</label>
                        <InputNumber v-model="producto.precio" id="precio" mode="currency" currency="USD" :locale="'en-US'" :class="{ 'p-invalid': submitted && producto.precio == null }" />
                        <small class="text-red-500" v-if="submitted && producto.precio == null">Precio requerido.</small>
                    </div>
                </div>
                <!-- ...dentro de tu Dialog, después del campo de precio... -->
                <div>
                    <label for="imagen">Imagen</label>
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
                <template #footer>
                    <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
                    <Button :label="btnTitle" icon="pi pi-check" @click="saveOrUpdate" />
                </template>
            </Dialog>

            <!-- Confirmación de eliminación -->
            <Dialog v-model:visible="deleteDialog" header="Confirmar" :modal="true" :style="{ width: '350px' }">
                <span>¿Eliminar el producto <b>{{ producto.nombre }}</b>?</span>
                <template #footer>
                    <Button label="No" icon="pi pi-times" text @click="deleteDialog = false" />
                    <Button label="Sí" icon="pi pi-check" severity="danger" @click="deleteProduct" />
                </template>
            </Dialog>
        </div>
    </AuthenticatedLayout>
</template>
