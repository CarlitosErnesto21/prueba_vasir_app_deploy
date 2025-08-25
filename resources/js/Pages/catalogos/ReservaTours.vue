<template>
  <Head title="Reservas de Tours" />
  <AuthenticatedLayout>
    <Toast />
    <div class="py-6 px-2 sm:px-4 md:px-6 mt-10 mx-auto bg-red-50 shadow-md rounded-lg max-w-full">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold text-center w-full">Gestión de Reserva</h3>
      </div>
      <!-- Filtros superiores con títulos alineados -->
      <div class="bg-white p-4 rounded shadow mb-4 border border-blue-300">
        <div class="flex flex-col md:flex-row md:items-start md:gap-12">
          <div class="flex flex-col mb-4 md:mb-0 md:w-1/3">
            <span class="font-semibold text-base text-gray-700 mb-2">Filtrar por tipo de reserva</span>
            <div class="flex flex-row gap-6">
              <!-- Radios accesibles: cada label envuelve su input y el id es único -->
              <label class="inline-flex items-center" for="radio-tours">
                <input id="radio-tours" type="radio" v-model="filtroTipo" value="tours" name="tipo-reserva" class="mr-2" />
                Tours
              </label>
              <label class="inline-flex items-center" for="radio-hoteles">
                <input id="radio-hoteles" type="radio" v-model="filtroTipo" value="hoteles" name="tipo-reserva" class="mr-2" />
                Hoteles
              </label>
              <label class="inline-flex items-center" for="radio-aerolineas">
                <input id="radio-aerolineas" type="radio" v-model="filtroTipo" value="aerolineas" name="tipo-reserva" class="mr-2" />
                Aerolíneas
              </label>
            </div>
          </div>
          <div class="flex-1 flex flex-col md:w-2/3">
            <span class="font-semibold text-base text-gray-700 mb-1">Filtrar por rango de fechas</span>
            <div class="flex flex-col md:flex-row md:items-end gap-2">
              <div class="flex flex-col md:flex-row md:items-center w-full gap-2">
                <div class="flex flex-col w-full md:w-1/2">
                  <label for="filtro-desde" class="block font-semibold mb-1">Desde:</label>
                  <DatePicker inputId="filtro-desde" id="filtro-desde" name="filtro-desde" v-model="filtroDesde" dateFormat="dd/mm/yy" class="w-full" showIcon />
                </div>
                <div class="flex flex-col w-full md:w-1/2">
                  <label for="filtro-hasta" class="block font-semibold mb-1">Hasta:</label>
                  <DatePicker inputId="filtro-hasta" id="filtro-hasta" name="filtro-hasta" v-model="filtroHasta" dateFormat="dd/mm/yy" class="w-full" showIcon />
                </div>
                <div class="flex items-end md:ml-4 mt-2 md:mt-0">
                  <Button label="Limpiar fechas" icon="pi pi-times" class="p-button-sm p-button-info" @click="limpiarFechas" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Selector de tipo de reservas -->
      <div class="flex flex-col md:flex-row items-center gap-4 mt-10 mb-2">
        <label for="tipo-estado" class="font-semibold mb-0">Ver reservas:</label>
        <select
          id="tipo-estado"
          name="tipo-estado"
          v-model="tipoEstadoSeleccionado"
          class="p-2 rounded border border-gray-300 appearance-none w-36"
          style="background-position: right 0.1rem center; background-repeat: no-repeat; background-size: 1.5em 1.5em;">
          <option value="pendientes">Pendientes</option>
          <option value="confirmadas">Confirmadas</option>
          <option value="rechazadas">Rechazadas</option>
          <option value="reprogramadas">Reprogramadas</option>
        </select>
        <!-- Buscador global por nombre de tour/hotel/aerolínea -->
        <label for="busqueda-nombre-general" class="sr-only">Buscar por nombre</label>
        <InputText
          id="busqueda-nombre-general"
          name="busqueda-nombre-general"
          v-model="busquedaNombreGeneral"
          placeholder="Buscar por nombre..."
          class="w-64"
        />
        <Button
          label="Historial de Reservas"
          class="p-button-info ml-2"
          @click="mostrarModalHistorial = true"
        />
      </div>
      <div class="overflow-x-auto">
        <template v-if="['pendientes','confirmadas','rechazadas','reprogramadas'].includes(tipoEstadoSeleccionado)">
          <h4 class="text-lg font-semibold mb-2">
            Reservas
            <span v-if="tipoEstadoSeleccionado === 'pendientes'">pendientes por confirmar</span>
            <span v-else-if="tipoEstadoSeleccionado === 'confirmadas'">confirmadas</span>
            <span v-else-if="tipoEstadoSeleccionado === 'rechazadas'">rechazadas</span>
            <span v-else-if="tipoEstadoSeleccionado === 'reprogramadas'">reprogramadas</span>
          </h4>
          <template v-if="tipoEstadoSeleccionado === 'pendientes'">
            <DataTable
              :value="filtroTipo === 'tours' ? toursResumenFiltrado : otrosResumenFiltrado"
              class="mb-4 min-w-[400px] w-full"
              :rows="5"
              :paginator="(filtroTipo === 'tours' ? toursResumenFiltrado.length : otrosResumenFiltrado.length) > 5"
              :rowsPerPageOptions="[5, 10]"
              scrollable
              scrollHeight="240px"
              v-if="(filtroTipo === 'tours' ? toursResumenFiltrado.length : otrosResumenFiltrado.length)"
            >
              <Column
                v-if="filtroTipo === 'tours'"
                field="nombre"
                header="Nombre del Tour"
              />
              <Column
                v-else
                field="nombre"
                :header="filtroTipo === 'hoteles' ? 'Nombre del Hotel' : 'Nombre de la Aerolínea'"
              />
              <Column header="Cantidad de Reservas">
                <template #body="slotProps">
                  {{ slotProps.data.cantidad }}
                </template>
              </Column>
              <Column header="Acciones">
                <template #body="slotProps">
                  <Button
                    label="Ver reservas"
                    icon="pi pi-eye"
                    class="p-button-sm p-button-info"
                    @click="() => abrirModalReservas(filtroTipo, slotProps.data.nombre)"
                  />
                </template>
              </Column>
            </DataTable>
            <div v-else class="text-gray-500 text-center" style="height: 240px; display: flex; align-items: center; justify-content: center;">
              No hay reservas en este rango.
            </div>
          </template>
          <template v-else>
            <DataTable
              :value="reservasPorEstado"
              class="mb-4 min-w-[400px] w-full"
              :rows="5"
              :paginator="reservasPorEstado.length > 5"
              :rowsPerPageOptions="[5, 10]"
              scrollable
              scrollHeight="240px"
              v-if="reservasPorEstado.length"
            >
              <Column field="tipo" header="Tipo"></Column>
              <Column header="Nombre">
                <template #body="slotProps">
                  {{ slotProps.data.nombreTour || slotProps.data.nombreTipo || '-' }}
                </template>
              </Column>
              <Column field="cliente" header="Cliente"></Column>
              <Column field="fecha" header="Fecha"></Column>
              <Column field="Cupos" header="Cupos">
                <template #body="slotProps">
                  {{ slotProps.data.Cupos }}
                </template>
              </Column>
              <Column field="Precio Unitario" header="Precio Unitario">
                <template #body="slotProps">
                  ${{ slotProps.data["Precio Unitario"] }}
                </template>
              </Column>
              <Column field="Monto" header="Monto">
                <template #body="slotProps">
                  ${{ (slotProps.data.Cupos * slotProps.data["Precio Unitario"]).toFixed(2) }}
                </template>
              </Column>
              <Column
                v-if="tipoEstadoSeleccionado !== 'rechazadas' && tipoEstadoSeleccionado !== 'confirmadas' && tipoEstadoSeleccionado !== 'reprogramadas'"
                field="estado"
                header="Estado"
              >
                <template #body="slotProps">
                  <span class="ml-2 px-2 py-1 rounded text-xs"
                    :class="slotProps.data.estado === 'Pendiente' ? 'bg-yellow-200 text-yellow-800' : slotProps.data.estado === 'Confirmado' ? 'bg-green-200 text-green-800' : slotProps.data.estado === 'Rechazada' ? 'bg-red-200 text-red-800' : slotProps.data.estado === 'Reprogramada' ? 'bg-blue-200 text-black' : ''">
                    {{ slotProps.data.estado }}
                  </span>
                </template>
              </Column>
              <Column
                header="Acciones"
                v-if="tipoEstadoSeleccionado !== 'rechazadas'"
              >
                <template #body="slotProps">
                  <!-- Mostrar botones solo en Confirmadas y Reprogramadas -->
                  <Button
                    v-if="tipoEstadoSeleccionado === 'confirmadas'"
                    label="Rechazar"
                    icon="pi pi-times"
                    class="p-button-xs p-button-danger mr-2"
                    @click="cambiarEstado(slotProps.data, 'Rechazada')"
                  />
                  <Button
                    v-if="tipoEstadoSeleccionado === 'confirmadas'"
                    label="Reprogramar"
                    icon="pi pi-refresh"
                    class="p-button-xs p-button-help mr-2"
                    @click="abrirModalReprogramar(slotProps.data)"
                  />
                  <Button
                    v-if="tipoEstadoSeleccionado === 'confirmadas'"
                    label="Finalizar"
                    icon="pi pi-check-circle"
                    class="p-button-xs p-button-success"
                    @click="finalizarReserva(slotProps.data)"
                  />
                  <Button
                    v-if="tipoEstadoSeleccionado === 'reprogramadas'"
                    label="Rechazar"
                    icon="pi pi-times"
                    class="p-button-xs p-button-danger mr-2"
                    @click="cambiarEstado(slotProps.data, 'Rechazada')"
                  />
                  <Button
                    v-if="tipoEstadoSeleccionado === 'reprogramadas'"
                    label="Finalizar"
                    icon="pi pi-check-circle"
                    class="p-button-xs p-button-success"
                    @click="finalizarReserva(slotProps.data)"
                  />
                </template>
              </Column>
            </DataTable>
            <div v-else class="text-gray-500 text-center" style="height: 240px; display: flex; align-items: center; justify-content: center;">
              No hay reservas en este rango.
            </div>
          </template>
        </template>
      </div>
      <!-- Modal único para reservas por tour/hotel/aerolínea -->
      <Dialog
        v-model:visible="mostrarModalReservasTour"
        modal
        header="Reservas"
        :style="{ width: '98vw', maxWidth: '1100px' }"
        :draggable="false"
        :position="'center'"
      >
        <div>
          <!-- Buscador en el modal -->
          <div class="flex flex-col md:flex-row gap-2 mb-3">
            <InputText
              id="busqueda-modal-cliente"
              name="busqueda-modal-cliente"
              v-model="busquedaModalCliente"
              placeholder="Buscar por cliente"
              class="w-full md:w-1/2"
            />
            <DatePicker
              id="busqueda-modal-fecha"
              name="busqueda-modal-fecha"
              v-model="busquedaModalFecha"
              placeholder="Buscar por fecha"
              dateFormat="dd/mm/yy"
              class="w-full md:w-1/2"
              showIcon
            />
          </div>
          <div class="overflow-y-auto" style="max-height: 350px;">
            <DataTable
              :value="reservasFiltradasModal"
              class="min-w-[350px] w-full"
              :rows="5"
              :paginator="reservasFiltradasModal.length > 5"
              :rowsPerPageOptions="[5, 10]"
              scrollable
              scrollHeight="320px"
              v-if="reservasFiltradasModal.length"
            >
              <Column field="cliente" header="Cliente"></Column>
              <Column field="fecha" header="Fecha"></Column>
              <Column field="Precio Unitario" header="Precio Unitario">
                <template #body="slotProps">
                  ${{ slotProps.data["Precio Unitario"] }}
                </template>
              </Column>
              <Column field="Cupos" header="Cupos">
                <template #body="slotProps">
                  {{ slotProps.data.Cupos }}
                </template>
              </Column>
              <Column field="Monto" header="Monto">
                <template #body="slotProps">
                  ${{ (slotProps.data.Cupos * slotProps.data["Precio Unitario"]).toFixed(2) }}
                </template>
              </Column>
              <Column field="estado" header="Estado">
                <template #body="slotProps">
                  <span class="ml-2 px-2 py-1 rounded text-xs"
                    :class="slotProps.data.estado === 'Pendiente' ? 'bg-yellow-200 text-yellow-800' : slotProps.data.estado === 'Confirmado' ? 'bg-green-200 text-green-800' : slotProps.data.estado === 'Rechazada' ? 'bg-red-200 text-red-800' : ''">
                    {{ slotProps.data.estado }}
                  </span>
                </template>
              </Column>
              <Column header="Acción">
                <template #body="slotProps">
                  <Button
                    v-if="slotProps.data.estado === 'Pendiente'"
                    label="Confirmar"
                    icon="pi pi-check"
                    class="p-button-xs p-button-success ml-2"
                    @click="confirmarReserva(slotProps.data)"
                  />
                  <Button
                    v-if="slotProps.data.estado === 'Pendiente'"
                    label="Rechazar"
                    icon="pi pi-times"
                    class="p-button-xs p-button-danger ml-2"
                    @click="cambiarEstado(slotProps.data, 'Rechazada')"
                  />
                </template>
              </Column>
            </DataTable>
            <div v-else class="text-gray-500 text-center py-4">No hay reservas para este elemento.</div>
          </div>
        </div>
      </Dialog>
      <!-- Modal para reprogramar reserva -->
      <Dialog
        v-model:visible="mostrarModalReprogramar"
        modal
        header="Reprogramar reserva"
        :style="{ width: '350px' }"
        :draggable="false"
        :position="'center'"
      >
        <div v-if="reservaAReprogramar">
          <div class="mb-4">
            <label class="block font-semibold mb-1" for="cliente-reprogramar">Cliente:</label>
            <span id="cliente-reprogramar">{{ reservaAReprogramar.cliente }}</span>
          </div>
          <div class="mb-4">
            <label class="block font-semibold mb-1" for="nueva-fecha">Nueva fecha:</label>
            <DatePicker
              id="nueva-fecha"
              inputId="nueva-fecha"
              v-model="nuevaFechaReprogramar"
              dateFormat="dd/mm/yy"
              class="w-full"
              showIcon
            />
          </div>
        </div>
        <div class="flex justify-end gap-2 mt-4">
          <Button label="Cerrar" icon="pi pi-times" class="p-button-danger" @click="cerrarModalReprogramar" />
          <Button label="Guardar" icon="pi pi-save" class="p-button-warn" @click="guardarReprogramacion" :disabled="!nuevaFechaReprogramar" />
        </div>
      </Dialog>
      <!-- Modal Historial de Reservas -->
      <Dialog
        v-model:visible="mostrarModalHistorial"
        modal
        header="Historial de Reservas"
        :style="{ width: '98vw', maxWidth: '1100px' }"
        :draggable="false"
        :position="'center'"
      >
        <div>
          <div class="flex flex-col md:flex-row gap-3 mb-3">
            <div class="w-full md:w-1/2 flex flex-col">
              <span class="block font-semibold mb-1">Buscar:</span>
              <InputText
                id="busqueda-historial-nombre"
                name="busqueda-historial-nombre"
                v-model="busquedaHistorialNombre"
                placeholder="Buscar por nombre o tipo..."
                class="w-full mt-2 md:mt-3"/>
            </div>
            <div class="flex flex-col md:flex-row gap-2 w-full md:w-1/2">
              <div class="w-full">
                <label class="block font-semibold mb-2 text-center">Filtrar por fechas:</label>
                <div class="flex flex-col md:flex-row gap-2 items-center">
                  <DatePicker
                    id="busqueda-historial-desde"
                    inputId="busqueda-historial-desde"
                    v-model="busquedaHistorialDesde"
                    placeholder="Desde"
                    dateFormat="dd/mm/yy"
                    class="w-full"
                    showIcon
                  />
                  <DatePicker
                    id="busqueda-historial-hasta"
                    inputId="busqueda-historial-hasta"
                    v-model="busquedaHistorialHasta"
                    placeholder="Hasta"
                    dateFormat="dd/mm/yy"
                    class="w-full"
                    showIcon
                  />
                  <div class="flex md:block w-full md:w-auto">
                    <Button
                      label="Limpiar fechas"
                      icon="pi pi-times"
                      class="p-button-sm p-button-info w-full md:w-auto mt-2 md:mt-0"
                      @click="() => { busquedaHistorialDesde = null; busquedaHistorialHasta = null }"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Filtro por tipo de reserva -->
          <div class="flex flex-wrap gap-4 mb-3 items-center">
            <label class="font-semibold">Tipo de reserva:</label>
            <select
              v-model="filtroTipoHistorial"
              class="p-2 rounded border border-gray-300 w-40"
            >
              <option value="">Todos</option>
              <option value="tours">Tours</option>
              <option value="hoteles">Hoteles</option>
              <option value="aerolineas">Aerolíneas</option>
            </select>
          </div>
          <div class="overflow-y-auto" style="max-height: 350px;">
            <DataTable
              :value="reservasFiltradasHistorial"
              class="min-w-[350px] w-full"
              :rows="5"
              :paginator="reservasFiltradasHistorial.length > 5"
              :rowsPerPageOptions="[5, 10]"
              scrollable
              scrollHeight="320px"
              v-if="reservasFiltradasHistorial.length"
            >
              <Column field="tipo" header="Tipo"></Column>
              <Column header="Nombre">
                <template #body="slotProps">
                  {{ slotProps.data.nombreTour || slotProps.data.nombreTipo || '-' }}
                </template>
              </Column>
              <Column field="cliente" header="Cliente"></Column>
              <Column field="fecha" header="Fecha"></Column>
              <Column field="Cupos" header="Cupos">
                <template #body="slotProps">
                  {{ slotProps.data.Cupos }}
                </template>
              </Column>
              <Column field="Precio Unitario" header="Precio Unitario">
                <template #body="slotProps">
                  ${{ slotProps.data["Precio Unitario"] }}
                </template>
              </Column>
              <Column field="Monto" header="Monto">
                <template #body="slotProps">
                  ${{ (slotProps.data.Cupos * slotProps.data["Precio Unitario"]).toFixed(2) }}
                </template>
              </Column>
              <Column field="estado" header="Estado">
                <template #body="slotProps">
                  <span class="ml-2 px-2 py-1 rounded text-xs"
                    :class="slotProps.data.estado === 'Pendiente' ? 'bg-yellow-200 text-yellow-800' : slotProps.data.estado === 'Confirmado' ? 'bg-green-200 text-green-800' : slotProps.data.estado === 'Rechazada' ? 'bg-red-200 text-red-800' : slotProps.data.estado === 'Reprogramada' ? 'bg-blue-200 text-black' : ''">
                    {{ slotProps.data.estado }}
                  </span>
                </template>
              </Column>
            </DataTable>
            <div v-else class="text-gray-500 text-center py-4">No hay reservas en el historial.</div>
          </div>
          <!-- Botón Cerrar eliminado -->
        </div>
      </Dialog>
      <ConfirmDialog :draggable="false" :position="'center'" />
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Dialog from 'primevue/dialog'
import Button from 'primevue/button'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import DatePicker from 'primevue/datepicker'
import InputText from 'primevue/inputtext'
import ConfirmDialog from 'primevue/confirmdialog'
import { useConfirm } from 'primevue/useconfirm'
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast'

