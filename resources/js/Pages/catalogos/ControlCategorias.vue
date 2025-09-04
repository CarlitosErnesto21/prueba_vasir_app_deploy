<script setup>
import { ref, computed, watch, onMounted, nextTick } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useToast } from 'primevue/usetoast'
import { FilterMatchMode } from "@primevue/core/api"
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faCheck, faExclamationTriangle, faFilter, faPencil, faPlus, faSignOut, faTrashCan, faXmark, faTags, faArrowLeft } from '@fortawesome/free-solid-svg-icons'
import axios from 'axios'

const toast = useToast()

// 📊 Estados reactivos
const categorias = ref([])
const categoria = ref({
    id: null,
    nombre: "",
    tipo: null
})

// 📝 Modal states
const dialog = ref(false)
const deleteDialog = ref(false)
const unsavedChangesDialog = ref(false)
const submitted = ref(false)
const hasUnsavedChanges = ref(false)
const originalCategoriaData = ref(null)

// 🔍 Filtros
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    tipo: { value: null, matchMode: FilterMatchMode.EQUALS }
})

const tipoEstadoSeleccionado = ref('productos')
const selectedTipo = ref(null)

// 📄 Paginación
const rowsPerPage = ref(10)
const rowsPerPageOptions = ref([5, 10, 20, 50])
const btnTitle = ref("Guardar")

// 📂 Datos de apoyo
const tiposCategoria = [
    { label: 'Productos', value: 'productos' }, 
    { label: 'Hoteles', value: 'hoteles' }
]

const tiposEstado = [
    { label: 'Productos', value: 'productos' },
    { label: 'Hoteles', value: 'hoteles' }
]

// 🔍 Categorías filtradas
const categoriasFiltradas = computed(() => {
    let filtered = categorias.value.filter(c => c.tipo === tipoEstadoSeleccionado.value)
    
    // Filtro por búsqueda global
    if (filters.value.global.value) {
        const searchTerm = filters.value.global.value.toLowerCase()
        filtered = filtered.filter(categoria => 
            categoria.nombre.toLowerCase().includes(searchTerm)
        )
    }
    
    return filtered
})

// 👀 Watcher para detectar cambios en el modal
watch([categoria], () => {
    if (originalCategoriaData.value && dialog.value) {
        nextTick(() => {
            const current = { ...categoria.value }
            
            const hasChanges = JSON.stringify(current) !== JSON.stringify(originalCategoriaData.value)
            
            const isCreatingNew = !originalCategoriaData.value.id
            const hasAnyData = categoria.value.nombre || categoria.value.tipo
            
            hasUnsavedChanges.value = hasChanges || (isCreatingNew && hasAnyData)
        })
    }
}, { deep: true, flush: 'post' })

// 🔄 Función para resetear formulario
function resetForm() {
    categoria.value = {
        id: null,
        nombre: "",
        tipo: null
    }
    submitted.value = false
}

// 📊 Cargar datos
onMounted(() => {
    cargarCategoriasPorTipo(tipoEstadoSeleccionado.value)
})

// Recargar cuando cambia el tipo seleccionado
watch(tipoEstadoSeleccionado, (nuevoTipo) => {
    cargarCategoriasPorTipo(nuevoTipo)
})

const cargarCategoriasPorTipo = async (tipo) => {
    try {
        const ruta = tipo === 'hoteles' ? 'categorias-hoteles' : 'categorias-productos'
        const response = await axios.get(`/api/${ruta}`)
        categorias.value = (response.data.data || response.data || []).map(cat => ({
            ...cat,
            tipo: tipo,
            categoria_id: cat.id
        })).sort((a, b) => {
            const dateA = new Date(a.created_at)
            const dateB = new Date(b.created_at)
            return dateB - dateA
        })
    } catch (error) {
        toast.add({ 
            severity: 'error', 
            summary: 'Error', 
            detail: `No se pudieron cargar las categorías de ${tipo}.`, 
            life: 4000 
        })
    }
}

// 🔍 Funciones para manejar filtros
const clearFilters = () => {
    selectedTipo.value = null
    filters.value.global.value = null
    filters.value.tipo.value = null
    toast.add({ 
        severity: "info", 
        summary: "Filtros limpiados", 
        detail: "Se han removido todos los filtros aplicados.", 
        life: 3000 
    })
}

