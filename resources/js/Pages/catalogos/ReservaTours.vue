<template>
  <Head title="Reservas de Tours" />
  <AuthenticatedLayout>
    <Toast />
    <div class="py-6 px-2 sm:px-4 md:px-6 mt-10 mx-auto bg-red-100 shadow-md rounded-lg max-w-full">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold">Catálogo de reservas de tours</h3>
      </div>
      <!-- Datos del cliente y reserva -->
      <div class="bg-white p-4 rounded shadow mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label for="cliente-select" class="block font-semibold mb-1">Cliente:</label>
          <div class="flex gap-2 mb-2">
            <Select
              id="cliente-select"
              name="cliente"
              v-model="cliente"
              :options="clientes"
              optionLabel="nombre"
              placeholder="Seleccione un cliente"
              class="w-full"
            />
            <Button
              label="Buscar"
              icon="pi pi-search"
              class="p-button-warn"
              @click="mostrarModalBuscar = true"
            />
          </div>
          <span v-if="errores.busqueda" class="text-red-500 text-sm">{{ errores.busqueda }}</span>
        </div>
        <div>
          <label for="direccion-input" class="block font-semibold mb-1">Dirección:</label>
          <InputText id="direccion-input" name="direccion" v-model="direccion" class="w-full" :disabled="true" />
        </div>
        <div>
          <label for="telefono-input" class="block font-semibold mb-1">Teléfono:</label>
          <InputText id="telefono-input" name="telefono" v-model="telefono" class="w-full" :disabled="true" />
        </div>
        <div>
          <label for="fecha-reserva" class="block font-semibold mb-1">Fecha de reserva:</label>
          <DatePicker id="fecha-reserva" name="fechaReserva" v-model="fechaReserva" dateFormat="dd/mm/yy" class="w-full" :showIcon="false" :disabled="true" />
        </div>
        <div>
          <label for="estado-select" class="block font-semibold mb-1">Estado:</label>
          <Select id="estado-select" name="estado" v-model="estado" :options="estados" class="w-full" />
        </div>
        <div>
          <label for="numero-reserva" class="block font-semibold mb-1">N° Reserva:</label>
          <InputText id="numero-reserva" name="numeroReserva" v-model="numeroReserva" class="w-full" :disabled="true" />
        </div>
      </div>

      <!-- Sección para agregar tours a la reserva -->
      <div class="bg-white p-4 rounded shadow mb-4 flex flex-col md:flex-row md:items-end gap-4 border border-blue-300">
        <div>
          <label for="tour-select" class="block font-semibold mb-1">Tour:</label>
          <div class="flex gap-2">
            <Select
              id="tour-select"
              name="tour"
              v-model="tourSeleccionado"
              :options="tours"
              optionLabel="nombre"
              placeholder="Seleccione un tour"
              class="w-48"
            />
            <Button label="Buscar" icon="pi pi-search" class="p-button-warn" @click="mostrarModalBuscarTour = true" />
          </div>
        </div>
        <div>
          <label for="fecha-tour" class="block font-semibold mb-1">Fecha del tour:</label>
          <DatePicker id="fecha-tour" name="fechaTour" v-model="fechaTour" dateFormat="dd/mm/yy" class="w-full" showIcon />
        </div>
        <div>
          <label for="cupos-input" class="block font-semibold mb-1">Cupos:</label>
          <InputNumber id="cupos-input" name="cupos" v-model="cupos" :min="1" class="w-full" />
        </div>
        <div>
          <label for="precio-unitario" class="block font-semibold mb-1">Precio unitario:</label>
          <InputNumber id="precio-unitario" name="precioUnitario" v-model="precioUnitario" :min="0" mode="currency" currency="USD" locale="en-US" class="w-full" :readonly="true" />
        </div>
        <div>
          <Button label="Agregar" icon="pi pi-plus" class="p-button-contrast mt-6 w-full" @click="agregarDetalle" />
        </div>
      </div>

      <!-- Tabla de detalles de reserva -->
      <div class="overflow-x-auto">
        <DataTable :value="detalles" class="mb-4 min-w-[600px] w-full" v-if="detalles.length">
          <Column field="id" header="N° Reserva"></Column>
          <Column field="tour" header="Tour"></Column>
          <Column field="fecha" header="Fecha"></Column>
          <Column field="cupos" header="Cupos"></Column>
          <Column field="precio_unitario" header="Precio Unitario" :body="precioBody"></Column>
          <Column field="subtotal" header="Subtotal" :body="subtotalBody"></Column>
          <Column header="Editar">
            <template #body="slotProps">
              <Button
                icon="pi pi-pencil"
                class="p-button-warn p-button-sm"
                @click="abrirEditarDetalle(detalles.indexOf(slotProps.data))"
              />
            </template>
          </Column>
          <Column header="Borrar">
            <template #body="slotProps">
              <Button icon="pi pi-trash" class="p-button-danger p-button-sm" @click="borrarDetalle(slotProps.rowIndex)" />
            </template>
          </Column>
        </DataTable>
      </div>
      <div class="text-right font-bold text-lg mt-2" v-if="detalles.length">
        Total a pagar: ${{ totalGeneral.toFixed(2) }}
      </div>

      <!-- Botones de acción -->
      <div class="flex flex-col sm:flex-row flex-wrap gap-4 mt-6">
        <Button label="Guardar" icon="pi pi-save" class="p-button-warn" @click="guardarReserva" />
        <Button label="Anular" icon="pi pi-times" class="p-button-danger" />
        <Button label="Validar" icon="pi pi-check" class="p-button-contrast" />
        <Button label="Imprimir" icon="pi pi-print" class="p-button-secondary" @click="imprimirReserva" />
      </div>

      <!-- Mensajes de error -->
      <div v-if="Object.keys(errores).length" class="mt-4">
        <div v-for="(error, key) in errores" :key="key" class="p-error">
          {{ error }}
        </div>
      </div>
      <span v-if="errores.cliente" class="text-red-500 text-sm">{{ errores.cliente }}</span>
      <span v-if="errores.detalles" class="text-red-500 text-sm">{{ errores.detalles }}</span>

      <!-- Modal de búsqueda de cliente -->
      <Dialog v-model:visible="mostrarModalBuscar" modal header="Buscar Cliente" :style="{ width: '90vw', maxWidth: '500px' }">
        <div class="mb-4 flex gap-2">
          <label for="modal-nombre-busqueda" class="sr-only">Nombre cliente</label>
          <InputText id="modal-nombre-busqueda" name="modalNombreBusqueda" v-model="nombreBusquedaModal" class="w-full" placeholder="Ingrese nombre del cliente" @keyup.enter="buscarClienteModal" aria-label="Nombre cliente" />
          <Button label="Buscar" icon="pi pi-search" class="p-button-warn" @click="buscarClienteModal" />
        </div>
        <DataTable :value="resultadosBusqueda" class="mb-2" v-if="resultadosBusqueda.length" selectionMode="single"
          v-model:selection="clienteSeleccionadoModal" dataKey="id">
          <Column selectionMode="single" headerStyle="width: 3rem"></Column>
          <Column field="nombre" header="Nombre"></Column>
          <Column field="direccion" header="Dirección"></Column>
          <Column field="telefono" header="Teléfono"></Column>
        </DataTable>
        <div v-else class="text-gray-500 text-center">No hay resultados.</div>
        <div class="flex justify-end mt-4">
          <Button label="Aceptar" icon="pi pi-check" class="p-button-danger"
            :disabled="!clienteSeleccionadoModal" @click="confirmarSeleccionCliente" />
        </div>
      </Dialog>

      <!-- Modal de búsqueda de tour -->
      <Dialog
        v-model:visible="mostrarModalBuscarTour"
        modal
        header="Buscar Tour"
        :style="{ width: '90vw', maxWidth: '500px' }"
        @hide="limpiarModalTour"
      >
        <div class="mb-4 flex gap-2">
          <label for="modal-nombre-busqueda-tour" class="sr-only">Nombre tour</label>
          <InputText id="modal-nombre-busqueda-tour" name="modalNombreBusquedaTour" v-model="nombreBusquedaTourModal" class="w-full" placeholder="Ingrese nombre del tour" @keyup.enter="buscarTourModal" aria-label="Nombre tour" />
          <Button label="Buscar" icon="pi pi-search" class="p-button-warn" @click="buscarTourModal" />
        </div>
        <DataTable :value="resultadosBusquedaTour" class="mb-2" v-if="resultadosBusquedaTour.length" selectionMode="single"
          v-model:selection="tourSeleccionadoModal" dataKey="id">
          <Column selectionMode="single" headerStyle="width: 3rem"></Column>
          <Column field="nombre" header="Nombre"></Column>
          <Column field="precio" header="Precio"></Column>
          <Column field="hora_regreso" header="Hora regreso"></Column>
        </DataTable>
        <div v-else class="text-gray-500 text-center">No hay resultados.</div>
        <div class="flex justify-end mt-4">
          <Button label="Aceptar" icon="pi pi-check" class="p-button-danger"
            :disabled="!tourSeleccionadoModal" @click="confirmarSeleccionTour" />
        </div>
      </Dialog>

      <!-- Modal de edición de detalle -->
      <Dialog v-model:visible="mostrarModalEditar" modal header="Editar Detalle" :style="{ width: '90vw', maxWidth: '400px' }">
        <div v-if="detalleEditando !== null">
          <div class="mb-3">
            <label for="editar-fecha-tour" class="block font-semibold mb-1">Fecha del tour:</label>
            <DatePicker id="editar-fecha-tour" name="editarFechaTour" v-model="detalleEditando.fechaObj" dateFormat="dd/mm/yy" class="w-full" showIcon />
          </div>
          <div class="mb-3">
            <label for="editar-cupos" class="block font-semibold mb-1">Cupos:</label>
            <InputNumber id="editar-cupos" name="editarCupos" v-model="detalleEditando.cupos" :min="1" class="w-full" />
          </div>
          <!-- Solo campos editables, no tour ni precio -->
        </div>
        <div class="flex justify-end mt-4">
          <Button label="Aceptar" icon="pi pi-check" class="p-button-danger" @click="aceptarEdicionDetalle" />
        </div>
      </Dialog>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useToast } from 'primevue/usetoast'