// Filtros
const filtroDesde = ref(null)
const filtroHasta = ref(null)
const filtroTipo = ref('tours')

// Filtro para el historial de reservas
const filtroTipoHistorial = ref('')

// Variables para el modal de reservas
const mostrarModalReservasTour = ref(false)
const reservasTourSeleccionado = ref([])

// Usa useToast correctamente
const toast = useToast()

// Variables para búsqueda en el modal
const busquedaModalCliente = ref('')
const busquedaModalFecha = ref('')

// Computed para filtrar las reservas del modal según búsqueda
const reservasFiltradasModal = computed(() => {
  let lista = reservasTourSeleccionado.value || []
  if (busquedaModalCliente.value) {
    lista = lista.filter(r =>
      r.cliente.toLowerCase().includes(busquedaModalCliente.value.toLowerCase())
    )
  }
  if (busquedaModalFecha.value) {
    lista = lista.filter(r =>
      r.fecha.includes(busquedaModalFecha.value)
    )
  }
  return lista
})

// Simulación de reservas de distintos tipos y tours
const reservas = ref([
  // Tours
  { id: 1, tipo: 'tours', nombreTour: 'Ruta Maya', cliente: 'Julio Deras', fecha: '2025-05-02', estado: 'Pendiente', "Precio Unitario": 120, Cupos: 2 },
  { id: 2, tipo: 'tours', nombreTour: 'Ruta Maya', cliente: 'Vanesa Cruz', fecha: '2025-05-07', estado: 'Confirmado', "Precio Unitario": 80, Cupos: 1 },
  { id: 3, tipo: 'tours', nombreTour: 'Ruta Maya', cliente: 'Luis Flores', fecha: '2025-05-10', estado: 'Pendiente', "Precio Unitario": 90, Cupos: 3 },
  { id: 4, tipo: 'tours', nombreTour: 'Playa El Tunco', cliente: 'Ana Pérez', fecha: '2025-05-03', estado: 'Pendiente', "Precio Unitario": 200, Cupos: 2 },
  { id: 5, tipo: 'tours', nombreTour: 'Playa El Tunco', cliente: 'Carlos López', fecha: '2025-05-08', estado: 'Pendiente', "Precio Unitario": 150, Cupos: 1 },
  { id: 6, tipo: 'tours', nombreTour: 'Cascada de Don Juan', cliente: 'María González', fecha: '2025-05-04', estado: 'Pendiente', "Precio Unitario": 300, Cupos: 4 },
  { id: 13, tipo: 'tours', nombreTour: 'Ruta de las Flores', cliente: 'Andrea Molina', fecha: '2025-05-12', estado: 'Pendiente', "Precio Unitario": 180, Cupos: 2 },
  { id: 14, tipo: 'tours', nombreTour: 'Ruta de las Flores', cliente: 'Roberto Salazar', fecha: '2025-05-13', estado: 'Pendiente', "Precio Unitario": 185, Cupos: 1 },
  { id: 15, tipo: 'tours', nombreTour: 'Suchitoto Colonial', cliente: 'Patricia Ramos', fecha: '2025-05-14', estado: 'Pendiente', "Precio Unitario": 210, Cupos: 2 },
  { id: 16, tipo: 'tours', nombreTour: 'Suchitoto Colonial', cliente: 'Miguel Torres', fecha: '2025-05-15', estado: 'Pendiente', "Precio Unitario": 220, Cupos: 1 },
  { id: 17, tipo: 'tours', nombreTour: 'Volcán de Izalco', cliente: 'Sofía Herrera', fecha: '2025-05-16', estado: 'Pendiente', "Precio Unitario": 250, Cupos: 3 },
  { id: 18, tipo: 'tours', nombreTour: 'Volcán de Izalco', cliente: 'Gabriela Díaz', fecha: '2025-05-17', estado: 'Pendiente', "Precio Unitario": 260, Cupos: 2 },
  // Hoteles
  { id: 7, tipo: 'hoteles', nombreTipo: 'Hotel Real', cliente: 'Pedro Martínez', fecha: '2025-05-09', estado: 'Pendiente', "Precio Unitario": 250, Cupos: 1 },
  { id: 8, tipo: 'hoteles', nombreTipo: 'Hotel Real', cliente: 'Lucía Ramírez', fecha: '2025-05-12', estado: 'Pendiente', "Precio Unitario": 180, Cupos: 2 },
  { id: 9, tipo: 'hoteles', nombreTipo: 'Hotel Plaza', cliente: 'Ana López', fecha: '2025-05-13', estado: 'Pendiente', "Precio Unitario": 210, Cupos: 1 },
  { id: 19, tipo: 'hoteles', nombreTipo: 'Hotel Plaza', cliente: 'Jorge Castillo', fecha: '2025-05-14', estado: 'Pendiente', "Precio Unitario": 215, Cupos: 2 },
  { id: 20, tipo: 'hoteles', nombreTipo: 'Hotel Boutique', cliente: 'Carmen Figueroa', fecha: '2025-05-15', estado: 'Pendiente', "Precio Unitario": 300, Cupos: 1 },
  { id: 21, tipo: 'hoteles', nombreTipo: 'Hotel Boutique', cliente: 'Ricardo Méndez', fecha: '2025-05-16', estado: 'Pendiente', "Precio Unitario": 320, Cupos: 2 },
  { id: 22, tipo: 'hoteles', nombreTipo: 'Hotel San Benito', cliente: 'Estela Morales', fecha: '2025-05-17', estado: 'Pendiente', "Precio Unitario": 180, Cupos: 1 },
  // Aerolíneas
  { id: 10, tipo: 'aerolineas', nombreTipo: 'AeroExpress', cliente: 'Sofía Herrera', fecha: '2025-05-11', estado: 'Pendiente', "Precio Unitario": 400, Cupos: 1 },
  { id: 11, tipo: 'aerolineas', nombreTipo: 'AeroExpress', cliente: 'Carlos Méndez', fecha: '2025-05-14', estado: 'Pendiente', "Precio Unitario": 350, Cupos: 2 },
  { id: 12, tipo: 'aerolineas', nombreTipo: 'VuelaYa', cliente: 'Luis Pérez', fecha: '2025-05-15', estado: 'Pendiente', "Precio Unitario": 420, Cupos: 1 },
  { id: 23, tipo: 'aerolineas', nombreTipo: 'VuelaYa', cliente: 'Marina López', fecha: '2025-05-16', estado: 'Pendiente', "Precio Unitario": 430, Cupos: 2 },
  { id: 24, tipo: 'aerolineas', nombreTipo: 'SkyTravel', cliente: 'Oscar Rivera', fecha: '2025-05-17', estado: 'Pendiente', "Precio Unitario": 390, Cupos: 1 },
  { id: 25, tipo: 'aerolineas', nombreTipo: 'SkyTravel', cliente: 'Daniela Cruz', fecha: '2025-05-18', estado: 'Pendiente', "Precio Unitario": 410, Cupos: 2 },
  { id: 26, tipo: 'aerolineas', nombreTipo: 'FlyNow', cliente: 'Felipe Gómez', fecha: '2025-05-19', estado: 'Pendiente', "Precio Unitario": 370, Cupos: 1 },
  { id: 27, tipo: 'aerolineas', nombreTipo: 'FlyNow', cliente: 'Paola Martínez', fecha: '2025-05-20', estado: 'Pendiente', "Precio Unitario": 395, Cupos: 2 },
])