// 📝 CRUD Operations
const openNew = () => {
    resetForm()
    categoria.value.tipo = tipoEstadoSeleccionado.value
    btnTitle.value = "Guardar"
    submitted.value = false
    dialog.value = true
    nextTick(() => {
        originalCategoriaData.value = { ...categoria.value }
        hasUnsavedChanges.value = false
    })
}

const editCategoria = (c) => {
    resetForm()
    submitted.value = false
    categoria.value = { ...c }
    hasUnsavedChanges.value = false
    btnTitle.value = "Actualizar"
    dialog.value = true
    nextTick(() => {
        originalCategoriaData.value = { ...categoria.value }
        hasUnsavedChanges.value = false
    })
}

const saveOrUpdate = async () => {
    submitted.value = true

    // ✅ CORREGIDO: Validar según TU controlador (min:3|max:50)
    if (!categoria.value.nombre || categoria.value.nombre.length < 3 || categoria.value.nombre.length > 50) {
        toast.add({ 
            severity: "warn", 
            summary: "Campos requeridos", 
            detail: "El nombre debe tener entre 3 y 50 caracteres.", 
            life: 4000 
        })
        return
    }

    // Validar tipo
    if (!categoria.value.tipo) {
        toast.add({ 
            severity: "warn", 
            summary: "Campos requeridos", 
            detail: "Por favor selecciona el tipo de categoría.", 
            life: 4000 
        })
        return
    }

    try {
        const tipo = categoria.value.tipo
        const ruta = tipo === 'hoteles' ? 'categorias-hoteles' : 'categorias-productos'
        
        let response
        if (!categoria.value.id) {
            response = await axios.post(`/api/${ruta}`, { 
                nombre: categoria.value.nombre 
            })
            toast.add({ 
                severity: "success", 
                summary: "¡Éxito!", 
                detail: "La categoría ha sido creada correctamente.", 
                life: 5000 
            })
        } else {
            response = await axios({
                method: 'POST',
                url: `/api/${ruta}/${categoria.value.id}`,
                data: {
                    _method: 'PUT',
                    nombre: categoria.value.nombre
                }
            })
            toast.add({ 
                severity: "success", 
                summary: "¡Éxito!", 
                detail: "La categoría ha sido actualizada correctamente.", 
                life: 5000 
            })
        }

        await cargarCategoriasPorTipo(tipoEstadoSeleccionado.value)
        dialog.value = false
        hasUnsavedChanges.value = false
        originalCategoriaData.value = null
        resetForm()
    } catch (err) {
        // 🚀 MEJORADO: Manejo específico de errores de validación
        if (err.response?.status === 422) {
            const errors = err.response.data.errors
            if (errors && errors.nombre) {
                toast.add({ 
                    severity: "warn", 
                    summary: "Error de validación", 
                    detail: errors.nombre[0], 
                    life: 6000 
                })
            } else {
                const mensaje = err.response.data.message || 'Error de validación.'
                toast.add({ 
                    severity: "warn", 
                    summary: "Error de validación", 
                    detail: mensaje, 
                    life: 6000 
                })
            }
        } else {
            const mensaje = err.response?.data?.message || 'Error al procesar la categoría.'
            toast.add({ 
                severity: "error", 
                summary: "Error", 
                detail: mensaje, 
                life: 6000 
            })
        }
    }
}

const confirmDeleteCategoria = (c) => { 
    categoria.value = { ...c }
    deleteDialog.value = true
}

