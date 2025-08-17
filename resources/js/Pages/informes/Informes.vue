<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { useToast } from 'primevue/usetoast'
import Toast from 'primevue/toast'
import DatePicker from 'primevue/datepicker'

// Locale español para PrimeVue DatePicker
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
  let fecha = new Date(2022, 0, 1)
  const hoy = new Date()
  while (fecha <= hoy) {
    const value = `${fecha.getFullYear()}-${(fecha.getMonth() + 1).toString().padStart(2, '0')}`
    const label = `${nombres[fecha.getMonth()]} ${fecha.getFullYear()}`
    meses.push({ value, label })
    fecha.setMonth(fecha.getMonth() + 1)
  }
  return meses
}

const mesesDisponibles = generarMeses()
const desde = ref(null)
const hasta = ref(null)
const today = new Date()
const modoSeleccion = ref('unico')
const mesUnico = ref(null)
const pdfUrl = ref(null)
const toast = useToast()

// Fecha mínima para el DatePicker de mes único (marzo 2019)
const minMesUnico = new Date(2019, 2, 1) // Mes 2 = marzo (0-indexed)

const formatMonth = date =>
  date ? `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}` : ''

const mesesFiltrados = computed(() => {
  if (!desde.value || !hasta.value) return []
  const desdeStr = formatMonth(desde.value)
  const hastaStr = formatMonth(hasta.value)
  const desdeIdx = mesesDisponibles.findIndex(m => m.value === desdeStr)
  const hastaIdx = mesesDisponibles.findIndex(m => m.value === hastaStr)
  if (desdeIdx === -1 || hastaIdx === -1 || desdeIdx > hastaIdx) return []
  return mesesDisponibles.slice(desdeIdx, hastaIdx + 1).map(m => m.value)
})

const puedeGenerar = computed(() =>
  modoSeleccion.value === 'unico' ? !!mesUnico.value : !!(desde.value && hasta.value && mesesFiltrados.value.length)
)

function showToast(type, summary, detail, life = 3500) {
  toast.add({ severity: type, summary, detail, life })
}

function descargarPDF() {
  let params = new URLSearchParams()
  let meses = []
  if (modoSeleccion.value === 'unico') {
    if (!mesUnico.value) {
      showToast('error', 'Error', 'Seleccione un mes para generar el informe.')
      return
    }
    meses = [formatMonth(mesUnico.value)]
  } else {
    if (!puedeGenerar.value) {
      showToast('error', 'Error', 'Seleccione un rango válido de meses para generar el informe.')
      return
    }
    meses = mesesFiltrados.value
  }
  meses.forEach(mes => params.append('meses[]', mes))
  showToast('info', 'Generando informe', '', 2000)
  pdfUrl.value = `/descargar-informe?${params.toString()}`
  setTimeout(() => {
    showToast('success', 'Vista previa lista', '', 2500)
  }, 1200)
}

function limpiarFechas() {
  mesUnico.value = null
  desde.value = null
  hasta.value = null
  pdfUrl.value = null
}
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
        <!-- Radio para activar el calendario de mes único y botón limpiar fechas en la misma fila -->
        <div class="w-full flex items-center mb-3 gap-3">
          <input
            id="radio-mes-unico"
            type="radio"
            v-model="modoSeleccion"
            value="unico"
            class="mr-2 accent-blue-600"
          />
          <label for="radio-mes-unico" class="font-semibold text-gray-700 cursor-pointer select-none mr-2">
            Seleccionar un solo mes
          </label>
          <button
            @click="limpiarFechas"
            class="px-4 py-1 bg-gray-200 text-gray-700 rounded-lg shadow hover:bg-gray-300 transition font-semibold text-sm border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-200"
          >
            Limpiar fechas
          </button>
        </div>
        <!-- Calendario para seleccionar solo un mes -->
        <div class="w-full mb-5">
          <label for="mes-unico-picker" class="block mb-2 font-semibold text-gray-700">Mes único:</label>
          <DatePicker
            id="mes-unico-picker"
            v-model="mesUnico"
            view="month"
            dateFormat="MM yy"
            showIcon
            class="w-full"
            placeholder="Seleccione un mes"
            :manualInput="false"
            :maxDate="today"
            :minDate="minMesUnico"
            :locale="esLocale"
            :disabled="modoSeleccion !== 'unico'"
          />
        </div>
        <div class="w-full flex items-center mb-5 gap-3">
          <input
            id="radio-activar-fechas"
            type="radio"
            v-model="modoSeleccion"
            value="rango"
            class="mr-2 accent-blue-600"
          />
          <label for="radio-activar-fechas" class="font-semibold text-gray-700 cursor-pointer select-none mr-2">
            Activar selección de meses
          </label>
        </div>
        <div class="w-full mb-4 flex flex-col sm:flex-row gap-4">
          <div class="flex-1">
            <label for="desde-picker" class="block mb-2 font-semibold text-gray-700">Desde:</label>
            <DatePicker
              id="desde-picker"
              v-model="desde"
              view="month"
              dateFormat="MM yy"
              showIcon
              class="w-full"
              placeholder="Seleccione mes"
              :manualInput="false"
              :maxDate="today"
              :locale="esLocale"
              :disabled="modoSeleccion !== 'rango'"
            />
          </div>
          <div class="flex-1">
            <label for="hasta-picker" class="block mb-2 font-semibold text-gray-700">Hasta:</label>
            <DatePicker
              id="hasta-picker"
              v-model="hasta"
              view="month"
              dateFormat="MM yy"
              showIcon
              class="w-full"
              placeholder="Seleccione mes"
              :manualInput="false"
              :maxDate="today"
              :minDate="desde"
              :locale="esLocale"
              :disabled="modoSeleccion !== 'rango'"
            />
          </div>
        </div>
        <!-- Botones en línea centrados -->
        <div class="w-full flex flex-row gap-3 mb-4 justify-center">
          <button
            @click="descargarPDF"
            class="bg-gradient-to-r from-red-800 to-red-500 flex items-center gap-2 px-7 py-2 text-white rounded-lg shadow-lg hover:from-red-900 hover:to-red-600 transition font-semibold text-base border border-red-800 focus:outline-none focus:ring-2 focus:ring-red-400"
            :disabled="!puedeGenerar"
            :class="{ 'opacity-60 cursor-not-allowed': !puedeGenerar }"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
            </svg>
            Vista previa PDF
          </button>
        </div>
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