const toursResumen = computed(() => {
  if (filtroTipo.value !== 'tours') return []
  let filtradas = reservas.value.filter(r => r.tipo === 'tours' && r.estado === 'Pendiente')
  if (filtroDesde.value) {
    const desde = normalizaFecha(filtroDesde.value)
    filtradas = filtradas.filter(r => normalizaFecha(r.fecha) >= desde)
  }
  if (filtroHasta.value) {
    const hasta = normalizaFecha(filtroHasta.value)
    filtradas = filtradas.filter(r => normalizaFecha(r.fecha) <= hasta)
  }
  const resumen = []
  const toursMap = {}
  filtradas.forEach(r => {
    if (!toursMap[r.nombreTour]) {
      toursMap[r.nombreTour] = { nombre: r.nombreTour, cantidad: 0 }
      resumen.push(toursMap[r.nombreTour])
    }
    toursMap[r.nombreTour].cantidad++
  })
  return resumen
})

const otrosResumen = computed(() => {
  if (filtroTipo.value === 'tours') return []
  let filtradas = reservas.value.filter(r => r.tipo === filtroTipo.value && r.estado === 'Pendiente')
  if (filtroDesde.value) {
    const desde = normalizaFecha(filtroDesde.value)
    filtradas = filtradas.filter(r => normalizaFecha(r.fecha) >= desde)
  }
  if (filtroHasta.value) {
    const hasta = normalizaFecha(filtroHasta.value)
    filtradas = filtradas.filter(r => normalizaFecha(r.fecha) <= hasta)
  }
  const resumen = []
  const map = {}
  filtradas.forEach(r => {
    const nombre = r.nombreTipo
    if (!map[nombre]) {
      map[nombre] = { nombre, cantidad: 0 }
      resumen.push(map[nombre])
    }
    map[nombre].cantidad++
  })
  return resumen
})

