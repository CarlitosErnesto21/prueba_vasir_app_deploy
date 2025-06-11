<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { useToast } from 'primevue/usetoast'
import Toast from 'primevue/toast'
import DatePicker from 'primevue/datepicker'

// Configuración de locale español para PrimeVue DatePicker
const esLocale = {
  firstDayOfWeek: 1,
  dayNames: ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'],
  dayNamesShort: ['dom', 'lun', 'mar', 'mié', 'jue', 'vie', 'sáb'],
  dayNamesMin: ['D', 'L', 'M', 'X', 'J', 'V', 'S'],
  monthNames: [
    'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
    'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
  ],
  monthNamesShort: [
    'ene', 'feb', 'mar', 'abr', 'may', 'jun',
    'jul', 'ago', 'sep', 'oct', 'nov', 'dic'
  ],
  today: 'Hoy',
  clear: 'Limpiar'
}

function generarMeses() {
  const meses = []
  const nombres = [
    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
  ]
  const fechaInicio = new Date(2022, 0, 1)
  const hoy = new Date()
  let anio = fechaInicio.getFullYear()
  let mes = fechaInicio.getMonth()
  while (anio < hoy.getFullYear() || (anio === hoy.getFullYear() && mes <= hoy.getMonth())) {
    const value = `${anio}-${(mes + 1).toString().padStart(2, '0')}`
    const label = `${nombres[mes]} ${anio}`
    meses.push({ value, label })
    mes++
    if (mes > 11) {
      mes = 0
      anio++
    }
  }
  return meses
}

const mesesDisponibles = generarMeses()

const desde = ref(null)
const hasta = ref(null)
const today = new Date()

function formatMonth(date) {
  if (!date) return ''
  const year = date.getFullYear()
  const month = (date.getMonth() + 1).toString().padStart(2, '0')
  return `${year}-${month}`
}

const mesesFiltrados = computed(() => {
  if (!desde.value || !hasta.value) return []
  const desdeStr = formatMonth(desde.value)
  const hastaStr = formatMonth(hasta.value)
  const desdeIdx = mesesDisponibles.findIndex(m => m.value === desdeStr)
  const hastaIdx = mesesDisponibles.findIndex(m => m.value === hastaStr)
  if (desdeIdx === -1 || hastaIdx === -1 || desdeIdx > hastaIdx) return []
  return mesesDisponibles.slice(desdeIdx, hastaIdx + 1).map(m => m.value)
})

const pdfUrl = ref(null)

function descargarPDF() {
  if (!desde.value || !hasta.value) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Seleccione un rango válido de meses para generar el informe.', life: 3500 })
    return
  }
  const desdeStr = formatMonth(desde.value)
  const hastaStr = formatMonth(hasta.value)
  const desdeIdx = mesesDisponibles.findIndex(m => m.value === desdeStr)
  const hastaIdx = mesesDisponibles.findIndex(m => m.value === hastaStr)
  if (desdeIdx === -1 || hastaIdx === -1 || desdeIdx > hastaIdx) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Seleccione un rango válido de meses para generar el informe.', life: 3500 })
    return
  }
  toast.add({ severity: 'info', summary: 'Generando informe', detail: 'Por favor espere mientras se genera el PDF.', life: 2000 })
  const params = new URLSearchParams()
  mesesFiltrados.value.forEach(mes => params.append('meses[]', mes))
  // Mostrar el PDF en el iframe
  pdfUrl.value = `/descargar-informe?${params.toString()}`
  // Alerta cuando el PDF ya está listo
  setTimeout(() => {
    toast.add({ severity: 'success', summary: 'Vista previa lista', detail: 'El informe PDF se ha generado y está listo para visualizar.', life: 2500 })
  }, 1200)
}

const toast = useToast()
</script>

<template>
  <Head title="Informe de Tours" />
  <AuthenticatedLayout>
    <Toast />
    <div class="min-h-[70vh] flex flex-col lg:flex-row items-start justify-center py-10 bg-gray-50 gap-8">
      <!-- Formulario alineado a la izquierda -->
      <div class="bg-white rounded-xl shadow-2xl border border-blue-100 px-8 py-10 max-w-lg w-full flex flex-col items-center sticky top-8 self-start">
        <img
          src="../../../../imagenes/logo.png"
          alt="Logo VASIR"
          class="w-32 h-auto mb-6 drop-shadow-lg"
          loading="lazy"
        />
        <div class="flex items-center gap-3 mb-3">
          <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a4 4 0 014-4h6M9 17v2a4 4 0 004 4h6M9 17H5a2 2 0 01-2-2V7a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-4"></path>
          </svg>
          <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">Informe de Cupos Vendidos Mensuales por Tour</h1>
        </div>
        <p class="text-gray-600 mb-5 text-center text-base">
          Seleccione el rango de meses o un solo mes para generar el informe PDF.
        </p>
        <div class="w-full mb-4 flex flex-col sm:flex-row gap-4">
          <div class="flex-1">
            <label class="block mb-2 font-semibold text-gray-700">Desde:</label>
            <DatePicker
              v-model="desde"
              view="month"
              dateFormat="MM yy"
              showIcon
              class="w-full"
              placeholder="Seleccione mes"
              :manualInput="false"
              :maxDate="today"
              :locale="esLocale"
            />
          </div>
          <div class="flex-1">
            <label class="block mb-2 font-semibold text-gray-700">Hasta:</label>
            <DatePicker
              v-model="hasta"
              view="month"
              dateFormat="MM yy"
              showIcon
              class="w-full"
              placeholder="Seleccione mes"
              :manualInput="false"
              :maxDate="today"
              :locale="esLocale"
            />
          </div>
        </div>
        <button
          @click="descargarPDF"
          class="flex items-center gap-2 px-7 py-2 bg-gradient-to-r from-red-800 to-red-500 text-white rounded-lg shadow-lg hover:from-red-900 hover:to-red-600 transition font-semibold text-base border border-red-800 focus:outline-none focus:ring-2 focus:ring-red-400"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
          </svg>
          Vista previa PDF
        </button>
        <div v-if="pdfUrl" class="w-full mt-4 flex flex-col items-center lg:hidden">
          <iframe
            :src="pdfUrl"
            width="100%"
            height="500"
            class="border rounded shadow"
            style="min-height:350px; max-width:800px;"
          ></iframe>
        </div>
      </div>
      <!-- Vista previa PDF lado derecho en escritorio -->
      <div class="flex-1 flex flex-col items-center min-w-[400px] max-w-[900px] w-full">
        <div v-if="pdfUrl" class="w-full flex flex-col items-center">
          <iframe
            :src="pdfUrl"
            width="100%"
            height="700"
            class="border rounded shadow"
            style="min-height:500px; max-width:900px;"
          ></iframe>
        </div>
        <div v-else class="w-full h-[700px] flex items-center justify-center border-2 border-dashed border-gray-300 rounded-lg bg-white">
          <div class="text-center px-6">
            <svg class="mx-auto mb-4 w-16 h-16 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
            </svg>
            <div class="text-lg font-semibold text-gray-500 mb-2">Aquí se mostrará la vista previa del PDF</div>
            <div class="text-gray-400 text-sm">Genera un informe para ver la vista previa aquí.</div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>