import Toast from 'primevue/toast'
import Dialog from 'primevue/dialog'

// PrimeVue components
import InputText from 'primevue/inputtext'
import InputNumber from 'primevue/inputnumber'
import Button from 'primevue/button'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Select from 'primevue/select'
import DatePicker from 'primevue/datepicker'

const toast = useToast()

// Simulación de datos (deberían venir del backend)
const clientes = [
  { id: 1, nombre: 'Juan Diaz', direccion: 'Av. # 1, C. Principal, Casa # 4B, Col. San Juan', telefono: '77558899' },
  { id: 2, nombre: 'Ana Perez', direccion: 'Col. Escalón, Calle 5', telefono: '22223333' },
  { id: 3, nombre: 'Carlos López', direccion: 'Col. Miramonte, Calle 3', telefono: '77778888' },
  { id: 4, nombre: 'María González', direccion: 'Av. Las Palmas, #12, Col. San Benito', telefono: '55556666' },
  { id: 5, nombre: 'Pedro Martínez', direccion: 'Calle El Progreso, #8, Col. Escalón', telefono: '44445555' },
  { id: 6, nombre: 'Lucía Ramírez', direccion: 'Col. Médica, Calle 2', telefono: '33334444' },
  { id: 7, nombre: 'Sofía Herrera', direccion: 'Col. San Francisco, Casa 10', telefono: '22221111' },
  { id: 8, nombre: 'Miguel Torres', direccion: 'Av. Independencia, #20', telefono: '11112222' }
]
const tours = [
  { id: 1, nombre: 'Ruta Maya', precio: 45, fecha: new Date(), hora_regreso: '18:00' },
  { id: 2, nombre: 'Playa El Tunco', precio: 35, fecha: new Date(), hora_regreso: '17:00' },
  { id: 3, nombre: 'Cascada de Don Juan', precio: 40, fecha: new Date(), hora_regreso: '16:00' },
  // Tours adicionales
  { id: 4, nombre: 'Volcán de Izalco', precio: 50, fecha: new Date(), hora_regreso: '19:00' },
  { id: 5, nombre: 'Lago de Coatepeque', precio: 55, fecha: new Date(), hora_regreso: '18:30' },
  { id: 6, nombre: 'Ruta de las Flores', precio: 60, fecha: new Date(), hora_regreso: '20:00' },
  { id: 7, nombre: 'Suchitoto Colonial', precio: 38, fecha: new Date(), hora_regreso: '17:30' },
  { id: 8, nombre: 'Parque El Imposible', precio: 65, fecha: new Date(), hora_regreso: '21:00' }
]
const estados = ['Pendiente', 'Confirmada', 'Cancelada']