function abrirModalReservas(tipo, nombre) {
  let filtradas = []
  if (tipo === 'tours') {
    filtradas = reservas.value.filter(r => r.tipo === 'tours' && r.estado === 'Pendiente' && r.nombreTour === nombre)
  } else {
    filtradas = reservas.value.filter(r => r.tipo === tipo && r.estado === 'Pendiente' && r.nombreTipo === nombre)
  }
  if (filtroDesde.value) {
    const desde = normalizaFecha(filtroDesde.value)
    filtradas = filtradas.filter(r => normalizaFecha(r.fecha) >= desde)
  }
  if (filtroHasta.value) {
    const hasta = normalizaFecha(filtroHasta.value)
    filtradas = filtradas.filter(r => normalizaFecha(r.fecha) <= hasta)
  }
  reservasTourSeleccionado.value = filtradas
  mostrarModalReservasTour.value = true
}

function confirmarReserva(reserva) {
  reserva.estado = 'Confirmado'
  // Opcional: cerrar el modal después de confirmar
  // mostrarModalReservasTour.value = false
}

function cambiarEstado(reserva, nuevoEstado) {
  reserva.estado = 'Rechazada'
}

const reservasConfirmadas = computed(() => {
  let filtradas = reservas.value.filter(r => r.estado === 'Confirmado' && r.tipo === filtroTipo.value)
  if (filtroDesde.value) {
    const desde = normalizaFecha(filtroDesde.value)
    filtradas = filtradas.filter(r => normalizaFecha(r.fecha) >= desde)
  }
  if (filtroHasta.value) {
    const hasta = normalizaFecha(filtroHasta.value)
    filtradas = filtradas.filter(r => normalizaFecha(r.fecha) <= hasta)
  }
  return filtradas
})

