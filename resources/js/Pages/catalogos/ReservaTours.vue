<script setup>
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, onMounted, computed, watch } from 'vue'
import { useToast } from 'primevue/usetoast'
import axios from 'axios'
axios.defaults.withCredentials = true

// Configuración de Toast
const toast = useToast()

// Estado reactivo
const reservas = ref([])
const loading = ref(false)
const filtros = ref({
  tipo: 'tours',
  busqueda: '',
  fechaDesde: null,
  fechaHasta: null
})

// Estados para modales
const modalReprogramar = ref(false)
const modalRechazar = ref(false)
const reservaSeleccionada = ref(null)
const motivoRechazo = ref('')
const fechaNuevaReprogramacion = ref(null)
const motivoReprogramacion = ref('')

// Pestaña activa
const pestanaActiva = ref(0)

// Estados disponibles (ajustados a la BD)
const estados = [
  { label: 'Pendientes', value: 'PENDIENTE', severity: 'warn' },
  { label: 'Confirmadas', value: 'CONFIRMADA', severity: 'success' },
  { label: 'Rechazadas', value: 'RECHAZADA', severity: 'danger' },
  { label: 'Reprogramadas', value: 'REPROGRAMADA', severity: 'info' },
  { label: 'Finalizadas', value: 'FINALIZADA', severity: 'secondary' }
]

// Computed para filtrar reservas por estado
const reservasPorEstado = computed(() => {
  const estadoActual = estados[pestanaActiva.value]?.value
  return reservas.value.filter(reserva => reserva.estado === estadoActual)
})

// Función para cargar todas las reservas
const cargarReservas = async () => {
  loading.value = true
  try {
        // 🔍 DEBUGGING - Ver todas las cookies
    console.log('🍪 Cookies:', document.cookie)
    const params = {
      tipo: filtros.value.tipo,
      busqueda: filtros.value.busqueda || undefined,
      fecha_inicio: filtros.value.fechaDesde || undefined,
      fecha_fin: filtros.value.fechaHasta || undefined
    }

    // 🔍 DEBUGGING - Ver configuración de axios
    console.log('⚙️ Axios config:', {
      withCredentials: axios.defaults.withCredentials,
      params: params
    })

    const response = await axios.get('/api/reservas', { params, withCredentials: true })
    reservas.value = response.data.data || []

  } catch (error) {
    console.error('Error al cargar reservas:', error)
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'No se pudieron cargar las reservas',
      life: 3000
    })
    reservas.value = []
  } finally {
    loading.value = false
  }
}

// Función para confirmar reserva
const confirmarReserva = async (reserva) => {
  try {
    await axios.put(`/api/reservas/${reserva.id}/confirmar`)

    // Actualizar estado local
    const index = reservas.value.findIndex(r => r.id === reserva.id)
    if (index !== -1) {
      reservas.value[index].estado = 'CONFIRMADA'
    }

    toast.add({
      severity: 'success',
      summary: 'Éxito',
      detail: 'Reserva confirmada correctamente',
      life: 3000
    })

  } catch (error) {
    console.error('Error al confirmar reserva:', error)
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'No se pudo confirmar la reserva',
      life: 3000
    })
  }
}

// Función para abrir modal de rechazo
const abrirModalRechazar = (reserva) => {
  reservaSeleccionada.value = reserva
  motivoRechazo.value = ''
  modalRechazar.value = true
}

// Función para rechazar reserva
const rechazarReserva = async () => {
  if (!motivoRechazo.value.trim()) {
    toast.add({
      severity: 'warn',
      summary: 'Advertencia',
      detail: 'Debe ingresar un motivo para rechazar la reserva',
      life: 3000
    })
    return
  }

  try {
    await axios.put(`/api/reservas/${reservaSeleccionada.value.id}/rechazar`, {
      motivo: motivoRechazo.value
    })

    // Actualizar estado local
    const index = reservas.value.findIndex(r => r.id === reservaSeleccionada.value.id)
    if (index !== -1) {
      reservas.value[index].estado = 'RECHAZADA'
    }

    modalRechazar.value = false
    toast.add({
      severity: 'success',
      summary: 'Éxito',
      detail: 'Reserva rechazada correctamente',
      life: 3000
    })

  } catch (error) {
    console.error('Error al rechazar reserva:', error)
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'No se pudo rechazar la reserva',
      life: 3000
    })
  }
}

// Función para abrir modal de reprogramación
const abrirModalReprogramar = (reserva) => {
  reservaSeleccionada.value = reserva
  fechaNuevaReprogramacion.value = null
  motivoReprogramacion.value = ''
  modalReprogramar.value = true
}

