<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useToast } from 'primevue/usetoast'

// Reactive window size for responsive iframe
const windowWidth = ref(window.innerWidth)

// Computed property for responsive iframe height
const iframeHeight = computed(() => {
  if (windowWidth.value < 768) {
    return '400' // Mobile: 400px
  } else if (windowWidth.value < 1024) {
    return '500' // Tablet: 500px  
  } else {
    return '700' // Desktop: 700px
  }
})

// Update window width on resize
const updateWindowWidth = () => {
  windowWidth.value = window.innerWidth
}

onMounted(() => {
  window.addEventListener('resize', updateWindowWidth)
})

onUnmounted(() => {
  window.removeEventListener('resize', updateWindowWidth)
})

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

async function descargarPDF() {
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

  try {
    showToast('info', 'Generando informe...', '', 2000)
    const response = await fetch(`/descargar-informe?${params.toString()}`)
    const contentType = response.headers.get('content-type')
    if (contentType && contentType.includes('application/json')) {
      const errorData = await response.json()
      showToast('warn', 'Sin datos', errorData.message || 'No se encontraron datos para el periodo seleccionado', 5000)
      return
    }
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }
    // Detectar si es móvil o tablet
    const isMobileOrTablet = window.innerWidth < 1024
    if (isMobileOrTablet) {
      // Descargar el PDF directamente y usar el nombre del backend
      const blob = await response.blob()
      let filename = 'informe.pdf'
      const disposition = response.headers.get('Content-Disposition') || response.headers.get('content-disposition')
      if (disposition) {
        const match = disposition.match(/filename="?([^";]+)"?/i)
        if (match && match[1]) {
          filename = match[1]
        }
      }
      const url = window.URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = filename
      document.body.appendChild(a)
      a.click()
      a.remove()
      window.URL.revokeObjectURL(url)
      showToast('success', 'Descarga iniciada', `El informe PDF (${filename}) se está descargando.`, 2500)
    } else {
      // Abrir en nueva ventana (solo escritorio) y permitir vista previa
      window.open(`/descargar-informe?${params.toString()}&preview=1`, '_blank')
      showToast('success', 'Informe abierto', 'El informe se abrió en una nueva ventana.', 2500)
    }
  } catch (error) {
    console.error('Error al abrir el informe:', error)
    showToast('error', 'Error', 'Error al abrir el informe. Por favor, inténtelo de nuevo.', 5000)
  }
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
    <div class="min-h-[70vh] flex flex-col items-center justify-center py-4 px-2 sm:px-4 md:px-8 bg-gray-50">
      <!-- Formulario responsive -->
      <div class="bg-white rounded-xl shadow-2xl border border-blue-100 px-3 py-4 sm:px-8 sm:py-10 w-full max-w-md sm:max-w-lg md:max-w-xl lg:max-w-2xl flex flex-col items-center gap-3">
        <div class="flex items-center gap-2 sm:gap-3 mb-3">
          <svg class="w-7 h-7 sm:w-8 sm:h-8 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a4 4 0 014-4h6M9 17v2a4 4 0 004 4h6M9 17H5a2 2 0 01-2-2V7a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-4"></path>
          </svg>
          <h1 class="text-lg sm:text-xl md:text-2xl font-extrabold text-gray-800 tracking-tight text-center">Informe de Cupos Vendidos Mensuales por Tour</h1>
        </div>
        <p class="text-gray-600 mb-3 sm:mb-5 text-center text-sm sm:text-base">
          Seleccione el rango de meses o un solo mes para generar el informe PDF.
        </p>
        <!-- Radios para selección de tipo de informe en una sola fila -->
        <div class="w-full flex flex-row items-center justify-between mb-2 gap-2">
          <label class="flex items-center gap-2 flex-1">
            <input
              id="radio-mes-unico"
              type="radio"
              v-model="modoSeleccion"
              value="unico"
              class="accent-blue-600"
            />
            <span class="font-semibold text-gray-700 cursor-pointer select-none text-sm">Por mes</span>
          </label>
          <label class="flex items-center gap-2 flex-1">
            <input
              id="radio-activar-fechas"
              type="radio"
              v-model="modoSeleccion"
              value="rango"
              class="accent-blue-600"
            />
            <span class="font-semibold text-gray-700 cursor-pointer select-none text-sm">Varios meses</span>
          </label>
          <button
            @click="limpiarFechas"
            class="px-3 py-1 bg-gray-200 text-gray-700 rounded-lg shadow hover:bg-gray-300 transition font-semibold text-xs border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-200"
          >
            <span class="block sm:hidden">Limpiar</span>
            <span class="hidden sm:block">Limpiar fechas</span>
          </button>
        </div>
        <!-- Calendario para seleccionar solo un mes -->
        <div class="w-full mb-3 sm:mb-5">
          <label for="mes-unico-picker" class="block mb-2 font-semibold text-gray-700">Mes único:</label>
          <DatePicker
            id="mes-unico-picker"
            v-model="mesUnico"
            view="month"
            dateFormat="MM yy"
            showIcon
            class="w-full text-xs sm:text-sm"
            placeholder="Seleccione un mes"
            :manualInput="false"
            :maxDate="today"
            :minDate="minMesUnico"
            :disabled="modoSeleccion !== 'unico'"
          />
        </div>
        <div class="w-full mb-2 flex flex-col sm:flex-row gap-2 sm:gap-4">
          <div class="flex-1">
            <label for="desde-picker" class="block mb-2 font-semibold text-gray-700">Desde:</label>
            <DatePicker
              id="desde-picker"
              v-model="desde"
              view="month"
              dateFormat="MM yy"
              showIcon
              class="w-full text-xs sm:text-sm"
              placeholder="Seleccione mes"
              :manualInput="false"
              :maxDate="today"
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
              class="w-full text-xs sm:text-sm"
              placeholder="Seleccione mes"
              :manualInput="false"
              :maxDate="today"
              :minDate="desde"
              :disabled="modoSeleccion !== 'rango'"
            />
          </div>
        </div>
        <!-- Botones en línea centrados -->
        <div class="w-full flex flex-col sm:flex-row gap-2 sm:gap-3 mb-2 justify-center items-center">
          <button
            @click="descargarPDF"
            class="bg-gradient-to-r from-red-800 to-red-500 flex items-center gap-2 px-5 py-2 text-white rounded-lg shadow-lg hover:from-red-900 hover:to-red-600 transition font-semibold text-xs sm:text-base border border-red-800 focus:outline-none focus:ring-2 focus:ring-red-400"
            :disabled="!puedeGenerar"
            :class="{ 'opacity-60 cursor-not-allowed': !puedeGenerar }"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
            </svg>
            Descargar PDF
          </button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>