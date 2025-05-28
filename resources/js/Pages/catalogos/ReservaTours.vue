<template>
  <Head title="Reservas de Tours" />
  <AuthenticatedLayout>
    <div class="py-6 px-6 mt-10 mx-auto bg-red-100 shadow-md rounded-lg">
      <!-- Datos del cliente y reserva -->
      <div class="bg-white p-4 rounded shadow mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block font-semibold mb-1">Cliente:</label>
          <Dropdown v-model="cliente" :options="clientes" optionLabel="nombre" placeholder="Seleccione un cliente" class="w-full" />
        </div>
        <div>
          <label class="block font-semibold mb-1">Dirección:</label>
          <InputText v-model="direccion" class="w-full" placeholder="Dirección" readonly/>
        </div>
        <div>
          <label class="block font-semibold mb-1">Teléfono:</label>
          <InputText v-model="telefono" class="w-full" placeholder="Teléfono" readonly/>
        </div>
        <div>
          <label class="block font-semibold mb-1">Fecha de reserva:</label>
          <Calendar
            v-model="fechaReserva"
            dateFormat="dd/mm/yy"
            class="w-full"
            :showIcon="false"
            :disabled="true"
          />
        </div>
        <div>
          <label class="block font-semibold mb-1">Factura No.:</label>
          <InputText v-model="facturaNo" class="w-full" readonly />
        </div>
      </div>

      <!-- Sección para agregar tour -->
      <div class="bg-white p-4 rounded shadow mb-4 flex flex-col md:flex-row md:items-end gap-4 border border-blue-300">
        <Button label="Buscar tour" icon="pi pi-search" class="p-button-outlined p-button-primary mb-2 md:mb-0" />
        <div>
          <label class="block font-semibold mb-1">Tour:</label>
          <Dropdown v-model="tourSeleccionado" :options="tours" optionLabel="nombre" placeholder="Seleccione un tour" class="w-full" />
        </div>
        <div>
          <label class="block font-semibold mb-1">Fecha del tour:</label>
          <Calendar v-model="fechaTour" dateFormat="dd/mm/yy" class="w-full" showIcon />
        </div>
        <div>
          <label class="block font-semibold mb-1">Personas:</label>
          <InputNumber v-model="cantidad" :min="1" class="w-full" />
        </div>
        <div>
          <label class="block font-semibold mb-1">Precio:</label>
          <InputNumber v-model="precio" :min="0" mode="currency" currency="USD" locale="en-US" class="w-full" :readonly="true" />
        </div>
        <div>
          <Button label="Agregar" icon="pi pi-plus" class="p-button-success mt-6 w-full" @click="agregarReserva" />
        </div>
      </div>

      <!-- Tabla de reservas -->
      <DataTable :value="reservas" class="mb-4" v-if="reservas.length">
        <Column field="tour" header="Tour"></Column>
        <Column field="fecha" header="Fecha"></Column>
        <Column field="cantidad" header="Personas"></Column>
        <Column field="precio" header="Precio Unitario" :body="precioBody"></Column>
        <Column field="total" header="Total" :body="totalBody"></Column>
        <Column header="Borrar">
          <template #body="slotProps">
            <Button icon="pi pi-trash" class="p-button-danger p-button-sm" @click="borrarReserva(slotProps.rowIndex)" />
          </template>
        </Column>
      </DataTable>
      <div class="text-right font-bold text-lg mt-2" v-if="reservas.length">
        Total a pagar: ${{ totalGeneral.toFixed(2) }}
      </div>

      <!-- Botones de acción -->
      <div class="flex flex-wrap gap-4 mt-6">
        <Button label="Guardar" icon="pi pi-save" class="p-button-success" />
        <Button label="Anular" icon="pi pi-times" class="p-button-warning" />
        <Button label="Validar" icon="pi pi-check" class="p-button-info" />
        <Button label="Imprimir" icon="pi pi-print" class="p-button-secondary" />
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// PrimeVue components
import InputText from 'primevue/inputtext'
import InputNumber from 'primevue/inputnumber'
import Dropdown from 'primevue/dropdown'
import Calendar from 'primevue/calendar'
import Button from 'primevue/button'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'

const clientes = [
  { nombre: 'Juan Diaz', direccion: 'Av. # 1, C. Principal, Casa # 4B, Col. San Juan', telefono: '77558899' },
  { nombre: 'Ana Perez', direccion: 'Col. Escalón, Calle 5', telefono: '22223333' }
]
const cliente = ref(null)
const direccion = ref('')
const telefono = ref('')
const fechaReserva = ref(new Date()) // ← Esto inicializa con la fecha regional
const facturaNo = ref('452125')

watch(cliente, (val) => {
  if (val) {
    direccion.value = val.direccion
    telefono.value = val.telefono
  } else {
    direccion.value = ''
    telefono.value = ''
  }
})

const tours = [
  { nombre: 'Ruta Maya', precio: 45 },
  { nombre: 'Playa El Tunco', precio: 35 },
  { nombre: 'Cascada de Don Juan', precio: 40 }
]
const tourSeleccionado = ref(null)
const fechaTour = ref(null)
const cantidad = ref(1)
const precio = ref(0)
const reservas = ref([])

watch(tourSeleccionado, (val) => {
  precio.value = val ? val.precio : 0
})

const totalGeneral = computed(() =>
  reservas.value.reduce((acc, item) => acc + item.total, 0)
)

function agregarReserva() {
  if (!tourSeleccionado.value || !fechaTour.value || cantidad.value < 1) return

  reservas.value.push({
    tour: tourSeleccionado.value.nombre,
    precio: tourSeleccionado.value.precio,
    cantidad: cantidad.value,
    fecha: fechaTour.value ? fechaTour.value.toLocaleDateString() : '',
    total: tourSeleccionado.value.precio * cantidad.value
  })

  // Limpiar campos de tour
  tourSeleccionado.value = null
  fechaTour.value = null
  cantidad.value = 1
  precio.value = 0
}

function borrarReserva(index) {
  reservas.value.splice(index, 1)
}

function precioBody(row) {
  return `$${row.precio.toFixed(2)}`
}
function totalBody(row) {
  return `$${row.total.toFixed(2)}`
}
</script>

<style scoped>
th, td {
  border: 1px solid #ccc;
}
</style>