// Función para reprogramar reserva
const reprogramarReserva = async () => {
  if (!fechaNuevaReprogramacion.value || !motivoReprogramacion.value.trim()) {
    toast.add({
      severity: 'warn',
      summary: 'Advertencia',
      detail: 'Debe ingresar una nueva fecha y motivo para reprogramar',
      life: 3000
    })
    return
  }

  try {
    await axios.put(`/api/reservas/${reservaSeleccionada.value.id}/reprogramar`, {
      fecha_nueva: fechaNuevaReprogramacion.value,
      motivo: motivoReprogramacion.value
    })

    // Actualizar estado local
    const index = reservas.value.findIndex(r => r.id === reservaSeleccionada.value.id)
    if (index !== -1) {
      reservas.value[index].estado = 'REPROGRAMADA'
      reservas.value[index].fecha_reserva = fechaNuevaReprogramacion.value
    }

    modalReprogramar.value = false
    toast.add({
      severity: 'success',
      summary: 'Éxito',
      detail: 'Reserva reprogramada correctamente',
      life: 3000
    })

  } catch (error) {
    console.error('Error al reprogramar reserva:', error)
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'No se pudo reprogramar la reserva',
      life: 3000
    })
  }
}

// Función para finalizar reserva (cambiar a completada)
const finalizarReserva = async (reserva) => {
  try {
    await axios.put(`/api/reservas/${reserva.id}/confirmar`)

    // Actualizar estado local a "Finalizada" o mantener "Confirmada"
    const index = reservas.value.findIndex(r => r.id === reserva.id)
    if (index !== -1) {
      reservas.value[index].estado = 'FINALIZADA'
    }

    toast.add({
      severity: 'success',
      summary: 'Éxito',
      detail: 'Reserva finalizada correctamente',
      life: 3000
    })

  } catch (error) {
    console.error('Error al finalizar reserva:', error)
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'No se pudo finalizar la reserva',
      life: 3000
    })
  }
}

// Función para obtener acciones disponibles según el estado
const getAccionesDisponibles = (reserva) => {
  switch (reserva.estado) {
    case 'PENDIENTE':
      return ['confirmar', 'rechazar', 'reprogramar']
    case 'CONFIRMADA':
      return ['rechazar', 'reprogramar', 'finalizar']
    case 'REPROGRAMADA':
      return ['confirmar', 'rechazar']
    case 'RECHAZADA':
      return [] // No se pueden hacer más acciones
    case 'FINALIZADA':
      return [] // No se pueden hacer más acciones en reservas finalizadas
    default:
      return []
  }
}

// Función para obtener severity del tag según el estado
const getSeverityByEstado = (estado) => {
  const estadoObj = estados.find(e => e.value === estado)
  return estadoObj?.severity || 'secondary'
}

// Función para formatear fecha
const formatearFecha = (fecha) => {
  if (!fecha) return 'N/A'
  return new Date(fecha).toLocaleDateString('es-ES')
}

// Función para limpiar filtros de fecha
const limpiarFechas = () => {
  filtros.value.fechaDesde = null
  filtros.value.fechaHasta = null
  cargarReservas()
}

// Watch para recargar cuando cambien los filtros
watch(() => filtros.value, () => {
  cargarReservas()
}, { deep: true })

// Cargar datos iniciales
onMounted(() => {
  cargarReservas()
})
</script>