// Datos de la reserva
const cliente = ref(null)
const nombreBusqueda = ref('')
const direccion = ref('')
const telefono = ref('')
const fechaReserva = ref(new Date())
const estado = ref(estados[0])
const numeroReserva = ref('')
const tourSeleccionado = ref(null)
const fechaTour = ref(null)
const cupos = ref(1)
const precioUnitario = ref(0)
const detalles = ref([])
const errores = ref({})

// Modal de búsqueda
const mostrarModalBuscar = ref(false)
const nombreBusquedaModal = ref('')
const resultadosBusqueda = ref([])
const clienteSeleccionadoModal = ref(null)

// Modal de búsqueda de tour
const mostrarModalBuscarTour = ref(false)
const nombreBusquedaTourModal = ref('')
const resultadosBusquedaTour = ref([])
const tourSeleccionadoModal = ref(null)

// Modal de edición de detalle
const mostrarModalEditar = ref(false)
const detalleEditando = ref(null)
const detalleEditandoIndex = ref(null)

function buscarClienteModal() {
  resultadosBusqueda.value = clientes.filter(c =>
    c.nombre.toLowerCase().includes(nombreBusquedaModal.value.trim().toLowerCase())
  )
}

function confirmarSeleccionCliente() {
  if (clienteSeleccionadoModal.value) {
    cliente.value = clienteSeleccionadoModal.value
    direccion.value = clienteSeleccionadoModal.value.direccion
    telefono.value = clienteSeleccionadoModal.value.telefono
    numeroReserva.value = clienteSeleccionadoModal.value.id
  }
  mostrarModalBuscar.value = false
  nombreBusquedaModal.value = ''
  resultadosBusqueda.value = []
}

