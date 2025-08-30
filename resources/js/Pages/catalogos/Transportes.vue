<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, onMounted, computed, watch, nextTick } from "vue";
import { useToast } from "primevue/usetoast";
import { FilterMatchMode } from "@primevue/core/api";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faEdit, faTrash } from "@fortawesome/free-solid-svg-icons";
import axios from "axios";

const toast = useToast();

const transportes = ref([]);
const transporte = ref({
    id: null,
    nombre: "",
    capacidad: null,
});
const selectedTransportes = ref(null);
const dialog = ref(false);
const deleteDialog = ref(false);
const unsavedChangesDialog = ref(false);
const submitted = ref(false);
const hasUnsavedChanges = ref(false);
const originalTransporteData = ref(null);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    nombre: { value: null, matchMode: FilterMatchMode.CONTAINS },
    capacidad: { value: null, matchMode: FilterMatchMode.EQUALS },
});
const rowsPerPage = ref(10);
const rowsPerPageOptions = ref([5, 10, 20, 50]);
const btnTitle = ref("Guardar");

const filteredTransportes = computed(() => {
    let filtered = transportes.value;

    // Filtro por búsqueda global
    if (filters.value.global.value) {
        const searchTerm = filters.value.global.value.toLowerCase();
        filtered = filtered.filter(t =>
            t.nombre.toLowerCase().includes(searchTerm) ||
            (t.capacidad && t.capacidad.toString().includes(searchTerm))
        );
    }

    // Filtro por nombre
    if (filters.value.nombre.value) {
        filtered = filtered.filter(t =>
            t.nombre.toLowerCase().includes(filters.value.nombre.value.toLowerCase())
        );
    }

    // Filtro por capacidad
    if (filters.value.capacidad.value) {
        filtered = filtered.filter(t =>
            t.capacidad == filters.value.capacidad.value
        );
    }

    return filtered;
});

// Watcher para cambios en el modal (para cambios no guardados)
watch([transporte], () => {
    if (originalTransporteData.value && dialog.value) {
        nextTick(() => {
            const current = { ...transporte.value };
            const hasChanges = JSON.stringify(current) !== JSON.stringify(originalTransporteData.value);
            const isCreatingNew = !originalTransporteData.value.id;
            const hasAnyData = transporte.value.nombre || transporte.value.capacidad;
            hasUnsavedChanges.value = hasChanges || (isCreatingNew && hasAnyData);
        });
    }
}, { deep: true });

function resetForm() {
    transporte.value = {
        id: null,
        nombre: "",
        capacidad: null,
    };
    submitted.value = false;
}

onMounted(() => {
    fetchTransportes();
});

const fetchTransportes = async () => {
    try {
        const response = await axios.get("/api/transportes");
        transportes.value = response.data.sort((a, b) => b.id - a.id);
    } catch (err) {
        toast.add({ severity: "error", summary: "Error", detail: "No se pudieron cargar los transportes.", life: 4000 });
    }
};

const openNew = () => {
    resetForm();
    btnTitle.value = "Guardar";
    dialog.value = true;
    nextTick(() => {
        originalTransporteData.value = { ...transporte.value };
        hasUnsavedChanges.value = false;
    });
};

const editTransporte = (t) => {
    resetForm();
    transporte.value = { ...t };
    btnTitle.value = "Actualizar";
    dialog.value = true;
    nextTick(() => {
        originalTransporteData.value = { ...transporte.value };
        hasUnsavedChanges.value = false;
    });
};