const deleteCategoria = async () => {
    try {
        const tipo = categoria.value.tipo
        const ruta = tipo === 'hoteles' ? 'categorias-hoteles' : 'categorias-productos'
        
        const response = await axios({
            method: 'POST',
            url: `/api/${ruta}/${categoria.value.id}`,
            data: {
                _method: 'DELETE'
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })

        await cargarCategoriasPorTipo(tipoEstadoSeleccionado.value)
        deleteDialog.value = false
        toast.add({ 
            severity: "success", 
            summary: "¡Eliminada!", 
            detail: "La categoría ha sido eliminada correctamente.", 
            life: 5000 
        })
    } catch (err) {
        deleteDialog.value = false
        
        // 🎯 Manejo específico de errores 422 - Restricciones de integridad
        if (err.response?.status === 422) {
            const errorData = err.response.data
            let mensaje = errorData.error || "La categoría está siendo utilizada en el sistema."
            
            // Si hay detalles específicos, mostrarlos en formato legible
            if (errorData.details && Array.isArray(errorData.details)) {
                mensaje += "\n\n📋 Detalles:\n• " + errorData.details.join("\n• ")
            }
            
            toast.add({ 
                severity: "warn", 
                summary: "❌ No se puede eliminar", 
                detail: mensaje,
                life: 10000 
            })
        } 
        // 🎯 Manejo de errores 404 - Categoría no encontrada
        else if (err.response?.status === 404) {
            toast.add({ 
                severity: "error", 
                summary: "Categoría no encontrada", 
                detail: "La categoría que intentas eliminar no existe o ya fue eliminada.", 
                life: 5000 
            })
            // Recargar la lista para reflejar el estado actual
            await cargarCategoriasPorTipo(tipoEstadoSeleccionado.value)
        } 
        // 🎯 Manejo de errores 500 - Error del servidor
        else if (err.response?.status === 500) {
            toast.add({ 
                severity: "error", 
                summary: "Error del servidor", 
                detail: "Ocurrió un error interno. Por favor, contacta al administrador.", 
                life: 6000 
            })
        }
        // 🎯 Otros errores
        else {
            const errorMsg = err.response?.data?.error || err.message || "Error desconocido"
            toast.add({ 
                severity: "error", 
                summary: "Error", 
                detail: `No se pudo eliminar la categoría: ${errorMsg}`, 
                life: 6000 
            })
        }
    }
}

// 🚪 Funciones para cerrar modales
const hideDialog = () => {
    if (hasUnsavedChanges.value) {
        unsavedChangesDialog.value = true
    } else {
        closeDialogWithoutSaving()
    }
}

const closeDialogWithoutSaving = () => {
    dialog.value = false
    unsavedChangesDialog.value = false
    hasUnsavedChanges.value = false
    originalCategoriaData.value = null
    resetForm()
}

const continueEditing = () => {
    unsavedChangesDialog.value = false
}

// ✅ CORREGIDO: Validaciones según TU controlador (máximo 50)
const validateNombre = () => {
    if (categoria.value.nombre && categoria.value.nombre.length > 50) {
        categoria.value.nombre = categoria.value.nombre.substring(0, 50)
    }
}
</script>

<template>
    <Head title="Control de Categorías" />
    <AuthenticatedLayout>
        <Toast class="z-[9999]" />        
        <div class="py-4 sm:py-6 px-4 sm:px-7 mt-6 sm:mt-10 mx-auto bg-red-50 shadow-md rounded-lg h-screen-full">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-2 sm:gap-0">
                <div class="flex items-center gap-3">
                    <Link :href="route('productos')" class="flex items-center text-blue-600 hover:text-blue-700 transition-colors duration-200 px-4 rounded-lg" title="Regresar a Productos">
                        <FontAwesomeIcon :icon="faArrowLeft" class="h-8" />
                    </Link>
                    <h3 class="text-lg sm:text-2xl text-blue-600 font-bold">Control de categorías</h3>
                </div>
                <button
                    class="bg-blue-500 border border-blue-500 p-2 text-sm text-white shadow-md hover:shadow-lg rounded-md hover:-translate-y-1 transition-transform duration-300" @click="openNew">
                    <FontAwesomeIcon :icon="faPlus" class="h-4 w-4 text-white" /><span>&nbsp;Agregar categoría</span>
                </button>                
            </div>

            <DataTable
                :value="categoriasFiltradas"
                dataKey="id"
                :paginator="true"
                :rows="rowsPerPage"
                :rowsPerPageOptions="rowsPerPageOptions"
                v-model:rowsPerPage="rowsPerPage"
                paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} categorías"
                class="overflow-x-auto max-w-full"
                style="display: block; max-width: 84vw"
                responsiveLayout="scroll"
                :pt="{
                    root: { class: 'text-sm' },
                    wrapper: { class: 'text-sm' },
                    table: { class: 'text-sm' },
                    thead: { class: 'text-sm' },
                    headerRow: { class: 'text-sm' },
                    headerCell: { class: 'text-sm font-medium py-3 px-2' },
                    tbody: { class: 'text-sm' },
                    bodyRow: { class: 'h-20 text-sm' },
                    bodyCell: { class: 'py-3 px-2 text-sm' },
                    paginator: { class: 'text-xs sm:text-sm' },
                    paginatorWrapper: { class: 'flex flex-wrap justify-center sm:justify-between items-center gap-2 p-2' }
                }"
            >
                <template #header>
                    <div class="bg-blue-50 p-3 rounded-lg shadow-sm border mb-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <h3 class="text-base font-medium text-gray-800 flex items-center gap-2">
                                    <i class="pi pi-filter text-blue-600 text-sm"></i>
                                    <span>Filtros</span>
                                </h3>
                                <div class="bg-blue-50 border border-blue-200 text-blue-700 px-3 py-1 rounded text-sm font-medium">
                                    {{ categoriasFiltradas.length }} resultado{{ categoriasFiltradas.length !== 1 ? 's' : '' }}
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <label for="tipo-estado" class="text-sm font-medium text-gray-700">Ver categorías:</label>
                                <Select
                                    id="tipo-estado"
                                    v-model="tipoEstadoSeleccionado"
                                    :options="tiposEstado"
                                    optionValue="value"
                                    optionLabel="label"
                                    class="w-32 h-8 text-sm"
                                    style="background-color: white; border-color: #93c5fd;"
                                />
                                <button class="bg-red-500 hover:bg-red-600 border border-red-500 px-3 py-1 text-sm text-white shadow-md rounded-md" @click="clearFilters">
                                    <FontAwesomeIcon :icon="faFilter" class="h-4 w-4 text-white" />
                                    <span>&nbsp;Limpiar filtros</span>
                                </button>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <InputText v-model="filters['global'].value" placeholder="🔍 Buscar categorías..." class="w-full h-9 text-sm" style="background-color: white; border-color: #93c5fd;"/>
                            </div>
                        </div>
                    </div>
                </template>

                <Column field="nombre" header="Nombre" sortable class="w-96 min-w-96">
                    <template #body="slotProps">
                        <div class="text-sm font-medium leading-relaxed">
                            {{ slotProps.data.nombre }}
                        </div>
                    </template>
                </Column>

                <Column field="tipo" header="Tipo" class="w-32 min-w-32">
                    <template #body="slotProps">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ slotProps.data.tipo === 'productos' ? 'Productos' : 'Hoteles' }}
                        </span>
                    </template>
                </Column>

                <Column :exportable="false" class="w-56 min-w-56">
                    <template #header>
                        <div class="text-center w-full font-bold">
                            Acciones
                        </div>
                    </template>
                    <template #body="slotProps">
                        <div class="flex gap-1 justify-center items-center w-64">
                            <button
                                class="bg-orange-200/30 border py-2 px-1 text-sm shadow-md hover:shadow-lg rounded-md hover:-translate-y-1 transition-transform duration-300"
                                @click="editCategoria(slotProps.data)">
                                <FontAwesomeIcon :icon="faPencil" class="h-4 w-4 text-orange-600" />
                                &nbsp;Editar
                            </button>
                            <button
                                class="bg-red-200/30 border py-2 px-1 text-sm shadow-md hover:shadow-lg rounded-md hover:-translate-y-1 transition-transform duration-300"
                                @click="confirmDeleteCategoria(slotProps.data)">
                                <FontAwesomeIcon :icon="faTrashCan" class="h-4 w-4 text-red-600" />
                                &nbsp;Eliminar
                            </button>
                        </div>
                    </template>
                </Column>
            </DataTable>

            <!-- 📝 Modal de formulario -->
            <Dialog v-model:visible="dialog" :header="btnTitle + ' Categoría'" :modal="true" :style="{ width: '450px' }" :closable="false">
                <div class="space-y-4">
                    <!-- Tipo -->
                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="tipo" class="w-24 flex items-center gap-1">
                                Tipo: <span class="text-red-500 font-bold">*</span>
                            </label>
                            <Select 
                                v-model="categoria.tipo" 
                                :options="tiposCategoria" 
                                optionLabel="label" 
                                optionValue="value" 
                                id="tipo" 
                                name="tipo" 
                                class="flex-1" 
                                placeholder="Selecciona el tipo" 
                                :class="{'p-invalid': submitted && !categoria.tipo}" 
                            />
                        </div>
                        <small class="text-red-500 ml-28" v-if="submitted && !categoria.tipo">
                            El tipo es obligatorio.
                        </small>
                    </div>

                    <!-- Nombre -->
                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="nombre" class="w-24 flex items-center gap-1">
                                Nombre: <span class="text-red-500 font-bold">*</span>
                            </label>
                            <InputText 
                                v-model.trim="categoria.nombre" 
                                id="nombre" 
                                name="nombre" 
                                :maxlength="50" 
                                :class="{'p-invalid': submitted && (!categoria.nombre || categoria.nombre.length < 3 || categoria.nombre.length > 50)}" 
                                class="flex-1" 
                                @input="validateNombre"
                                placeholder="Nombre de la categoría"
                            />
                        </div>
                        <small class="text-red-500 ml-28" v-if="categoria.nombre && categoria.nombre.length < 3">
                            El nombre debe tener al menos 3 caracteres. Actual: {{ categoria.nombre.length }}/3
                        </small>
                        <small class="text-orange-500 ml-28" v-if="categoria.nombre && categoria.nombre.length >= 45 && categoria.nombre.length <= 50">
                            Caracteres restantes: {{ 50 - categoria.nombre.length }}
                        </small>
                        <small class="text-red-500 ml-28" v-if="submitted && !categoria.nombre">
                            El nombre es obligatorio.
                        </small>
                    </div>
                </div>

                <template #footer>
                    <div class="flex justify-center gap-4 w-full">
                        <button 
                            type="button" 
                            class="bg-white hover:bg-green-100 text-green-600 border border-green-600 px-6 py-2 rounded-md transition-all duration-200 ease-in-out flex items-center gap-2" 
                            @click="hideDialog"
                        >
                            <FontAwesomeIcon :icon="faXmark" class="h-5 text-green-600" />
                            Cancelar
                        </button>
                        <button 
                            class="bg-red-500 hover:bg-red-700 text-white border-none px-6 py-2 rounded-md transition-all duration-200 ease-in-out flex items-center gap-2" 
                            @click="saveOrUpdate"
                        >
                            <FontAwesomeIcon :icon="faCheck" class="h-5 text-white" />
                            {{ btnTitle }}
                        </button>
                    </div>
                </template>
            </Dialog>

            <!-- 🗑️ Modal de confirmación de eliminación -->
            <Dialog v-model:visible="deleteDialog" header="Eliminar categoría" :modal="true" :style="{ width: '350px' }" :closable="false">
                <div class="flex items-center gap-3">
                    <FontAwesomeIcon :icon="faExclamationTriangle" class="h-8 w-8 text-red-500" />
                    <div class="flex flex-col">
                        <span>¿Estás seguro de eliminar la categoría: <b>{{ categoria.nombre }}</b>?</span>
                        <span class="text-red-600 text-sm font-medium mt-1">Esta acción es irreversible.</span>
                    </div>
                </div>
                <template #footer>
                    <div class="flex justify-center gap-4 w-full">
                        <button 
                            type="button" 
                            class="bg-white hover:bg-green-100 text-green-600 border border-green-600 px-6 py-2 rounded-md transition-all duration-200 ease-in-out flex items-center gap-2"
                            @click="deleteDialog = false"
                        >
                            <FontAwesomeIcon :icon="faXmark" class="h-5" />
                            <span>No</span>
                        </button>
                        <button 
                            type="button" 
                            class="bg-red-500 hover:bg-red-700 text-white border-none px-6 py-2 rounded-md transition-all duration-200 ease-in-out flex items-center gap-2"
                            @click="deleteCategoria"
                        >
                            <FontAwesomeIcon :icon="faCheck" class="h-5" />
                            <span>Sí</span>
                        </button>
                    </div>
                </template>
            </Dialog>

            <!-- ⚠️ Modal de cambios sin guardar -->
            <Dialog v-model:visible="unsavedChangesDialog" header="Cambios sin guardar" :modal="true" :style="{ width: '400px' }" :closable="false">
                <div class="flex items-center gap-3">
                    <FontAwesomeIcon :icon="faExclamationTriangle" class="h-8 w-8 text-red-500" />
                    <div class="flex flex-col">
                        <span>¡Tienes información sin guardar!</span>
                        <span class="text-red-600 text-sm font-medium mt-1">¿Deseas salir sin guardar?</span>
                    </div>
                </div>
                <template #footer>
                    <div class="flex justify-center gap-3 w-full">
                        <button 
                            type="button" 
                            class="bg-white hover:bg-green-100 text-green-600 border border-green-600 px-6 py-2 rounded-md transition-all duration-200 ease-in-out flex items-center gap-2"
                            @click="continueEditing"
                        >
                            <FontAwesomeIcon :icon="faPencil" class="h-4" />
                            <span>Continuar</span>
                        </button>
                        <button 
                            type="button" 
                            class="bg-red-500 hover:bg-red-700 text-white border-none px-6 py-2 rounded-md transition-all duration-200 ease-in-out flex items-center gap-2"
                            @click="closeDialogWithoutSaving"
                        >
                            <FontAwesomeIcon :icon="faSignOut" class="h-4" />
                            <span>Salir sin guardar</span>
                        </button>
                    </div>
                </template>
            </Dialog>
        </div>
    </AuthenticatedLayout>
</template>