const reservasRechazadas = computed(() => {
  let filtradas = reservas.value.filter(r => r.estado === 'Rechazada' && r.tipo === filtroTipo.value)
  if (filtroDesde.value) {
    const desde = normalizaFecha(filtroDesde.value)
    filtradas = filtradas.filter(r => normalizaFecha(r.fecha) >= desde)
  }
  if (filtroHasta.value) {
    const hasta = normalizaFecha(filtroHasta.value)
    filtradas = filtradas.filter(r => normalizaFecha(r.fecha) <= hasta)
  }
  return filtradas
})

const reservasPendientes = computed(() => {
  let filtradas = reservas.value.filter(r => r.estado === 'Pendiente' && r.tipo === filtroTipo.value)
  if (filtroDesde.value) {
    const desde = normalizaFecha(filtroDesde.value)
    filtradas = filtradas.filter(r => normalizaFecha(r.fecha) >= desde)
  }
  if (filtroHasta.value) {
    const hasta = normalizaFecha(filtroHasta.value)
    filtradas = filtradas.filter(r => normalizaFecha(r.fecha) <= hasta)
  }
  return filtradas
})

const reservasReprogramadas = computed(() => {
  let filtradas = reservas.value.filter(r => r.estado === 'Reprogramada' && r.tipo === filtroTipo.value)
  if (filtroDesde.value) {
    const desde = normalizaFecha(filtroDesde.value)
    filtradas = filtradas.filter(r => normalizaFecha(r.fecha) >= desde)
  }
  if (filtroHasta.value) {
    const hasta = normalizaFecha(filtroHasta.value)
    filtradas = filtradas.filter(r => normalizaFecha(r.fecha) <= hasta)
  }
  return filtradas
})

