<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';
    import { ref, onMounted } from 'vue';
    import { useToast } from 'primevue/usetoast';
    import { FilterMatchMode } from '@primevue/core/api';
    import FileUpload from 'primevue/fileupload';
    import Toast from 'primevue/toast';

    const toast = useToast();

    const productos = ref([]);  
    const producto = ref({ id: null, nombre: '', precio: null, imagenes: [] });
    const imagenPreviewList = ref([]);
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
        productos.value = [
            { id: 1, nombre: 'Producto A', precio: 10.99, imagenes: [] },
            { id: 2, nombre: 'Producto B', precio: 20.5, imagenes: [] },
            { id: 3, nombre: 'Producto C', precio: 32.99, imagenes: [] },
            { id: 4, nombre: 'Producto D', precio: 15.75, imagenes: [] },
            { id: 5, nombre: 'Producto E', precio: 8.99, imagenes: [] },
            { id: 6, nombre: 'Producto F', precio: 25.0, imagenes: [] },
            { id: 7, nombre: 'Producto G', precio: 18.5, imagenes: [] },
            { id: 8, nombre: 'Producto H', precio: 30.0, imagenes: [] },
            { id: 9, nombre: 'Producto I', precio: 12.99, imagenes: [] },
            { id: 10, nombre: 'Producto J', precio: 22.5, imagenes: [] }
        ];
    };

    const openNew = () => {
        producto.value = { id: null, nombre: '', precio: null, imagenes: [] };
        imagenPreviewList.value = [];
        submitted.value = false;
        btnTitle.value = 'Guardar';
        dialog.value = true;
    };

    const editProduct = (prod) => {
        producto.value = { ...prod };
        imagenPreviewList.value = prod.imagenes || [];
        btnTitle.value = 'Actualizar';
        dialog.value = true;
    };

    const saveOrUpdate = () => {
    submitted.value = true;

    if (
        !producto.value.nombre ||
        !producto.value.descripcion ||
        producto.value.precio == null ||
        producto.value.precio <= 0 ||
        producto.value.precio > 999999.99 ||
        imagenPreviewList.value.length === 0
    ) return;

    // Verificar si ya existe un producto con el mismo nombre (excepto en edición del mismo)
    const nombreExiste = productos.value.some(p =>
        p.nombre.toLowerCase().trim() === producto.value.nombre.toLowerCase().trim() &&
        p.id !== producto.value.id
    );

    if (nombreExiste) {
        toast.add({
            severity: 'error',
            summary: 'Nombre duplicado',
            detail: `Ya tienes un producto con el nombre "${producto.value.nombre}".`,
            life: 4000
        });
        return;
    }

    producto.value.imagenes = [...imagenPreviewList.value];

    if (producto.value.id === null) {
        producto.value.id = Date.now();
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
    producto.value = { id: null, nombre: '', descripcion: '', precio: null, imagenes: [] };
    imagenPreviewList.value = [];
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
</script>

<template>
    <Head title="Productos" />
    <AuthenticatedLayout>
    <Toast />
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
                <Column header="Imágenes">
                    <template #body="slotProps">
                        <div class="flex gap-1">
                            <img v-for="(img, index) in slotProps.data.imagenes" :key="index" :src="img" alt="img" class="w-10 h-10 object-cover rounded" />
                        </div>
                    </template>
                </Column>
                <Column header="Acciones" :exportable="false">
                    <template #body="slotProps">
                        <Button icon="pi pi-pencil" class="p-button-rounded p-button-warn p-button-md mr-2" @click="editProduct(slotProps.data)" />
                        <Button icon="pi pi-trash" class="p-button-rounded p-button-danger p-button-md" @click="confirmDeleteProduct(slotProps.data)" />
                    </template>
                </Column>
            </DataTable>

            <Dialog v-model:visible="dialog" :header="btnTitle + ' Producto'" :modal="true" :style="{ width: '500px' }">
                <div class="space-y-4">
                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="nombre" class="w-24">Nombre:</label>
                            <InputText v-model.trim="producto.nombre" id="nombre" :class="{ 'p-invalid': submitted && !producto.nombre }" class="flex-1" />
                        </div>
                        <small class="text-red-500 ml-28" v-if="submitted && !producto.nombre">El nombre es obligatorio.</small>
                    </div>

                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="descripcion" class="w-24">Descripción:</label>
                            <InputText v-model.trim="producto.descripcion" id="descripcion" :class="{ 'p-invalid': submitted && !producto.descripcion }" class="flex-1" />
                        </div>
                        <small class="text-red-500 ml-28" v-if="submitted && !producto.descripcion">La descripción es obligatoria.</small>
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
                            :class="{ 'p-invalid': submitted && (!producto.precio || producto.precio <= 0 || producto.precio > 999999.99) }"
                            class="flex-1"
                            />

                        </div>
                        <small
                        class="text-red-500 ml-28"
                        v-if="submitted && (producto.precio == null || producto.precio <= 0 || producto.precio > 999999.99)"
                        >
                        El precio es obligatorio, debe ser mayor a 0 y menor o igual a 9999.99.
                        </small>

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
                <span>¿Eliminar el producto <b>{{ producto.nombre }}</b>?</span>
                <template #footer>
                    <Button label="No" icon="pi pi-times" text @click="deleteDialog = false" class="p-button-rounded p-button-warn p-button-md mr-2" />
                    <Button label="Sí" icon="pi pi-check" severity="danger" @click="deleteProduct" />
                </template>
            </Dialog>
        </div>
    </AuthenticatedLayout>
</template>