function buscarTourModal() {
  resultadosBusquedaTour.value = tours.filter(t =>
    t.nombre.toLowerCase().includes(nombreBusquedaTourModal.value.trim().toLowerCase())
  )
  tourSeleccionadoModal.value = null // Limpiar selección anterior
}

function confirmarSeleccionTour() {
  if (tourSeleccionadoModal.value) {
    tourSeleccionado.value = tourSeleccionadoModal.value
    precioUnitario.value = tourSeleccionadoModal.value.precio
  }
  mostrarModalBuscarTour.value = false
  nombreBusquedaTourModal.value = ''
  resultadosBusquedaTour.value = []
  tourSeleccionadoModal.value = null
}

function abrirEditarDetalle(index) {
  const detalle = detalles.value[index]
  if (!detalle) return

  let fechaObj = new Date()
  if (detalle.fecha instanceof Date) {
    fechaObj = detalle.fecha
  } else if (typeof detalle.fecha === 'string' && detalle.fecha.trim() !== '') {
    // Intenta convertir dd/mm/yyyy a Date
    const partes = detalle.fecha.split('/')
    if (partes.length === 3) {
      // new Date(año, mes-1, día)
      fechaObj = new Date(Number(partes[2]), Number(partes[1]) - 1, Number(partes[0]))
    }
  }

  detalleEditando.value = {
    ...detalle,
    fechaObj
  }
  detalleEditandoIndex.value = index
  mostrarModalEditar.value = true
}