// Utilidad para normalizar fechas a Date (acepta Date o string 'YYYY-MM-DD')
function normalizaFecha(fecha) {
  if (!fecha) return null
  if (fecha instanceof Date) {
    // Solo la parte de año, mes, día
    return new Date(fecha.getFullYear(), fecha.getMonth(), fecha.getDate())
  }
  if (typeof fecha === 'string') {
    // Si es 'YYYY-MM-DD'
    const partes = fecha.split('-')
    if (partes.length === 3) {
      return new Date(Number(partes[0]), Number(partes[1]) - 1, Number(partes[2]))
    }
    // Si es 'DD/MM/YYYY'
    const partesSlash = fecha.split('/')
    if (partesSlash.length === 3) {
      return new Date(Number(partesSlash[2]), Number(partesSlash[1]) - 1, Number(partesSlash[0]))
    }
  }
  return new Date(fecha)
}

// Mostrar siempre registros recientes aunque no haya filtro, ordenados por fecha descendente

const toursResumenRecientes = computed(() => {
  let resumen = toursResumen.value
  // Ordenar por la fecha más reciente de cada grupo
  resumen = resumen
    .map(grupo => {
      // Buscar la fecha más reciente de las reservas de ese tour
      const fechas = reservas.value
        .filter(r => r.tipo === 'tours' && r.estado === 'Pendiente' && r.nombreTour === grupo.nombre)
        .map(r => normalizaFecha(r.fecha));
      return { ...grupo, fechaReciente: fechas.length ? Math.max(...fechas.map(f => f.getTime())) : 0 };
    })
    .sort((a, b) => b.fechaReciente - a.fechaReciente);
  return resumen;
});

const otrosResumenRecientes = computed(() => {
  let resumen = otrosResumen.value
  resumen = resumen
    .map(grupo => {
      const fechas = reservas.value
        .filter(r => r.tipo === filtroTipo.value && r.estado === 'Pendiente' && r.nombreTipo === grupo.nombre)
        .map(r => normalizaFecha(r.fecha));
      return { ...grupo, fechaReciente: fechas.length ? Math.max(...fechas.map(f => f.getTime())) : 0 };
    })
    .sort((a, b) => b.fechaReciente - a.fechaReciente);
  return resumen;
});

const reservasConfirmadasRecientes = computed(() => {
  return reservasConfirmadas.value
    .slice()
    .sort((a, b) => normalizaFecha(b.fecha) - normalizaFecha(a.fecha));
});

const reservasRechazadasRecientes = computed(() => {
  return reservasRechazadas.value
    .slice()
    .sort((a, b) => normalizaFecha(b.fecha) - normalizaFecha(a.fecha));
});

// Asegúrate de definir las siguientes variables y funciones con "const" o "let"
const limpiarFechas = () => {
  filtroDesde.value = null
  filtroHasta.value = null
}

const tipoEstadoSeleccionado = ref('pendientes')

