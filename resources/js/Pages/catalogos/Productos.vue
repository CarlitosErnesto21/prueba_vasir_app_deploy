<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { useToast } from 'primevue/usetoast';
import { FilterMatchMode } from '@primevue/core/api';

const toast = useToast();

const productos = ref([]);
const producto = ref({ id: null, nombre: '', precio: null });
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
    btnTitle.value = 'Actualizar';
    dialog.value = true;
};

const saveOrUpdate = () => {
    submitted.value = true;

    if (!producto.value.nombre || producto.value.precio == null) return;

    if (producto.value.id === null) {
        producto.value.id = Date.now(); // Generar ID único (solo ejemplo)
        productos.value.push({ ...producto.value });
        toast.add({ severity: 'success', summary: 'Producto agregado', life: 3000 });
    } else {
        const index = productos.value.findIndex(p => p.id === producto.value.id);
        if (index !== -1) {
            productos.value[index] = { ...producto.value };
            toast.add({ severity: 'info', summary: 'Producto actualizado', life: 3000 });
        }
    }

    dialog.value = false;
    producto.value = { id: null, nombre: '', precio: null };
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
</script>

<template>
    <Head title="Productos" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Productos</h2>
        </template>

        <div class="py-12 px-6 max-w-4xl mx-auto bg-gray-100 shadow-md rounded-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Gestión de Productos</h3>
                <Button label="Nuevo Producto" icon="pi pi-plus" class="p-button-sm" @click="openNew" />
            </div>

            <DataTable :value="productos" v-model:selection="selectedProducts" dataKey="id" :filters="filters" :paginator="true" :rows="5">
                <template #header>
                    <InputText v-model="filters['global'].value" placeholder="Buscar..." class="w-full" />
                </template>
                <Column field="nombre" header="Nombre" sortable />
                <Column field="precio" header="Precio" sortable>
                    <template #body="slotProps">
                        ${{ slotProps.data.precio.toFixed(2) }}
                    </template>
                </Column>
                <Column header="Acciones" :exportable="false">
                    <template #body="slotProps">
                        <Button icon="pi pi-pencil" class="p-button-text p-button-sm" @click="editProduct(slotProps.data)" />
                        <Button icon="pi pi-trash" class="p-button-text p-button-sm p-button-danger" @click="confirmDeleteProduct(slotProps.data)" />
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
                        <label for="precio">Precio</label>
                        <InputNumber v-model="producto.precio" id="precio" mode="currency" currency="USD" :locale="'en-US'" :class="{ 'p-invalid': submitted && producto.precio == null }" />
                        <small class="text-red-500" v-if="submitted && producto.precio == null">Precio requerido.</small>
                    </div>
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
