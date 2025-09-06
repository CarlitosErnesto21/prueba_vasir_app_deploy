<script setup>
import { ref, computed, watch } from 'vue'

// Props del componente
const props = defineProps({
  visible: {
    type: Boolean,
    default: false
  },
  tourSeleccionado: {
    type: Object,
    default: null
  },
  user: {
    type: Object,
    default: null
  }
})

// Emits para comunicación con el componente padre
const emit = defineEmits(['update:visible', 'confirmar-reserva'])

// Estado local del formulario
const reservaForm = ref({
  correo: '',
  nombres: '',
  tipo_documento: 'DUI',
  numero_identificacion: '',
  fecha_nacimiento: '',
  genero: 'Masculino',
  direccion: '',
  telefono: '',
  cupos_adultos: 1,
  cupos_menores: 0,
})

// Computed para el total de cupos
const cupos_total = computed(() => {
  const adultos = Number(reservaForm.value.cupos_adultos) || 0
  const menores = Number(reservaForm.value.cupos_menores) || 0
  return adultos + menores
})

// Función para formatear fechas
const formatearFecha = (fecha) => {
  if (!fecha) return ''
  const date = new Date(fecha)
  return date.toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Función para resetear el formulario
const resetFormularioReserva = () => {
  const loggedInUser = props.user
  let nombreCompleto = ''
  let correo = ''

  if (loggedInUser) {
    correo = loggedInUser.email || ''
    nombreCompleto = loggedInUser.name || ''
  }

  reservaForm.value = {
    correo: correo,
    nombres: nombreCompleto,
    tipo_documento: 'DUI',
    numero_identificacion: '',
    fecha_nacimiento: '',
    genero: 'Masculino',
    direccion: '',
    telefono: '',
    cupos_adultos: 1,
    cupos_menores: 0,
  }
}

// Función para cerrar el modal
const cerrarModal = () => {
  emit('update:visible', false)
}

// Función para confirmar la reserva
const confirmarReserva = () => {
  if (!props.tourSeleccionado) return

  const reserva = {
    tour: props.tourSeleccionado,
    cliente: reservaForm.value,
    cupos: {
      adultos: reservaForm.value.cupos_adultos,
      menores: reservaForm.value.cupos_menores,
      total: cupos_total.value
    }
  }

  emit('confirmar-reserva', reserva)
}

// Watch para resetear el formulario cuando se abre el modal
watch(() => props.visible, (newValue) => {
  if (newValue) {
    resetFormularioReserva()
  }
})
</script>

<template>
  <Dialog 
    :visible="visible" 
    @update:visible="emit('update:visible', $event)"
    modal 
    :closable="true" 
    class="max-w-3xl w-full mx-4" 
    :draggable="false"
  >
    <template #header>
      <h3 class="text-lg font-bold text-red-700">🧾 Reservando Tour</h3>
    </template>

    <div v-if="tourSeleccionado" class="space-y-6 text-sm text-gray-700">
      <!-- Resumen del tour -->
      <div>
        <h4 class="font-semibold text-gray-800 mb-2">Resumen del tour</h4>
        <div class="overflow-x-auto">
          <table class="w-full text-left border border-gray-200 text-xs sm:text-sm">
            <thead class="bg-gray-100">
              <tr>
                <th class="p-2 border text-center">Nombre</th>
                <th class="p-2 border text-center">Incluye</th>
                <th class="p-2 border text-center">No incluye</th>
                <th class="p-2 border text-center">Punto de salida</th>
                <th class="p-2 border text-center">Fecha de salida</th>
                <th class="p-2 border text-center">Fecha regreso</th>
                <th class="p-2 border text-center">Precio</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="p-2 border">{{ tourSeleccionado.nombre }}</td>
                <td class="p-2 border">{{ tourSeleccionado.incluye || '---' }}</td>
                <td class="p-2 border">{{ tourSeleccionado.no_incluye || '---' }}</td>
                <td class="p-2 border">{{ tourSeleccionado.punto_salida }}</td>
                <td class="p-2 border">{{ formatearFecha(tourSeleccionado.fecha_salida) }}</td>
                <td class="p-2 border">{{ formatearFecha(tourSeleccionado.fecha_regreso) }}</td>
                <td class="p-2 border font-bold text-green-600">${{ tourSeleccionado.precio }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Datos personales -->
      <div>
        <h4 class="font-semibold text-gray-800 mb-2">Datos personales</h4>
        <form class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold mb-1">Correo electrónico</label>
            <input 
              v-model="reservaForm.correo" 
              type="email" 
              class="w-full border rounded-lg px-2 py-1" 
              :class="{ 'bg-gray-100 cursor-not-allowed': !!user }" 
              placeholder="ejemplo@email.com" 
              :readonly="!!user" 
            />
          </div>
          <div>
            <label class="block text-xs font-semibold mb-1">Nombre Completo</label>
            <input 
              v-model="reservaForm.nombres" 
              type="text" 
              class="w-full border rounded-lg px-2 py-1" 
              :class="{ 'bg-gray-100 cursor-not-allowed': !!user }" 
              placeholder="Nombres" 
              :readonly="!!user" 
            />
          </div>
          <div>
            <label class="block text-xs font-semibold mb-1">Tipo documento</label>
            <select v-model="reservaForm.tipo_documento" class="w-full border rounded-lg px-2 py-1">
              <option>DUI</option>
              <option>CÉDULA</option>
              <option>PASAPORTE</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold mb-1">Número de identificación</label>
            <input 
              v-model="reservaForm.numero_identificacion" 
              type="text" 
              class="w-full border rounded-lg px-2 py-1" 
            />
          </div>
          <div>
            <label class="block text-xs font-semibold mb-1">Fecha de nacimiento</label>
            <DatePicker 
              v-model="reservaForm.fecha_nacimiento" 
              dateFormat="dd/mm/yy" 
              class="w-full" 
              inputClass="w-full border rounded-lg px-2 py-1" 
              placeholder="dd/mm/aaaa" 
              :maxDate="new Date()" 
              showIcon 
            />
          </div>
          <div>
            <label class="block text-xs font-semibold mb-1">Género</label>
            <select v-model="reservaForm.genero" class="w-full border rounded-lg px-2 py-1">
              <option>Masculino</option>
              <option>Femenino</option>
            </select>
          </div>
          <div class="md:col-span-2">
            <label class="block text-xs font-semibold mb-1">Dirección de residencia</label>
            <textarea 
              v-model="reservaForm.direccion" 
              class="w-full border rounded-lg px-2 py-1"
            ></textarea>
          </div>
          <div>
            <label class="block text-xs font-semibold mb-1">Teléfono</label>
            <input 
              v-model="reservaForm.telefono" 
              type="text" 
              class="w-full border rounded-lg px-2 py-1" 
            />
          </div>
        </form>
      </div>

      <!-- Cupos -->
      <div>
        <h4 class="font-semibold text-gray-800 mb-2">Cupos a reservar (Total: {{ cupos_total }})</h4>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-xs font-semibold mb-1">Mayores de edad</label>
            <input 
              v-model.number="reservaForm.cupos_adultos" 
              type="number" 
              min="1" 
              class="w-full border rounded-lg px-2 py-1" 
            />
          </div>
          <div>
            <label class="block text-xs font-semibold mb-1">Menores de edad</label>
            <input 
              v-model.number="reservaForm.cupos_menores" 
              type="number" 
              min="0" 
              class="w-full border rounded-lg px-2 py-1" 
            />
            <p class="text-xs text-red-600 mt-1">* Presentar permiso firmado de padre/madre</p>
          </div>
        </div>
      </div>
    </div>

    <template #footer>
      <div class="flex justify-end gap-2">
        <Button 
          label="Cancelar" 
          @click="cerrarModal" 
          class="p-button-text" 
        />
        <Button 
          label="Confirmar Reserva" 
          icon="pi pi-check" 
          class="!bg-red-600 !border-none" 
          @click="confirmarReserva" 
        />
      </div>
    </template>
  </Dialog>
</template>