// Watch para reiniciar el filtro de estado a 'pendientes' cuando cambia el tipo de reserva
watch(filtroTipo, () => {
  tipoEstadoSeleccionado.value = 'pendientes'
})

// Buscador global por nombre de tour/hotel/aerolínea
const busquedaNombreGeneral = ref('')

// Computed para filtrar los resúmenes por nombre y estado seleccionado
const toursResumenFiltrado = computed(() => {
  // Filtra las reservas según el estado y fechas
  let reservasFiltradas = reservas.value.filter(r => r.tipo === 'tours')
  if (tipoEstadoSeleccionado.value === 'confirmadas') {
    reservasFiltradas = reservasFiltradas.filter(r => r.estado === 'Confirmado')
  } else if (tipoEstadoSeleccionado.value === 'rechazadas') {
    reservasFiltradas = reservasFiltradas.filter(r => r.estado === 'Rechazada')
  } else if (tipoEstadoSeleccionado.value === 'pendientes') {
    reservasFiltradas = reservasFiltradas.filter(r => r.estado === 'Pendiente')
  } else if (tipoEstadoSeleccionado.value === 'reprogramadas') {
    reservasFiltradas = reservasFiltradas.filter(r => r.estado === 'Reprogramada')
  }
  // Filtra por fechas
  if (filtroDesde.value) {
    const desde = normalizaFecha(filtroDesde.value)
    reservasFiltradas = reservasFiltradas.filter(r => normalizaFecha(r.fecha) >= desde)
  }
  if (filtroHasta.value) {
    const hasta = normalizaFecha(filtroHasta.value)
    reservasFiltradas = reservasFiltradas.filter(r => normalizaFecha(r.fecha) <= hasta)
  }
  // Agrupa por nombreTour y cuenta
  const resumen = []
  const toursMap = {}
  reservasFiltradas.forEach(r => {
    if (!toursMap[r.nombreTour]) {
      toursMap[r.nombreTour] = { nombre: r.nombreTour, cantidad: 0 }
      resumen.push(toursMap[r.nombreTour])
    }
    toursMap[r.nombreTour].cantidad++
  })
  // Aplica el filtro de búsqueda
  if (!busquedaNombreGeneral.value) return resumen
  const search = busquedaNombreGeneral.value.toLowerCase()
  return resumen.filter(t =>
    t.nombre.toLowerCase().includes(search)
  )
})

const otrosResumenFiltrado = computed(() => {
  // Filtra las reservas según el estado y fechas
  let reservasFiltradas = reservas.value.filter(r => r.tipo === filtroTipo.value)
  if (tipoEstadoSeleccionado.value === 'confirmadas') {
    reservasFiltradas = reservasFiltradas.filter(r => r.estado === 'Confirmado')
  } else if (tipoEstadoSeleccionado.value === 'rechazadas') {
    reservasFiltradas = reservasFiltradas.filter(r => r.estado === 'Rechazada')
  } else if (tipoEstadoSeleccionado.value === 'pendientes') {
    reservasFiltradas = reservasFiltradas.filter(r => r.estado === 'Pendiente')
  } else if (tipoEstadoSeleccionado.value === 'reprogramadas') {
    reservasFiltradas = reservasFiltradas.filter(r => r.estado === 'Reprogramada')
  }
  // Filtra por fechas
  if (filtroDesde.value) {
    const desde = normalizaFecha(filtroDesde.value)
    reservasFiltradas = reservasFiltradas.filter(r => normalizaFecha(r.fecha) >= desde)
  }
  if (filtroHasta.value) {
    const hasta = normalizaFecha(filtroHasta.value)
    reservasFiltradas = reservasFiltradas.filter(r => normalizaFecha(r.fecha) <= hasta)
  }
  // Agrupa por nombreTipo y cuenta
  const resumen = []
  const map = {}
  reservasFiltradas.forEach(r => {
    const nombre = r.nombreTipo
    if (!map[nombre]) {
      map[nombre] = { nombre, cantidad: 0 }
      resumen.push(map[nombre])
    }
    map[nombre].cantidad++
  })
  // Aplica el filtro de búsqueda
  if (!busquedaNombreGeneral.value) return resumen
  const search = busquedaNombreGeneral.value.toLowerCase()
  return resumen.filter(o =>
    o.nombre.toLowerCase().includes(search)
  )
})

// Cambia el estado de todas las reservas de ese grupo (por nombre)
function cambiarEstadoPorNombre(nombre, nuevoEstado) {
  if (filtroTipo.value === 'tours') {
    reservas.value.forEach(r => {
      if (r.tipo === 'tours' && r.nombreTour === nombre && r.estado === (tipoEstadoSeleccionado.value === 'confirmadas' ? 'Confirmado' : 'Rechazada')) {
        r.estado = nuevoEstado
      }
    })
  } else {
    reservas.value.forEach(r => {
      if (r.tipo === filtroTipo.value && r.nombreTipo === nombre && r.estado === (tipoEstadoSeleccionado.value === 'confirmadas' ? 'Confirmado' : 'Rechazada')) {
        r.estado = nuevoEstado
      }
    })
  }
}