function aceptarEdicionDetalle() {
  if (detalleEditandoIndex.value !== null && detalleEditando.value) {
    // Actualiza los campos editables
    const idx = detalleEditandoIndex.value
    const detalle = detalles.value[idx]
    detalle.cupos = detalleEditando.value.cupos
    // Actualiza la fecha en formato local
    detalle.fecha = detalleEditando.value.fechaObj ? detalleEditando.value.fechaObj.toLocaleDateString() : ''
    // Recalcula el subtotal
    detalle.subtotal = detalle.cupos * detalle.precio_unitario
    // Cierra el modal
    mostrarModalEditar.value = false
    detalleEditando.value = null
    detalleEditandoIndex.value = null
  }
}

watch(cliente, (val) => {
  if (val) {
    direccion.value = val.direccion
    telefono.value = val.telefono
    numeroReserva.value = val.id
  } else {
    direccion.value = ''
    telefono.value = ''
    numeroReserva.value = ''
  }
})

watch(tourSeleccionado, (val) => {
  precioUnitario.value = val ? val.precio : 0
})

const totalGeneral = computed(() =>
  detalles.value.reduce((acc, item) => acc + item.subtotal, 0)
)

function agregarDetalle() {
  if (!tourSeleccionado.value || !fechaTour.value || cupos.value < 1) return

  detalles.value.push({
    id: numeroReserva.value, // Agregar el id de la reserva
    tour: tourSeleccionado.value.nombre,
    fecha: fechaTour.value ? fechaTour.value.toLocaleDateString() : '',
    cupos: cupos.value,
    precio_unitario: precioUnitario.value,
    subtotal: precioUnitario.value * cupos.value
  })

  // Limpiar campos de detalle
  tourSeleccionado.value = null
  fechaTour.value = null
  cupos.value = 1
  precioUnitario.value = 0
}

function borrarDetalle(index) {
  detalles.value.splice(index, 1)
}

function precioBody(row) {
  return `$${row.precio_unitario.toFixed(2)}`
}
function subtotalBody(row) {
  return `$${row.subtotal.toFixed(2)}`
}

function validarFormulario() {
  errores.value = {}
  if (!cliente.value) errores.value.cliente = 'Seleccione un cliente.'
  if (detalles.value.length === 0) errores.value.detalles = 'Agregue al menos un tour.'
  // Puedes agregar más validaciones aquí
  return Object.keys(errores.value).length === 0
}

function guardarReserva() {
  if (!validarFormulario()) return
  // Aquí va la lógica para guardar (ejemplo: llamar a la API)
  toast.add({ severity: 'success', summary: 'Éxito', detail: 'Reserva guardada correctamente', life: 3000 })
}

function imprimirReserva() {
  window.print()
}

function buscarCliente() {
  errores.value.busqueda = ''
  const encontrado = clientes.find(c =>
    c.nombre.toLowerCase().includes(nombreBusqueda.value.trim().toLowerCase())
  )
  if (encontrado) {
    cliente.value = encontrado
    direccion.value = encontrado.direccion
    telefono.value = encontrado.telefono
    numeroReserva.value = encontrado.id
  } else {
    cliente.value = null
    direccion.value = ''
    telefono.value = ''
    numeroReserva.value = ''
    errores.value.busqueda = 'Cliente no encontrado.'
  }
}

function limpiarModalTour() {
  nombreBusquedaTourModal.value = ''
  resultadosBusquedaTour.value = []
  tourSeleccionadoModal.value = null
}
</script>

<style scoped>
th, td {
  border: 1px solid #ccc;
  word-break: break-word;
}
@media (max-width: 768px) {
  th, td {
    font-size: 0.95rem;
    padding: 0.25rem 0.5rem;
  }
}
</style>