<template>
  <Head title="Gestión de Reservas" />

  <AuthenticatedLayout>
    <Toast class="z-[9999]" />

    <div class="py-4 sm:py-6 px-4 sm:px-7 mt-6 sm:mt-10 mx-auto bg-red-50 shadow-md rounded-lg">
      <!-- Encabezado -->
      <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 gap-4">
        <h3 class="text-lg sm:text-2xl text-blue-600 font-bold">Gestión de Reservas</h3>
      </div>

      <!-- Filtros -->
      <div class="bg-white p-4 rounded-lg shadow border border-gray-200 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- Filtro por tipo -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Reserva</label>
            <Select
              v-model="filtros.tipo"
              :options="[
                { label: 'Tours', value: 'tours' },
                { label: 'Hoteles', value: 'hoteles' },
                { label: 'Aerolíneas', value: 'aerolineas' }
              ]"
              optionLabel="label"
              optionValue="value"
              class="w-full"
            />
          </div>

          <!-- Búsqueda -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
            <InputText
              v-model="filtros.busqueda"
              placeholder="Buscar por cliente, tour, hotel..."
              class="w-full"
            />
          </div>

          <!-- Fecha desde -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Desde</label>
            <DatePicker
              v-model="filtros.fechaDesde"
              dateFormat="dd/mm/yy"
              class="w-full"
              showIcon
              placeholder="Fecha inicio"
            />
          </div>

          <!-- Fecha hasta -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Hasta</label>
            <div class="flex gap-2">
              <DatePicker
                v-model="filtros.fechaHasta"
                dateFormat="dd/mm/yy"
                class="flex-1"
                showIcon
                placeholder="Fecha fin"
              />
              <button
                class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-md transition-colors"
                @click="limpiarFechas"
                title="Limpiar fechas"
              >
                <i class="pi pi-times"></i>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Pestañas por estado -->
      <Tabs v-model:value="pestanaActiva" class="bg-white rounded-lg shadow">
        <TabList>
          <Tab
            v-for="(estado, index) in estados"
            :key="estado.value"
            :value="index"
          >
            {{ estado.label }} ({{ reservas.filter(r => r.estado === estado.value).length }})
          </Tab>
        </TabList>

        <TabPanels>
          <TabPanel
            v-for="(estado, index) in estados"
            :key="estado.value"
            :value="index"
          >
            <!-- Tabla de reservas -->
            <DataTable
              :value="reservasPorEstado"
              :loading="loading"
              paginator
              :rows="10"
              :rowsPerPageOptions="[5, 10, 20]"
              paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
              currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} reservas"
              responsiveLayout="scroll"
              class="mt-4"
            >
              <template #empty>
                <div class="text-center py-8">
                  <i class="pi pi-inbox text-4xl text-gray-400 mb-4"></i>
                  <p class="text-gray-500">No hay reservas {{ estado.label.toLowerCase() }}</p>
                </div>
              </template>

              <template #loading>
                <div class="text-center py-8">
                  <i class="pi pi-spin pi-spinner text-2xl text-blue-500"></i>
                  <p class="text-gray-500 mt-2">Cargando reservas...</p>
                </div>
              </template>

              <!-- Columna Fecha -->
              <Column field="fecha_reserva" header="Fecha" sortable class="min-w-[100px]">
                <template #body="slotProps">
                  {{ formatearFecha(slotProps.data.fecha_reserva) }}
                </template>
            </Column>

            <!-- Columna Cliente -->
            <Column field="cliente.nombres" header="Cliente" sortable class="min-w-[150px]">
              <template #body="slotProps">
                <div>
                  <div class="font-medium">
                    {{ (slotProps.data.cliente?.user?.name) || (slotProps.data.cliente?.nombres) || 'N/A' }}
                  </div>
                  <div class="text-sm text-gray-500">
                    {{ (slotProps.data.cliente?.user?.email) || (slotProps.data.cliente?.correo) || 'N/A' }}
                  </div>
                </div>
              </template>
            </Column>

            <!-- Columna Entidad (Tour/Hotel/Aerolínea) -->
            <Column field="entidad_nombre" header="Servicio" sortable class="min-w-[200px]">
              <template #body="slotProps">
                <div>
                  <div class="font-medium">{{ slotProps.data.entidad_nombre || 'N/A' }}</div>
                  <div class="text-sm text-gray-500">{{ slotProps.data.tipo || 'N/A' }}</div>
                </div>
              </template>
            </Column>

            <!-- Columna Personas -->
            <Column header="Personas" class="min-w-[100px]">
              <template #body="slotProps">
                <div class="text-center">
                  <div class="text-sm">
                    <span class="font-medium">{{ slotProps.data.mayores_edad || 0 }}</span> adultos
                  </div>
                  <div class="text-sm text-gray-500">
                    <span class="font-medium">{{ slotProps.data.menores_edad || 0 }}</span> niños
                  </div>
                </div>
              </template>
            </Column>

            <!-- Columna Total -->
            <Column field="total" header="Total" sortable class="min-w-[100px]">
              <template #body="slotProps">
                <div class="text-right font-medium text-green-600">
                  ${{ Number(slotProps.data.total || 0).toFixed(2) }}
                </div>
              </template>
            </Column>

            <!-- Columna Estado -->
            <Column field="estado" header="Estado" class="min-w-[120px]">
              <template #body="slotProps">
                <Tag
                  :value="slotProps.data.estado"
                  :severity="getSeverityByEstado(slotProps.data.estado)"
                  class="text-sm"
                />
              </template>
            </Column>

            <!-- Columna Acciones -->
            <Column header="Acciones" class="min-w-[200px]">
              <template #body="slotProps">
                <div class="flex gap-1 flex-wrap">
                  <!-- Botón Confirmar -->
                  <button
                    v-if="getAccionesDisponibles(slotProps.data).includes('confirmar')"
                    class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm transition-colors"
                    @click="confirmarReserva(slotProps.data)"
                    title="Confirmar reserva"
                  >
                    <i class="pi pi-check mr-1"></i>
                    Confirmar
                  </button>

                  <!-- Botón Rechazar -->
                  <button
                    v-if="getAccionesDisponibles(slotProps.data).includes('rechazar')"
                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition-colors"
                    @click="abrirModalRechazar(slotProps.data)"
                    title="Rechazar reserva"
                  >
                    <i class="pi pi-times mr-1"></i>
                    Rechazar
                  </button>

                  <!-- Botón Reprogramar -->
                  <button
                    v-if="getAccionesDisponibles(slotProps.data).includes('reprogramar')"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition-colors"
                    @click="abrirModalReprogramar(slotProps.data)"
                    title="Reprogramar reserva"
                  >
                    <i class="pi pi-calendar mr-1"></i>
                    Reprogramar
                  </button>

                  <!-- Botón Finalizar -->
                  <button
                    v-if="getAccionesDisponibles(slotProps.data).includes('finalizar')"
                    class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-1 rounded text-sm transition-colors"
                    @click="finalizarReserva(slotProps.data)"
                    title="Finalizar reserva"
                  >
                    <i class="pi pi-check-circle mr-1"></i>
                    Finalizar
                  </button>
                </div>
              </template>
            </Column>
          </DataTable>
        </TabPanel>
      </TabPanels>
    </Tabs>

      <!-- Modal para rechazar reserva -->
      <Dialog
        v-model:visible="modalRechazar"
        modal
        header="Rechazar Reserva"
        :style="{ width: '500px' }"
      >
        <div class="space-y-4">
          <div v-if="reservaSeleccionada" class="bg-gray-50 p-4 rounded-lg">
            <h4 class="font-medium text-gray-800 mb-2">Detalles de la Reserva</h4>
            <p><strong>Cliente:</strong> {{ (reservaSeleccionada.cliente?.user?.name) || (reservaSeleccionada.cliente?.nombres) || 'N/A' }}</p>
            <p><strong>Servicio:</strong> {{ reservaSeleccionada.entidad_nombre }}</p>
            <p><strong>Fecha:</strong> {{ formatearFecha(reservaSeleccionada.fecha_reserva) }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Motivo del rechazo <span class="text-red-500">*</span>
            </label>
            <Textarea
              v-model="motivoRechazo"
              placeholder="Ingrese el motivo por el cual se rechaza esta reserva..."
              rows="3"
              class="w-full"
            />
          </div>
        </div>

        <template #footer>
          <div class="flex justify-end gap-2">
            <button
              class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition-colors"
              @click="modalRechazar = false"
            >
              Cancelar
            </button>
            <button
              class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition-colors"
              @click="rechazarReserva"
              :disabled="!motivoRechazo.trim()"
            >
              Rechazar Reserva
            </button>
          </div>
        </template>
      </Dialog>

      <!-- Modal para reprogramar reserva -->
      <Dialog
        v-model:visible="modalReprogramar"
        modal
        header="Reprogramar Reserva"
        :style="{ width: '500px' }"
      >
        <div class="space-y-4">
          <div v-if="reservaSeleccionada" class="bg-gray-50 p-4 rounded-lg">
            <h4 class="font-medium text-gray-800 mb-2">Detalles de la Reserva</h4>
            <p><strong>Cliente:</strong> {{ (reservaSeleccionada.cliente?.user?.name) || (reservaSeleccionada.cliente?.nombres) || 'N/A' }}</p>
            <p><strong>Servicio:</strong> {{ reservaSeleccionada.entidad_nombre }}</p>
            <p><strong>Fecha actual:</strong> {{ formatearFecha(reservaSeleccionada.fecha_reserva) }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Nueva fecha <span class="text-red-500">*</span>
            </label>
            <DatePicker
              v-model="fechaNuevaReprogramacion"
              dateFormat="dd/mm/yy"
              class="w-full"
              showIcon
              placeholder="Seleccione la nueva fecha"
              :minDate="new Date()"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Motivo de la reprogramación <span class="text-red-500">*</span>
            </label>
            <Textarea
              v-model="motivoReprogramacion"
              placeholder="Ingrese el motivo de la reprogramación..."
              rows="3"
              class="w-full"
            />
          </div>
        </div>

        <template #footer>
          <div class="flex justify-end gap-2">
            <button
              class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition-colors"
              @click="modalReprogramar = false"
            >
              Cancelar
            </button>
            <button
              class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition-colors"
              @click="reprogramarReserva"
              :disabled="!fechaNuevaReprogramacion || !motivoReprogramacion.trim()"
            >
              Reprogramar
            </button>
          </div>
        </template>
      </Dialog>
    </div>
  </AuthenticatedLayout>
</template>