// Computed para mostrar reservas individuales por estado (confirmadas/rechazadas/pendientes) y aplicar filtros de búsqueda y fechas
const reservasPorEstado = computed(() => {
  let lista = reservas.value.filter(r => r.tipo === filtroTipo.value)

  // Filtra por estado seleccionado
  if (tipoEstadoSeleccionado.value === 'confirmadas') {
    lista = lista.filter(r => r.estado === 'Confirmado')
  } else if (tipoEstadoSeleccionado.value === 'rechazadas') {
    lista = lista.filter(r => r.estado === 'Rechazada')
  } else if (tipoEstadoSeleccionado.value === 'pendientes') {
    lista = lista.filter(r => r.estado === 'Pendiente')
  } else if (tipoEstadoSeleccionado.value === 'reprogramadas') {
    lista = lista.filter(r => r.estado === 'Reprogramada')
  } else {
    lista = []
  }

  // Excluir las reservas finalizadas del listado principal
  lista = lista.filter(r => !reservasFinalizadasIds.value.includes(r.id) && r.estado !== 'Finalizada')

  // Filtra por fechas si hay filtro
  if (filtroDesde.value) {
    const desde = normalizaFecha(filtroDesde.value)
    lista = lista.filter(r => normalizaFecha(r.fecha) >= desde)
  }
  if (filtroHasta.value) {
    const hasta = normalizaFecha(filtroHasta.value)
    lista = lista.filter(r => normalizaFecha(r.fecha) <= hasta)
  }

  // Filtra por nombre de tour/hotel/aerolínea o cliente
  if (busquedaNombreGeneral.value) {
    const search = busquedaNombreGeneral.value.toLowerCase()
    lista = lista.filter(r => {
      const nombreEntidad = filtroTipo.value === 'tours' ? r.nombreTour : r.nombreTipo
      return (
        (nombreEntidad && nombreEntidad.toLowerCase().includes(search)) ||
        (r.cliente && r.cliente.toLowerCase().includes(search))
      )
    })
  }

  // Ordena por fecha descendente
  return lista.slice().sort((a, b) => normalizaFecha(b.fecha) - normalizaFecha(a.fecha))
})

// Modal de reprogramación
const mostrarModalReprogramar = ref(false)
const reservaAReprogramar = ref(null)
const nuevaFechaReprogramar = ref(null)

function abrirModalReprogramar(reserva) {
  reservaAReprogramar.value = reserva
  // Si la fecha es string tipo 'YYYY-MM-DD', conviértela a Date
  if (typeof reserva.fecha === 'string') {
    const partes = reserva.fecha.split('-')
    if (partes.length === 3) {
      nuevaFechaReprogramar.value = new Date(Number(partes[0]), Number(partes[1]) - 1, Number(partes[2]))
    } else {
      nuevaFechaReprogramar.value = null
    }
  } else if (reserva.fecha instanceof Date) {
    nuevaFechaReprogramar.value = reserva.fecha
  } else {
    nuevaFechaReprogramar.value = null
  }
  mostrarModalReprogramar.value = true
}

function cerrarModalReprogramar() {
  mostrarModalReprogramar.value = false
  reservaAReprogramar.value = null
  nuevaFechaReprogramar.value = null
}

function guardarReprogramacion() {
  if (reservaAReprogramar.value && nuevaFechaReprogramar.value) {
    // Formatea la fecha a 'YYYY-MM-DD'
    const fecha = nuevaFechaReprogramar.value
    const yyyy = fecha.getFullYear()
    const mm = String(fecha.getMonth() + 1).padStart(2, '0')
    const dd = String(fecha.getDate()).padStart(2, '0')
    reservaAReprogramar.value.fecha = `${yyyy}-${mm}-${dd}`
    reservaAReprogramar.value.estado = 'Reprogramada'
    cerrarModalReprogramar()
  }
}

const mostrarModalHistorial = ref(false)
const busquedaHistorialNombre = ref('')
// Elimina busquedaHistorialFecha y agrega desde/hasta
const busquedaHistorialDesde = ref(null)
const busquedaHistorialHasta = ref(null)

// Estado para guardar ids de reservas finalizadas
const reservasFinalizadasIds = ref([])

// Mostrar solo reservas finalizadas en el historial
const reservasFiltradasHistorial = computed(() => {
  // Solo mostrar reservas finalizadas (estado Finalizada o id en reservasFinalizadasIds)
  return reservas.value.filter(r =>
      reservasFinalizadasIds.value.includes(r.id) || r.estado === 'Finalizada'
    )
    .filter(r => {
      // Filtro por tipo de reserva (nuevo)
      if (filtroTipoHistorial.value && r.tipo !== filtroTipoHistorial.value) {
        return false
      }
      // Filtro por nombre, cliente o tipo
      if (busquedaHistorialNombre.value) {
        const search = busquedaHistorialNombre.value.toLowerCase()
        const nombreEntidad = r.tipo === 'tours' ? r.nombreTour : r.nombreTipo
        if (
          !(nombreEntidad && nombreEntidad.toLowerCase().includes(search)) &&
          !(r.cliente && r.cliente.toLowerCase().includes(search)) &&
          !(r.tipo && r.tipo.toLowerCase().includes(search))
        ) {
          return false
        }
      }
      // Filtro por rango de fechas
      if (busquedaHistorialDesde.value) {
        const desde = normalizaFecha(busquedaHistorialDesde.value)
        const fechaReserva = normalizaFecha(r.fecha)
        if (fechaReserva < desde) {
          return false
        }
      }
      if (busquedaHistorialHasta.value) {
        const hasta = normalizaFecha(busquedaHistorialHasta.value)
        const fechaReserva = normalizaFecha(r.fecha)
        if (fechaReserva > hasta) {
          return false
        }
      }
      return true
    })
    .sort((a, b) => normalizaFecha(b.fecha) - normalizaFecha(a.fecha))
})

// Función para finalizar una reserva
const confirm = useConfirm()

function finalizarReserva(reserva) {
  confirm.require({
    message: 'Esta acción es irreversible.',
    header: '¿Estás seguro de completar esta reserva?',
    icon: 'pi pi-question-circle',
    acceptLabel: 'Completar',
    rejectLabel: 'Cancelar',
    acceptClass: 'p-button-success',
    rejectClass: 'p-button-danger',
    accept: () => {
      if (!reservasFinalizadasIds.value.includes(reserva.id)) {
        reservasFinalizadasIds.value.push(reserva.id)
      }
      reserva.estado = 'Finalizada'
      setTimeout(() => {
        toast.add({
          severity: 'success',
          summary: 'Completada',
          detail: 'La reserva ha sido movida al historial.',
          life: 2000
        })
      }, 100)
    }
  })
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
