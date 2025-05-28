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
        { id: 2, nombre: 'Producto B', precio: 20.5 }
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

            <div class="py-14 mt-10 px-6  mx-auto bg-red-100 shadow-md rounded-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Productos</h3>
                <Button label="Agregar producto" icon="pi pi-plus" class="p-button-sm p-button-danger" @click="openNew" />
            </div>

            <DataTable :value="productos" v-model:selection="selectedProducts" dataKey="id" :filters="filters" :paginator="true" :rows="5">
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
          <Dialog v-model:visible="dialog" :header="btnTitle + ' Producto'" :modal="true" :style="{ width: '500px' }">
    <div class="space-y-4">
        <div class="w-full flex items-center gap-4">
            <label for="nombre" class="w-24">Nombre:</label>
            <InputText v-model.trim="producto.nombre" id="nombre"
                required :class="{ 'p-invalid': submitted && !producto.nombre }"
                class="flex-1" />
        </div>
        <div class="w-full flex items-center gap-4">
            <label for="descripcion" class="w-24">Descripción:</label>
            <InputText v-model.trim="producto.descripcion" id="descripcion"
                required :class="{ 'p-invalid': submitted && !producto.descripcion }"
                class="flex-1" />
        </div>
        <div class="w-full flex items-center gap-4">
            <label for="precio" class="w-24">Precio:</label>
            <InputNumber v-model="producto.precio" id="precio"
                mode="currency" currency="USD" :locale="'en-US'"
                :class="{ 'p-invalid': submitted && producto.precio == null }"
                class="flex-1" />
        </div>
        <div class="w-full flex items-center gap-4">
    <label for="imagen" class="w-24">Imagen:</label>
    <FileUpload
        mode="basic"
        name="imagen"
        accept="image/*"
        :auto="true"
        chooseLabel="Seleccionar imagen"
        @select="onImageSelect"
        :customUpload="true"
        class="flex-1"
    />
</div>

<div v-if="imagenPreview" class="flex flex-col items-center mt-2 space-y-2">
    <img :src="imagenPreview" alt="Vista previa" class="w-32 h-32 object-cover rounded border" />
    <Button label="Quitar imagen" icon="pi pi-times" class="p-button-outlined p-button-danger p-button-sm" @click="onImageClear" />
</div>

    </div>

    <template #footer>
        <div class="flex justify-center gap-4 w-full">
            <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
            <Button :label="btnTitle" icon="pi pi-check" @click="saveOrUpdate" />
        </div>
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