const saveOrUpdate = async () => {
    submitted.value = true;
    if (!transporte.value.nombre || transporte.value.nombre.length < 3 || transporte.value.nombre.length > 50) {
        toast.add({ severity: "warn", summary: "Campos requeridos", detail: "El nombre debe tener entre 3 y 50 caracteres.", life: 4000 });
        return;
    }
    if (!transporte.value.capacidad || transporte.value.capacidad < 1) {
        toast.add({ severity: "warn", summary: "Campos requeridos", detail: "La capacidad debe ser al menos 1.", life: 4000 });
        return;
    }

    try {
        let response;
        if (!transporte.value.id) {
            response = await axios.post("/api/transportes", {
                nombre: transporte.value.nombre,
                capacidad: transporte.value.capacidad,
            });
            toast.add({ severity: "success", summary: "¡Éxito!", detail: "Transporte creado correctamente.", life: 4000 });
        } else {
            response = await axios.put(`/api/transportes/${transporte.value.id}`, {
                nombre: transporte.value.nombre,
                capacidad: transporte.value.capacidad,
            });
            toast.add({ severity: "success", summary: "¡Éxito!", detail: "Transporte actualizado correctamente.", life: 4000 });
        }
        await fetchTransportes();
        dialog.value = false;
        hasUnsavedChanges.value = false;
        originalTransporteData.value = null;
        resetForm();
    } catch (err) {
        toast.add({ severity: "error", summary: "Error", detail: "No se pudo guardar el transporte.", life: 4000 });
    }
};

const confirmDeleteTransporte = (t) => {
    transporte.value = { ...t };
    deleteDialog.value = true;
};

const deleteTransporte = async () => {
    try {
        await axios.delete(`/api/transportes/${transporte.value.id}`);
        await fetchTransportes();
        deleteDialog.value = false;
        toast.add({ severity: "success", summary: "¡Eliminado!", detail: "Transporte eliminado correctamente.", life: 4000 });
    } catch (err) {
        toast.add({ severity: "error", summary: "Error", detail: "No se pudo eliminar el transporte.", life: 4000 });
    }
};

const hideDialog = () => {
    if (hasUnsavedChanges.value) {
        unsavedChangesDialog.value = true;
    } else {
        closeDialogWithoutSaving();
    }
};

const closeDialogWithoutSaving = () => {
    dialog.value = false;
    unsavedChangesDialog.value = false;
    hasUnsavedChanges.value = false;
    originalTransporteData.value = null;
    resetForm();
};

const continueEditing = () => {
    unsavedChangesDialog.value = false;
};

const clearFilters = () => {
    filters.value.global.value = null;
    filters.value.nombre.value = null;
    filters.value.capacidad.value = null;
    toast.add({
        severity: "info",
        summary: "Filtros limpiados",
        detail: "Se han removido todos los filtros aplicados.",
        life: 3000
    });
};
</script>

