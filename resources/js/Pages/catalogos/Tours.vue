<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useToast } from 'primevue/usetoast';

const toast = useToast();

// Datos de productos
const products = ref([
  {
    id: '1',
    code: 'P001',
    name: 'Tour Machu Picchu',
    image: 'product-placeholder.svg',
    description: 'Visita a Machu Picchu',
    price: 1000,
    category: 'Aventura',
    rating: 5,
    inventoryStatus: 'INSTOCK',
    quantity: 10
  }
]);

const dt = ref();
const productDialog = ref(false);
const deleteProductDialog = ref(false);
const deleteProductsDialog = ref(false);
const product = ref({});
const selectedProducts = ref();
const filters = ref({
  global: { value: null, matchMode: 'contains' }
});
const submitted = ref(false);
const statuses = ref([
  { label: 'INSTOCK', value: 'INSTOCK' },
  { label: 'LOWSTOCK', value: 'LOWSTOCK' },
  { label: 'OUTOFSTOCK', value: 'OUTOFSTOCK' }
]);

const formatCurrency = (value) => {
  return value
    ? value.toLocaleString('en-US', {
        style: 'currency',
        currency: 'USD'
      })
    : '';
};

const openNew = () => {
  product.value = {};
  submitted.value = false;
  productDialog.value = true;
};

const hideDialog = () => {
  productDialog.value = false;
  submitted.value = false;
};

const saveProduct = () => {
  submitted.value = true;
  if (product.value.name?.trim()) {
    if (product.value.id) {
      products.value[findIndexById(product.value.id)] = { ...product.value };
      toast.add({ severity: 'success', summary: 'Successful', detail: 'Product Updated', life: 3000 });
    } else {
      product.value.id = createId();
      product.value.code = createId();
      product.value.image = 'product-placeholder.svg';
      products.value.push({ ...product.value });
      toast.add({ severity: 'success', summary: 'Successful', detail: 'Product Created', life: 3000 });
    }
    productDialog.value = false;
    product.value = {};
  }
};

const editProduct = (prod) => {
  product.value = { ...prod };
  productDialog.value = true;
};

const confirmDeleteProduct = (prod) => {
  product.value = prod;
  deleteProductDialog.value = true;
};

const deleteProduct = () => {
  products.value = products.value.filter((val) => val.id !== product.value.id);
  deleteProductDialog.value = false;
  product.value = {};
  toast.add({ severity: 'success', summary: 'Successful', detail: 'Product Deleted', life: 3000 });
};

const findIndexById = (id) => {
  return products.value.findIndex((prod) => prod.id === id);
};

const createId = () => {
  let id = '';
  const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  for (let i = 0; i < 5; i++) {
    id += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  return id;
};

const exportCSV = () => {
  dt.value.exportCSV();
};

const confirmDeleteSelected = () => {
  deleteProductsDialog.value = true;
};

const deleteSelectedProducts = () => {
  products.value = products.value.filter((val) => !selectedProducts.value.includes(val));
  deleteProductsDialog.value = false;
  selectedProducts.value = null;
  toast.add({ severity: 'success', summary: 'Successful', detail: 'Products Deleted', life: 3000 });
};

const getStatusLabel = (status) => {
  switch (status) {
    case 'INSTOCK':
      return 'success';
    case 'LOWSTOCK':
      return 'warn';
    case 'OUTOFSTOCK':
      return 'danger';
    default:
      return null;
  }
};
</script>

<template>
  <AuthenticatedLayout>
    <div class="card">
      <!-- Toolbar and DataTable omitidos por brevedad -->

      <!-- Diálogo para productos -->
      <Dialog v-model:visible="productDialog" :style="{ width: '450px' }" header="Product Details" modal>
        <div class="flex flex-col gap-6">
          <img
            v-if="product.image"
            :src="`https://primefaces.org/cdn/primevue/images/product/${product.image}`"
            :alt="product.image"
            class="block m-auto pb-4"
          />
          <div>
            <label for="name" class="block font-bold mb-3">Name</label>
            <InputText
              id="name"
              v-model.trim="product.name"
              required
              autofocus
              :invalid="submitted && !product.name"
              class="w-full"
            />
            <small v-if="submitted && !product.name" class="text-red-500">Name is required.</small>
          </div>
          <div>
            <label for="description" class="block font-bold mb-3">Description</label>
            <Textarea id="description" v-model="product.description" rows="3" class="w-full" />
          </div>
          <div>
            <label for="inventoryStatus" class="block font-bold mb-3">Inventory Status</label>
            <Dropdown
              id="inventoryStatus"
              v-model="product.inventoryStatus"
              :options="statuses"
              optionLabel="label"
              optionValue="value"
              placeholder="Select a Status"
              class="w-full"
            />
          </div>
          <div>
            <label class="block font-bold mb-3">Category</label>
            <div class="flex flex-wrap gap-3">
              <div class="flex items-center gap-2">
                <RadioButton id="accessories" v-model="product.category" name="category" value="Accessories" />
                <label for="accessories">Accessories</label>
              </div>
              <div class="flex items-center gap-2">
                <RadioButton id="clothing" v-model="product.category" name="category" value="Clothing" />
                <label for="clothing">Clothing</label>
              </div>
              <div class="flex items-center gap-2">
                <RadioButton id="electronics" v-model="product.category" name="category" value="Electronics" />
                <label for="electronics">Electronics</label>
              </div>
            </div>
          </div>
        </div>

        <template #footer>
          <Button label="Cancel" icon="pi pi-times" text @click="hideDialog" />
          <Button label="Save" icon="pi pi-check" text @click="saveProduct" />
        </template>
      </Dialog>
    </div>
  </AuthenticatedLayout>
</template>