<template>
    <Head title="Transportes" />
    <AuthenticatedLayout>
        <Toast class="z-[9999]" />
        <div class="py-4 sm:py-6 px-4 sm:px-7 mt-6 sm:mt-10 mx-auto bg-blue-100 shadow-md rounded-lg max-w-full">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-2 sm:gap-0">
                <h3 class="text-lg sm:text-xl font-bold">Catálogo de Transportes</h3>
                <Button
                    label="Agregar transporte"
                    icon="pi pi-plus"
                    style="background-color: #2563eb !important; color: white !important; border: none !important; padding: 0.5rem 1.5rem; border-radius: 0.375rem; transition: all 0.2s ease; font-weight: 500;"
                    onmouseover="this.style.backgroundColor='#1e40af'"
                    onmouseout="this.style.backgroundColor='#2563eb'"
                    class="w-full sm:w-auto"
                    @click="openNew"
                />
            </div>
            <DataTable
                :value="filteredTransportes"
                v-model:selection="selectedTransportes"
                dataKey="id"
                :paginator="true"
                :rows="rowsPerPage"
                :rowsPerPageOptions="rowsPerPageOptions"
                v-model:rowsPerPage="rowsPerPage"
                paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} transportes"
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
                    <div class="bg-white p-3 rounded-lg shadow-sm border mb-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <h3 class="text-base font-medium text-gray-800 flex items-center gap-2">
                                    <i class="pi pi-filter text-blue-600 text-sm"></i>
                                    Filtros
                                </h3>
                                <div class="bg-blue-50 border border-blue-200 text-blue-700 px-3 py-1 rounded text-sm font-medium">
                                    {{ filteredTransportes.length }} resultado{{ filteredTransportes.length !== 1 ? 's' : '' }}
                                </div>
                            </div>
                            <Button
                                label="Limpiar"
                                icon="pi pi-filter-slash"
                                class="p-button-outlined p-button-sm text-xs px-3 py-1"
                                @click="clearFilters"
                            />
                        </div>
                        <div class="space-y-3">
                            <div>
                                <InputText
                                    v-model="filters['global'].value"
                                    placeholder="🔍 Buscar transportes..."
                                    class="w-full h-9 text-sm"
                                />
                            </div>
                        </div>
                    </div>
                </template>
                <Column field="nombre" header="Nombre" sortable class="w-56 min-w-56">
                    <template #body="slotProps">
                        <div class="text-sm font-medium leading-relaxed">
                            {{ slotProps.data.nombre }}
                        </div>
                    </template>
                </Column>
                <Column field="capacidad" header="Capacidad" class="w-32 min-w-32">
                    <template #body="slotProps">
                        <div class="text-sm leading-relaxed">
                            {{ slotProps.data.capacidad }}
                        </div>
                    </template>
                </Column>
                <Column header="Acciones" :exportable="false" class="w-28 min-w-28">
                    <template #body="slotProps">
                        <div class="flex gap-1 justify-center items-center h-full">
                            <button
                                title="Editar transporte"
                                class="text-orange-600 hover:text-orange-900 transition-colors p-1.5 text-sm"
                                @click="editTransporte(slotProps.data)"
                            >
                                <FontAwesomeIcon :icon="faEdit" class="h-4 w-4" />
                            </button>
                            <button
                                title="Eliminar transporte"
                                class="text-red-600 hover:text-red-900 transition-colors p-1.5 text-sm"
                                @click="confirmDeleteTransporte(slotProps.data)"
                            >
                                <FontAwesomeIcon :icon="faTrash" class="h-4 w-4" />
                            </button>
                        </div>
                    </template>
                </Column>
            </DataTable>
            <Dialog
                v-model:visible="dialog"
                :header="btnTitle + ' Transporte'"
                :modal="true"
                :style="{ width: '400px' }"
                :closable="false"
            >
                <div class="space-y-4">
                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="nombre" class="w-24 flex items-center gap-1">
                                Nombre:
                                <span class="text-red-500 font-bold">*</span>
                            </label>
                            <InputText
                                v-model.trim="transporte.nombre"
                                id="nombre"
                                name="nombre"
                                :maxlength="50"
                                :class="{
                                    'p-invalid': submitted && (!transporte.nombre || transporte.nombre.length < 3 || transporte.nombre.length > 50),
                                }"
                                class="flex-1"
                            />
                        </div>
                        <small
                            class="text-red-500 ml-28"
                            v-if="transporte.nombre && transporte.nombre.length < 3"
                            >El nombre debe tener al menos 3 caracteres. Actual: {{ transporte.nombre.length }}/3</small
                        >
                        <small
                            class="text-orange-500 ml-28"
                            v-if="transporte.nombre && transporte.nombre.length > 45"
                            >Caracteres restantes: {{ 50 - transporte.nombre.length }}</small
                        >
                        <small
                            class="text-red-500 ml-28"
                            v-if="submitted && !transporte.nombre"
                            >El nombre es obligatorio.</small
                        >
                    </div>
                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="capacidad" class="w-24 flex items-center gap-1">
                                Capacidad:
                                <span class="text-red-500 font-bold">*</span>
                            </label>
                            <InputNumber
                                v-model="transporte.capacidad"
                                id="capacidad"
                                name="capacidad"
                                :min="1"
                                :max="500"
                                :step="1"
                                class="flex-1"
                                :class="{
                                    'p-invalid': submitted && (!transporte.capacidad || transporte.capacidad < 1),
                                }"
                                placeholder="Cantidad"
                            />
                        </div>
                        <small
                            class="text-red-500 ml-28"
                            v-if="submitted && (!transporte.capacidad || transporte.capacidad < 1)"
                            >La capacidad es obligatoria y debe ser mayor o igual a 1.</small
                        >
                    </div>
                </div>
                <template #footer>
                    <div class="flex justify-center gap-4 w-full">
                        <Button
                            label="Cancelar"
                            icon="pi pi-times"
                            style="background-color: white !important; border: 1px solid #2563eb !important; color: #2563eb !important; padding: 0.5rem 1.5rem; border-radius: 0.375rem; transition: all 0.2s ease;"
                            onmouseover="this.style.backgroundColor='#f0fdf4'; this.style.borderColor='#1e40af'; this.style.color='#1e40af'"
                            onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#2563eb'; this.style.color='#2563eb'"
                            text
                            @click="hideDialog"
                        />
                        <Button
                            :label="btnTitle"
                            icon="pi pi-check"
                            style="background-color: #2563eb !important; color: white !important; border: none !important; padding: 0.5rem 1.5rem; border-radius: 0.375rem; transition: all 0.2s ease;"
                            onmouseover="this.style.backgroundColor='#1e40af'"
                            onmouseout="this.style.backgroundColor='#2563eb'"
                            @click="saveOrUpdate"
                        />
                    </div>
                </template>
            </Dialog>
            <Dialog
                v-model:visible="deleteDialog"
                header="Confirmar"
                :modal="true"
                :style="{ width: '350px' }"
                :closable="false"
            >
                <div class="flex items-center gap-3">
                    <i class="pi pi-exclamation-triangle text-gray-800" style="font-size: 30px;"></i>
                    <span
                        >¿Eliminar el transporte <b>{{ transporte.nombre }}</b
                        >?</span
                    >
                </div>
                <template #footer>
                    <div class="flex justify-end gap-4 w-full">
                        <Button
                            label="No"
                            icon="pi pi-times"
                            style="background-color: white !important; border: 1px solid #2563eb !important; color: #2563eb !important; padding: 0.5rem 1.5rem; border-radius: 0.375rem; transition: all 0.2s ease;"
                            onmouseover="this.style.backgroundColor='#f0fdf4'; this.style.borderColor='#1e40af'; this.style.color='#1e40af'"
                            onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#2563eb'; this.style.color='#2563eb'"
                            text
                            @click="deleteDialog = false"
                        />
                        <Button
                            label="Sí"
                            icon="pi pi-check"
                            style="background-color: #2563eb !important; color: white !important; border: none !important; padding: 0.5rem 1.5rem; border-radius: 0.375rem; transition: all 0.2s ease;"
                            onmouseover="this.style.backgroundColor='#1e40af'"
                            onmouseout="this.style.backgroundColor='#2563eb'"
                            @click="deleteTransporte"
                        />
                    </div>
                </template>
            </Dialog>
            <Dialog
                v-model:visible="unsavedChangesDialog"
                header="Cambios sin guardar"
                :modal="true"
                :style="{ width: '400px' }"
                :closable="false"
            >
                <div class="flex items-center gap-3">
                    <i class="pi pi-exclamation-triangle text-gray-800" style="font-size: 24px;"></i>
                    <span>Tienes información sin guardar. ¿Deseas salir sin guardar?</span>
                </div>
                <template #footer>
                    <div class="flex justify-end gap-3 w-full">
                        <Button
                            label="Continuar"
                            icon="pi pi-pencil"
                            size="small"
                            style="background-color: white !important; border: 1px solid #2563eb !important; color: #2563eb !important; padding: 0.4rem 1rem; border-radius: 0.375rem; transition: all 0.2s ease; font-size: 0.875rem;"
                            onmouseover="this.style.backgroundColor='#f0fdf4'; this.style.borderColor='#1e40af'; this.style.color='#1e40af'"
                            onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#2563eb'; this.style.color='#2563eb'"
                            text
                            @click="continueEditing"
                        />
                        <Button
                            label="Salir sin guardar"
                            icon="pi pi-sign-out"
                            size="small"
                            style="background-color: #2563eb !important; color: white !important; border: none !important; padding: 0.4rem 1rem; border-radius: 0.375rem; transition: all 0.2s ease; font-size: 0.875rem;"
                            onmouseover="this.style.backgroundColor='#1e40af'"
                            onmouseout="this.style.backgroundColor='#2563eb'"
                            @click="closeDialogWithoutSaving"
                        />
                    </div>
                </template>
            </Dialog>
        </div>
    </AuthenticatedLayout>
</template>