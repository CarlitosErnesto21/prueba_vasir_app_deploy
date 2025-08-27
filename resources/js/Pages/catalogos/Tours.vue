<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, onMounted, computed, watch, nextTick } from "vue";
import { useToast } from "primevue/usetoast";
import { FilterMatchMode } from "@primevue/core/api";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faEdit, faEye, faTrash } from "@fortawesome/free-solid-svg-icons";
import DatePicker from "primevue/datepicker";
import axios from "axios";

const toast = useToast();

// Variables para listas de incluye/no incluye
const incluyeLista = ref([]);
const noIncluyeLista = ref([]);
const nuevoItemIncluye = ref("");
const nuevoItemNoIncluye = ref("");

const tours = ref([]);
const tour = ref({
    id: null,
    nombre: "",
    categoria: null,
    incluye: "",
    no_incluye: "",
    cupo_min: null,
    cupo_max: null,
    punto_salida: "",
    fecha_salida: null,
    fecha_regreso: null,
    estado: "DISPONIBLE",
    precio: null,
    transporte_id: null,
    imagenes: [],
});

const imagenPreviewList = ref([]);
const imagenFiles = ref([]);
const removedImages = ref([]);
const selectedTours = ref(null);
const dialog = ref(false);
const deleteDialog = ref(false);
const unsavedChangesDialog = ref(false);
const submitted = ref(false);
const hasUnsavedChanges = ref(false);
const originalTourData = ref(null);
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    categoria: { value: null, matchMode: FilterMatchMode.EQUALS },
    'transporte.nombre': { value: null, matchMode: FilterMatchMode.EQUALS },
    estado: { value: null, matchMode: FilterMatchMode.EQUALS },
    fecha_salida: { value: null, matchMode: FilterMatchMode.DATE_IS },
});
const selectedCategoria = ref(null);
const selectedTipoTransporte = ref(null);
const selectedEstado = ref(null);
const selectedFechaInicio = ref(null);
const selectedFechaFin = ref(null);
const rowsPerPage = ref(10);
const rowsPerPageOptions = ref([5, 10, 20, 50]);
const btnTitle = ref("Guardar");
const horaRegresoCalendar = ref(null);
const url = "/api/tours";
const IMAGE_PATH = "/images/tours/";
const tipoTransportes = ref([]);
const categoriasOptions = ref([
    { label: 'Nacional', value: 'NACIONAL' },
    { label: 'Internacional', value: 'INTERNACIONAL' }
]);
const estadosOptions = ref([
    { label: 'Disponible', value: 'DISPONIBLE' },
    { label: 'Agotado', value: 'AGOTADO' },
    { label: 'En Curso', value: 'EN_CURSO' },
    { label: 'Completado', value: 'COMPLETADO' },
    { label: 'Cancelado', value: 'CANCELADO' },
    { label: 'Suspendido', value: 'SUSPENDIDO' },
    { label: 'Reprogramado', value: 'REPROGRAMADO' }
]);
const showImageDialog = ref(false);
const selectedImages = ref([]);
const carouselIndex = ref(0);
const fileInput = ref(null);

// Computed property para obtener tours filtrados
const filteredTours = computed(() => {
    let filtered = tours.value;
    
    // Filtro por búsqueda global
    if (filters.value.global.value) {
        const searchTerm = filters.value.global.value.toLowerCase();
        filtered = filtered.filter(tour => 
            tour.nombre.toLowerCase().includes(searchTerm) ||
            (tour.incluye && tour.incluye.toLowerCase().includes(searchTerm)) ||
            (tour.no_incluye && tour.no_incluye.toLowerCase().includes(searchTerm)) ||
            tour.punto_salida.toLowerCase().includes(searchTerm)
        );
    }
    
    // Filtro por categoría
    if (filters.value.categoria.value) {
        filtered = filtered.filter(tour => 
            tour.categoria === filters.value.categoria.value
        );
    }
    
    // Filtro por tipo de transporte
    if (filters.value['transporte.nombre'].value) {
        filtered = filtered.filter(tour => 
            tour['transporte.nombre'] === filters.value['transporte.nombre'].value
        );
    }
    
    // Filtro por estado
    if (filters.value.estado.value) {
        filtered = filtered.filter(tour => 
            tour.estado === filters.value.estado.value
        );
    }
    
    // Filtro por rango de fechas de salida
    if (selectedFechaInicio.value || selectedFechaFin.value) {
        filtered = filtered.filter(tour => {
            if (!tour.fecha_salida) return false;
            
            const fechaTour = new Date(tour.fecha_salida);
            let cumpleFiltro = true;
            
            if (selectedFechaInicio.value) {
                const fechaInicio = new Date(selectedFechaInicio.value);
                cumpleFiltro = cumpleFiltro && fechaTour >= fechaInicio;
            }
            
            if (selectedFechaFin.value) {
                const fechaFin = new Date(selectedFechaFin.value);
                fechaFin.setHours(23, 59, 59, 999); // Incluir todo el día final
                cumpleFiltro = cumpleFiltro && fechaTour <= fechaFin;
            }
            
            return cumpleFiltro;
        });
    }

    return filtered;
});

// Watcher para detectar cambios en el modal
watch([tour, horaRegresoCalendar, incluyeLista, noIncluyeLista, imagenPreviewList, removedImages], () => {
    if (originalTourData.value && dialog.value) {
        // Usar nextTick para evitar que se active inmediatamente después de cargar datos
        nextTick(() => {
            const current = {
                ...tour.value,
                fecha_regreso: horaRegresoCalendar.value,
                incluye_lista: [...incluyeLista.value],
                no_incluye_lista: [...noIncluyeLista.value],
                imagenes_actuales: [...imagenPreviewList.value]
            };
            
            // Comparar datos actuales con originales
            const hasChanges = JSON.stringify(current) !== JSON.stringify({
                ...originalTourData.value,
                imagenes_actuales: originalTourData.value.imagenes_originales
            }) || removedImages.value.length > 0;
            
            // Para tours nuevos, también verificar si hay algún dato ingresado
            const isCreatingNew = !originalTourData.value.id;
            const hasAnyData = tour.value.nombre || 
                              tour.value.punto_salida || 
                              tour.value.fecha_salida || 
                              horaRegresoCalendar.value || 
                              tour.value.precio ||
                              tour.value.categoria ||
                              tour.value.transporte_id ||
                              incluyeLista.value.length > 0 ||
                              noIncluyeLista.value.length > 0 ||
                              imagenPreviewList.value.length > 0;
            
            hasUnsavedChanges.value = hasChanges || (isCreatingNew && hasAnyData);
        });
    }
}, { deep: true, flush: 'post' });

function resetForm() {
    tour.value = {
        id: null,
        nombre: "",
        categoria: null,
        incluye: "",
        no_incluye: "",
        cupo_min: null,
        cupo_max: null,
        punto_salida: "",
        fecha_salida: null,
        fecha_regreso: null,
        estado: "DISPONIBLE",
        precio: null,
        transporte_id: null,
        imagenes: [],
    };
    imagenPreviewList.value = [];
    imagenFiles.value = [];
    removedImages.value = [];
    horaRegresoCalendar.value = null;
    submitted.value = false;
    
    // Limpiar listas
    incluyeLista.value = [];
    noIncluyeLista.value = [];
    nuevoItemIncluye.value = "";
    nuevoItemNoIncluye.value = "";
}

// Funciones para manejar listas de incluye/no incluye
const agregarItemIncluye = () => {
    if (nuevoItemIncluye.value.trim()) {
        incluyeLista.value.push(nuevoItemIncluye.value.trim());
        nuevoItemIncluye.value = "";
    }
};

const eliminarItemIncluye = (index) => {
    incluyeLista.value.splice(index, 1);
};

const agregarItemNoIncluye = () => {
    if (nuevoItemNoIncluye.value.trim()) {
        noIncluyeLista.value.push(nuevoItemNoIncluye.value.trim());
        nuevoItemNoIncluye.value = "";
    }
};

const eliminarItemNoIncluye = (index) => {
    noIncluyeLista.value.splice(index, 1);
};

// Funciones para convertir entre texto y lista
const textoALista = (texto) => {
    return texto ? texto.split('|').filter(item => item.trim()).map(item => item.trim()) : [];
};

const listaATexto = (lista) => {
    return lista.join('|');
};

// Obtener tours, categorías y tipos de transporte
onMounted(() => {
    fetchTours();
    fetchTipoTransportes();
});

const fetchTours = async () => {
    try {
        const response = await axios.get(url);
        tours.value = response.data.map((t) => ({
            ...t,
            imagenes: (t.imagenes || []).map((img) =>
                typeof img === "string" ? img : img.nombre
            ),
            transporte_nombre: t.transporte?.nombre || "",
            transporte_capacidad: t.transporte?.capacidad || "",
            // Agregar campos para filtros
            'transporte.nombre': t.transporte?.nombre,
        })).sort((a, b) => {
            // Ordenar por fecha de creación descendente (más recientes primero)
            const dateA = new Date(a.created_at);
            const dateB = new Date(b.created_at);
            return dateB - dateA;
        });
    } catch (err) {
        toast.add({ severity: "error", summary: "Error", detail: "No se pudieron cargar los tours.", life: 4000 });
    }
};

const fetchTipoTransportes = async () => {
    try {
        const response = await axios.get("/api/transportes");
        tipoTransportes.value = response.data || [];
    } catch {
        tipoTransportes.value = [];
        toast.add({ severity: "error", summary: "Error", detail: "No se pudieron cargar los transportes.", life: 4000 });
    }
};

// Funciones para manejar filtros
const onCategoriaFilterChange = () => {
    filters.value.categoria.value = selectedCategoria.value;
};

const onTipoTransporteFilterChange = () => {
    if (selectedTipoTransporte.value) {
        // Encontrar el transporte seleccionado para obtener su nombre
        const transporteSeleccionado = tipoTransportes.value.find(t => t.id === selectedTipoTransporte.value);
        filters.value['transporte.nombre'].value = transporteSeleccionado ? transporteSeleccionado.nombre : null;
    } else {
        filters.value['transporte.nombre'].value = null;
    }
};

const onEstadoFilterChange = () => {
    filters.value.estado.value = selectedEstado.value;
};

const onFechaInicioFilterChange = () => {
    // Si la fecha inicio es posterior a la fecha fin, limpiar fecha fin
    if (selectedFechaInicio.value && selectedFechaFin.value) {
        const fechaInicio = new Date(selectedFechaInicio.value);
        const fechaFin = new Date(selectedFechaFin.value);
        
        if (fechaInicio > fechaFin) {
            selectedFechaFin.value = null;
            toast.add({ 
                severity: "warn", 
                summary: "Fecha ajustada", 
                detail: "La fecha 'hasta' se limpió porque era anterior a la fecha 'desde'.", 
                life: 3000 
            });
        }
    }
};

const onFechaFinFilterChange = () => {
    // Si la fecha fin es anterior a la fecha inicio, limpiar fecha inicio
    if (selectedFechaInicio.value && selectedFechaFin.value) {
        const fechaInicio = new Date(selectedFechaInicio.value);
        const fechaFin = new Date(selectedFechaFin.value);
        
        if (fechaFin < fechaInicio) {
            selectedFechaInicio.value = null;
            toast.add({ 
                severity: "warn", 
                summary: "Fecha ajustada", 
                detail: "La fecha 'desde' se limpió porque era posterior a la fecha 'hasta'.", 
                life: 3000 
            });
        }
    }
};

const clearFilters = () => {
    selectedCategoria.value = null;
    selectedTipoTransporte.value = null;
    selectedEstado.value = null;
    selectedFechaInicio.value = null;
    selectedFechaFin.value = null;
    filters.value.global.value = null;
    filters.value.categoria.value = null;
    filters.value['transporte.nombre'].value = null;
    filters.value.estado.value = null;
    filters.value.fecha_salida.value = null;
    
    toast.add({ 
        severity: "info", 
        summary: "Filtros limpiados", 
        detail: "Se han removido todos los filtros aplicados.", 
        life: 3000 
    });
};

const openNew = () => {
    resetForm();
    btnTitle.value = "Guardar";
    submitted.value = false; // Asegurar que no hay estado de validación
    dialog.value = true;
    
    // Configurar estado inicial para detectar cambios en tours nuevos
    nextTick(() => {
        originalTourData.value = {
            ...tour.value,
            fecha_regreso: horaRegresoCalendar.value,
            incluye_lista: [...incluyeLista.value],
            no_incluye_lista: [...noIncluyeLista.value],
            imagenes_originales: [...imagenPreviewList.value]
        };
        hasUnsavedChanges.value = false;
    });
};

const editTour = (t) => {
    resetForm();
    submitted.value = false; // Limpiar estado de validación
    
    tour.value = {
        ...t,
        transporte_id: t.transporte?.id || t.transporte_id || null,
    };

    // Cargar fecha_salida correctamente
    if (t.fecha_salida) {
        if (typeof t.fecha_salida === 'string') {
            tour.value.fecha_salida = new Date(t.fecha_salida);
        } else {
            tour.value.fecha_salida = t.fecha_salida;
        }
    }

    // Cargar fecha_regreso correctamente para horaRegresoCalendar
    if (t.fecha_regreso) {
        if (typeof t.fecha_regreso === 'string') {
            horaRegresoCalendar.value = new Date(t.fecha_regreso);
        } else {
            horaRegresoCalendar.value = t.fecha_regreso;
        }
    }

    imagenPreviewList.value = (t.imagenes || []).map(img => typeof img === "string" ? img : img.nombre);
    
    // Cargar listas desde el texto existente
    incluyeLista.value = textoALista(t.incluye);
    noIncluyeLista.value = textoALista(t.no_incluye);
    
    hasUnsavedChanges.value = false;
    btnTitle.value = "Actualizar";
    dialog.value = true;
    
    // Guardar datos originales después de que todo esté cargado
    nextTick(() => {
        originalTourData.value = {
            ...tour.value,
            fecha_regreso: horaRegresoCalendar.value,
            incluye_lista: [...incluyeLista.value],
            no_incluye_lista: [...noIncluyeLista.value],
            imagenes_originales: [...imagenPreviewList.value]
        };
        hasUnsavedChanges.value = false;
    });
};

const saveOrUpdate = async () => {
    submitted.value = true;

    if (!horaRegresoCalendar.value) {
        toast.add({ severity: "warn", summary: "Campos requeridos", detail: "Por favor verifica que todos los campos obligatorios estén completos.", life: 4000 });
        return;
    }

    // Validar nombre específicamente
    if (!tour.value.nombre || tour.value.nombre.length < 10 || tour.value.nombre.length > 200) {
        toast.add({ severity: "warn", summary: "Campos requeridos", detail: "Por favor verifica que todos los campos obligatorios estén completos y cumplan los requisitos.", life: 4000 });
        return;
    }

    // Validar punto_salida específicamente
    if (!tour.value.punto_salida || tour.value.punto_salida.length < 5 || tour.value.punto_salida.length > 200) {
        toast.add({ severity: "warn", summary: "Campos requeridos", detail: "Por favor verifica que todos los campos obligatorios estén completos y cumplan los requisitos.", life: 4000 });
        return;
    }

    // Validar cupos si están definidos
    if (tour.value.cupo_min && tour.value.cupo_max && tour.value.cupo_min >= tour.value.cupo_max) {
        toast.add({ severity: "warn", summary: "Verificar datos", detail: "Por favor revisa los valores ingresados y corrige cualquier inconsistencia.", life: 4000 });
        return;
    }

    // Validar fechas
    if (!validateFechaSalida()) {
        toast.add({ severity: "warn", summary: "Verificar fechas", detail: "Por favor revisa las fechas ingresadas y asegúrate de que sean correctas.", life: 4000 });
        return;
    }

    if (!validateFechaRegreso()) {
        toast.add({ severity: "warn", summary: "Verificar fechas", detail: "Por favor revisa las fechas ingresadas y asegúrate de que sean correctas.", life: 4000 });
        return;
    }

    if (!tour.value.fecha_salida || !tour.value.precio || !tour.value.categoria || !tour.value.transporte_id || imagenPreviewList.value.length === 0) {
        toast.add({ severity: "warn", summary: "Campos requeridos", detail: "Por favor verifica que todos los campos obligatorios estén completos.", life: 4000 });
        return;
    }

    // Validar tamaño de imágenes antes de enviar
    const maxSize = 2 * 1024 * 1024; // 2MB
    const oversizedFiles = imagenFiles.value.filter(file => file.size > maxSize);
    if (oversizedFiles.length > 0) {
        toast.add({ 
            severity: "warn", 
            summary: "Verificar archivos", 
            detail: "Por favor revisa el tamaño de las imágenes seleccionadas.", 
            life: 4000 
        });
        return;
    }

    try {
        const formData = new FormData();
        
        // Campos obligatorios con validación
        formData.append("nombre", tour.value.nombre || "");
        
        // Campos opcionales - solo agregar si tienen contenido
        if (incluyeLista.value.length > 0) {
            formData.append("incluye", listaATexto(incluyeLista.value));
        } else {
            formData.append("incluye", ""); // Enviar cadena vacía para limpiar el campo
        }
        
        formData.append("punto_salida", tour.value.punto_salida || "");
        
        // Formatear fecha_salida correctamente
        if (tour.value.fecha_salida instanceof Date) {
            formData.append("fecha_salida", tour.value.fecha_salida.toISOString().slice(0, 19).replace('T', ' '));
        } else if (tour.value.fecha_salida) {
            formData.append("fecha_salida", tour.value.fecha_salida);
        }
        
        // Formatear fecha_regreso correctamente
        if (horaRegresoCalendar.value instanceof Date) {
            formData.append("fecha_regreso", horaRegresoCalendar.value.toISOString().slice(0, 19).replace('T', ' '));
        } else if (horaRegresoCalendar.value) {
            formData.append("fecha_regreso", horaRegresoCalendar.value);
        }
        
        // Campos numéricos con validación
        formData.append("precio", tour.value.precio || "");
        formData.append("categoria", tour.value.categoria || "");
        formData.append("transporte_id", tour.value.transporte_id || "");

        // Agregar campos opcionales
        if (noIncluyeLista.value.length > 0) {
            formData.append("no_incluye", listaATexto(noIncluyeLista.value));
        } else {
            formData.append("no_incluye", ""); // Enviar cadena vacía para limpiar el campo
        }
        if (tour.value.cupo_min) {
            formData.append("cupo_min", tour.value.cupo_min);
        }
        if (tour.value.cupo_max) {
            formData.append("cupo_max", tour.value.cupo_max);
        }

        imagenFiles.value.forEach(f => formData.append("imagenes[]", f));
        removedImages.value.forEach(img => {
            // Manejo seguro para obtener el nombre del archivo
            const fileName = img.includes("/") ? img.split("/").pop() : img;
            formData.append("removed_images[]", fileName);
        });

        let response;
        if (!tour.value.id) {
            response = await axios.post(url, formData, { headers: {"Content-Type":"multipart/form-data"} });
            toast.add({ 
                severity: "success", 
                summary: "¡Éxito!", 
                detail: "El tour ha sido creado correctamente.", 
                life: 5000 
            });
        } else {
            formData.append("_method","PUT");
            response = await axios.post(`${url}/${tour.value.id}`, formData, { headers: {"Content-Type":"multipart/form-data"} });
            toast.add({ 
                severity: "success", 
                summary: "¡Éxito!", 
                detail: "El tour ha sido actualizado correctamente.", 
                life: 5000 
            });
        }

        await fetchTours();
        dialog.value = false;
        hasUnsavedChanges.value = false;
        originalTourData.value = null;
        resetForm();
    } catch (err) {
        toast.add({ 
            severity: "warn", 
            summary: "Error de validación", 
            detail: "Por favor revisa los campos e intenta nuevamente.", 
            life: 6000 
        });
    }
};

const confirmDeleteTour = (t) => { tour.value = { ...t }; deleteDialog.value = true; };
const deleteTour = async () => {
    try {
        await axios.delete(`${url}/${tour.value.id}`);
        await fetchTours();
        deleteDialog.value = false;
        toast.add({ 
            severity: "success", 
            summary: "¡Eliminado!", 
            detail: "El tour ha sido eliminado correctamente.", 
            life: 5000 
        });
    } catch (err) {
        toast.add({ 
            severity: "error", 
            summary: "Error", 
            detail: "No se pudo eliminar el tour. Inténtalo de nuevo.", 
            life: 5000 
        });
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
    originalTourData.value = null;
    resetForm();
};

const continueEditing = () => {
    unsavedChangesDialog.value = false;
};
const onImageSelect = (event) => {
    const files = event.target ? event.target.files : event.files;
    const maxSize = 2 * 1024 * 1024; // 2MB en bytes
    
    for (const file of files) {
        if (file instanceof File) {
            // Validar tamaño del archivo
            if (file.size > maxSize) {
                toast.add({ 
                    severity: "warn", 
                    summary: "Archivo no válido", 
                    detail: "Por favor selecciona archivos que cumplan con los requisitos de tamaño (máximo 2MB).", 
                    life: 5000 
                });
                continue; // Saltar este archivo
            }
            
            // Validar tipo de archivo
            if (!file.type.startsWith('image/')) {
                toast.add({ 
                    severity: "warn", 
                    summary: "Formato no válido", 
                    detail: "Por favor selecciona únicamente archivos de imagen válidos.", 
                    life: 4000 
                });
                continue; // Saltar este archivo
            }
            
            imagenFiles.value.push(file);
            const reader = new FileReader();
            reader.onload = (e) => imagenPreviewList.value.push(e.target.result);
            reader.readAsDataURL(file);
        }
    }
};
const removeImage = (index) => {
    const removed = imagenPreviewList.value[index];
    if (typeof removed === "string" && removed.startsWith("data:image")) {
        imagenPreviewList.value.splice(index,1);
        imagenFiles.value.splice(index,1);
    } else {
        removedImages.value.push(removed);
        imagenPreviewList.value.splice(index,1);
    }
};
const viewImages = (imagenesTour) => {
    selectedImages.value = (imagenesTour||[]).map(img=>IMAGE_PATH+(typeof img==="string"?img:img.nombre));
    showImageDialog.value = true;
};

const validateNombre = () => {
    // Limitar a 200 caracteres máximo
    if (tour.value.nombre && tour.value.nombre.length > 200) {
        tour.value.nombre = tour.value.nombre.substring(0, 200);
    }
};

const validatePuntoSalida = () => {
    // Limitar a 200 caracteres máximo
    if (tour.value.punto_salida && tour.value.punto_salida.length > 200) {
        tour.value.punto_salida = tour.value.punto_salida.substring(0, 200);
    }
};

const validateCupos = () => {
    // Si se llena cupo_min, asegurar que cupo_max sea al menos cupo_min + 1
    if (tour.value.cupo_min && !tour.value.cupo_max) {
        // Si hay mínimo pero no máximo, sugerir un máximo
        return true;
    }
    
    // Si se llena cupo_max, asegurar que cupo_min sea menor
    if (tour.value.cupo_max && !tour.value.cupo_min) {
        // Si hay máximo pero no mínimo, está bien
        return true;
    }
    
    // Si ambos están llenos, validar la lógica
    if (tour.value.cupo_min && tour.value.cupo_max) {
        if (tour.value.cupo_min >= tour.value.cupo_max) {
            return false;
        }
    }
    
    return true;
};

const validateFechaSalida = () => {
    if (!tour.value.fecha_salida) return true;
    
    const now = new Date();
    const fechaSalida = new Date(tour.value.fecha_salida);
    
    // Validar que la fecha de salida sea futura (al menos 1 hora después de ahora)
    const minTime = new Date(now.getTime() + 60 * 60 * 1000); // 1 hora después de ahora
    if (fechaSalida < minTime) {
        return false;
    }
    
    // Si hay fecha de regreso, validar que salida sea anterior al regreso con mínimo 1 hora de diferencia
    if (horaRegresoCalendar.value) {
        const fechaRegreso = new Date(horaRegresoCalendar.value);
        const diferenciaHoras = (fechaRegreso - fechaSalida) / (1000 * 60 * 60);
        
        if (diferenciaHoras < 1) {
            return false;
        }
    }
    
    return true;
};

const validateFechaRegreso = () => {
    if (!horaRegresoCalendar.value) return true;
    
    const fechaRegreso = new Date(horaRegresoCalendar.value);
    
    // Si hay fecha de salida, validar que regreso sea posterior a salida con mínimo 1 hora de diferencia
    if (tour.value.fecha_salida) {
        const fechaSalida = new Date(tour.value.fecha_salida);
        const diferenciaHoras = (fechaRegreso - fechaSalida) / (1000 * 60 * 60);
        
        // Validar que haya al menos 1 hora de diferencia
        if (diferenciaHoras < 1) {
            return false;
        }
    }
    
    return true;
};

// Función para obtener fecha mínima (1 hora desde ahora)
const getMinDate = () => {
    const now = new Date();
    now.setHours(now.getHours() + 1); // 1 hora desde ahora
    return now;
};

// Función para obtener fecha mínima de regreso (fecha salida + 1 hora)
const getMinDateRegreso = () => {
    if (!tour.value.fecha_salida) return getMinDate();
    
    const fechaSalida = new Date(tour.value.fecha_salida);
    fechaSalida.setHours(fechaSalida.getHours() + 1); // 1 hora después de la salida
    return fechaSalida;
};

// Función para obtener fecha máxima de salida (fecha regreso - 1 hora)
const getMaxDateSalida = () => {
    if (!horaRegresoCalendar.value) return null;
    
    const fechaRegreso = new Date(horaRegresoCalendar.value);
    fechaRegreso.setHours(fechaRegreso.getHours() - 1); // 1 hora antes del regreso
    return fechaRegreso;
};
</script>


<template>
    <Head title="Tours" />
    <AuthenticatedLayout>
        <!-- Toast Component for notifications with high z-index -->
        <Toast class="z-[9999]" />
        
        <div class="py-4 sm:py-6 px-4 sm:px-7 mt-6 sm:mt-10 mx-auto bg-red-100 shadow-md rounded-lg max-w-full">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-2 sm:gap-0">
                <h3 class="text-lg sm:text-xl font-bold">Catálogo de Tours</h3>
                <Button
                    label="Agregar tour"
                    icon="pi pi-plus"
                    style="background-color: #ef4444 !important; color: white !important; border: none !important; padding: 0.5rem 1.5rem; border-radius: 0.375rem; transition: all 0.2s ease; font-weight: 500;"
                    onmouseover="this.style.backgroundColor='#dc2626'"
                    onmouseout="this.style.backgroundColor='#ef4444'"
                    class="w-full sm:w-auto"
                    @click="openNew"
                />
            </div>

            <DataTable
                :value="filteredTours"
                v-model:selection="selectedTours"
                dataKey="id"
                :paginator="true"
                :rows="rowsPerPage"
                :rowsPerPageOptions="rowsPerPageOptions"
                v-model:rowsPerPage="rowsPerPage"
                paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} tours"
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
                        <!-- Encabezado compacto -->
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <h3 class="text-base font-medium text-gray-800 flex items-center gap-2">
                                    <i class="pi pi-filter text-blue-600 text-sm"></i>
                                    Filtros
                                </h3>
                                <div class="bg-blue-50 border border-blue-200 text-blue-700 px-3 py-1 rounded text-sm font-medium">
                                    {{ filteredTours.length }} resultado{{ filteredTours.length !== 1 ? 's' : '' }}
                                </div>
                            </div>
                            <Button
                                label="Limpiar"
                                icon="pi pi-filter-slash"
                                class="p-button-outlined p-button-sm text-xs px-3 py-1"
                                @click="clearFilters"
                            />
                        </div>
                        
                        <!-- Filtros en dos filas compactas -->
                        <div class="space-y-3">
                            <!-- Fila 1: Buscador -->
                            <div>
                                <InputText
                                    v-model="filters['global'].value"
                                    placeholder="🔍 Buscar tours..."
                                    class="w-full h-9 text-sm"
                                />
                            </div>
                            
                            <!-- Fila 2: Filtros con mejor responsive -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                                <!-- Categoría -->
                                <div class="sm:col-span-1">
                                    <!--PENDIENTE-->
                                    <Select
                                        v-model="selectedCategoria"
                                        :options="categoriasOptions" 
                                        optionLabel="label"
                                        optionValue="value"
                                        placeholder="Categoría"
                                        class="w-full h-9 text-sm"
                                        @change="onCategoriaFilterChange"
                                        :clearable="true"
                                    />
                                </div>
                                
                                <!-- Transporte -->
                                <div class="sm:col-span-1">
                                    <Select
                                        v-model="selectedTipoTransporte"
                                        :options="tipoTransportes"
                                        optionLabel="nombre"
                                        optionValue="id"
                                        placeholder="Transporte"
                                        class="w-full h-9 text-sm"
                                        @change="onTipoTransporteFilterChange"
                                        :clearable="true"
                                    />
                                </div>
                                
                                <!-- Estado -->
                                <div class="sm:col-span-2 md:col-span-1">
                                    <Select
                                        v-model="selectedEstado"
                                        :options="estadosOptions"
                                        optionLabel="label"
                                        optionValue="value"
                                        placeholder="Estado"
                                        class="w-full h-9 text-sm"
                                        @change="onEstadoFilterChange"
                                        :clearable="true"
                                    />
                                </div>
                                
                                <!-- Fecha desde -->
                                <div class="sm:col-span-1 md:col-span-1 lg:col-span-1">
                                    <DatePicker
                                        v-model="selectedFechaInicio"
                                        placeholder="Fecha desde"
                                        class="w-full h-9 text-sm"
                                        @date-select="onFechaInicioFilterChange"
                                        @clear="onFechaInicioFilterChange"
                                        :showIcon="true"
                                        dateFormat="dd/mm/yy"
                                        :maxDate="selectedFechaFin"
                                    />
                                </div>
                                
                                <!-- Fecha hasta -->
                                <div class="sm:col-span-1 md:col-span-1 lg:col-span-1">
                                    <DatePicker
                                        v-model="selectedFechaFin"
                                        placeholder="Fecha hasta"
                                        class="w-full h-9 text-sm"
                                        @date-select="onFechaFinFilterChange"
                                        @clear="onFechaFinFilterChange"
                                        :showIcon="true"
                                        dateFormat="dd/mm/yy"
                                        :minDate="selectedFechaInicio"
                                    />
                                </div>
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
                <Column field="incluye" header="Incluye" class="w-40 min-w-40">
                    <template #body="slotProps">
                        <div class="text-sm leading-relaxed whitespace-normal">
                            {{ slotProps.data.incluye }}
                        </div>
                    </template>
                </Column>
                <Column field="no_incluye" header="No incluye" class="w-40 min-w-40">
                    <template #body="slotProps">
                        <div class="text-sm leading-relaxed whitespace-normal">
                            {{ slotProps.data.no_incluye }}
                        </div>
                    </template>
                </Column>
                <Column field="punto_salida" header="Punto salida" class="w-32 min-w-32">
                    <template #body="slotProps">
                        <div class="text-sm leading-relaxed">
                            {{ slotProps.data.punto_salida }}
                        </div>
                    </template>
                </Column>
                <Column field="fecha_salida" header="Fecha salida" class="w-36 min-w-36">
                    <template #body="slotProps">
                        <div class="text-sm leading-relaxed">
                            {{
                                slotProps.data.fecha_salida
                                    ? new Date(slotProps.data.fecha_salida).toLocaleString(
                                          "es-ES",
                                          {
                                              day: "2-digit",
                                              month: "2-digit",
                                              year: "numeric",
                                              hour: "2-digit",
                                              minute: "2-digit",
                                              hour12: true,
                                          }
                                      )
                                    : ""
                            }}
                        </div>
                    </template>
                </Column>
                <Column field="precio" header="Precio" class="w-24 min-w-24">
                    <template #body="slotProps">
                        <div class="text-sm font-medium leading-relaxed">
                            {{
                                isNaN(Number(slotProps.data.precio))
                                    ? ""
                                    : `$${Number(slotProps.data.precio).toFixed(2)}`
                            }}
                        </div>
                    </template>
                </Column>
                <Column header="Acciones" :exportable="false" class="w-28 min-w-28">
                    <template #body="slotProps">
                        <div class="flex gap-1 justify-center items-center h-full">
                            <!-- Botón Editar -->
                            <button
                                title="Editar tour"
                                class="text-orange-600 hover:text-orange-900 transition-colors p-1.5 text-sm"
                                @click="editTour(slotProps.data)"
                            >
                                <FontAwesomeIcon :icon="faEdit" class="h-4 w-4" />
                            </button>
                            
                            <!-- Botón Ver imágenes -->
                            <button
                                title="Ver imágenes"
                                class="text-blue-600 hover:text-blue-900 transition-colors p-1.5 text-sm"
                                @click="viewImages(slotProps.data.imagenes)"
                            >
                                <FontAwesomeIcon :icon="faEye" class="h-4 w-4" />
                            </button>
                            
                            <!-- Botón Eliminar -->
                            <button
                                title="Eliminar tour"
                                class="text-red-600 hover:text-red-900 transition-colors p-1.5 text-sm"
                                @click="confirmDeleteTour(slotProps.data)"
                            >
                                <FontAwesomeIcon :icon="faTrash" class="h-4 w-4" />
                            </button>
                        </div>
                    </template>
                </Column>
            </DataTable>

            <Dialog
                v-model:visible="dialog"
                :header="btnTitle + ' Tour'"
                :modal="true"
                :style="{ width: '500px' }"
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
                                v-model.trim="tour.nombre"
                                id="nombre"
                                name="nombre"
                                :maxlength="200"
                                :class="{
                                    'p-invalid': submitted && (!tour.nombre || tour.nombre.length < 10 || tour.nombre.length > 200),
                                }"
                                class="flex-1"
                                @input="validateNombre"
                            />
                        </div>
                        <!-- Mensaje cuando tiene menos de 10 caracteres -->
                        <small
                            class="text-red-500 ml-28"
                            v-if="tour.nombre && tour.nombre.length < 10"
                            >El nombre debe tener al menos 10 caracteres. Actual: {{ tour.nombre.length }}/10</small
                        >
                        <!-- Mensaje cuando está cerca del límite o lo excede -->
                        <small
                            class="text-orange-500 ml-28"
                            v-if="tour.nombre && tour.nombre.length >= 180 && tour.nombre.length <= 200"
                            >Caracteres restantes: {{ 200 - tour.nombre.length }}</small
                        >
                        <!-- Mensaje de error cuando está vacío y se intentó enviar -->
                        <small
                            class="text-red-500 ml-28"
                            v-if="submitted && !tour.nombre"
                            >El nombre es obligatorio.</small
                        >
                    </div>
                    <div class="w-full flex flex-col">
                        <div class="flex items-start gap-4">
                            <label for="incluye" class="w-24 flex items-center gap-1 mt-2">
                                Incluye:
                            </label>
                            <div class="flex-1">
                                <!-- Input para agregar nuevo item -->
                                <div class="flex gap-2 mb-3">
                                    <input
                                        v-model="nuevoItemIncluye"
                                        type="text"
                                        placeholder="Agregar nuevo elemento..."
                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        @keyup.enter="agregarItemIncluye"
                                    />
                                    <button
                                        type="button"
                                        @click="agregarItemIncluye"
                                        :disabled="!nuevoItemIncluye.trim()"
                                        class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors"
                                    >
                                        <i class="pi pi-plus"></i>
                                    </button>
                                </div>
                                
                                <!-- Lista de items -->
                                <div class="space-y-2 max-h-40 overflow-y-auto">
                                    <div
                                        v-for="(item, index) in incluyeLista"
                                        :key="index"
                                        class="flex items-center justify-between bg-gray-50 p-2 rounded-md border"
                                    >
                                        <span class="flex-1">{{ item }}</span>
                                        <button
                                            type="button"
                                            @click="eliminarItemIncluye(index)"
                                            class="text-red-500 hover:text-red-700 p-1"
                                        >
                                            <i class="pi pi-times"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Mensaje cuando no hay items -->
                                <div v-if="incluyeLista.length === 0" class="text-gray-500 text-sm mt-2">
                                    Sin elementos agregados.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="w-full flex flex-col">
                        <div class="flex items-start gap-4">
                            <label for="no_incluye" class="w-24 mt-2">No incluye:</label>
                            <div class="flex-1">
                                <!-- Input para agregar nuevo item -->
                                <div class="flex gap-2 mb-3">
                                    <input
                                        v-model="nuevoItemNoIncluye"
                                        type="text"
                                        placeholder="Agregar nuevo elemento..."
                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        @keyup.enter="agregarItemNoIncluye"
                                    />
                                    <button
                                        type="button"
                                        @click="agregarItemNoIncluye"
                                        :disabled="!nuevoItemNoIncluye.trim()"
                                        class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors"
                                    >
                                        <i class="pi pi-plus"></i>
                                    </button>
                                </div>
                                
                                <!-- Lista de items -->
                                <div class="space-y-2 max-h-40 overflow-y-auto" v-if="noIncluyeLista.length > 0">
                                    <div
                                        v-for="(item, index) in noIncluyeLista"
                                        :key="index"
                                        class="flex items-center justify-between bg-gray-50 p-2 rounded-md border"
                                    >
                                        <span class="flex-1">{{ item }}</span>
                                        <button
                                            type="button"
                                            @click="eliminarItemNoIncluye(index)"
                                            class="text-red-500 hover:text-red-700 p-1"
                                        >
                                            <i class="pi pi-times"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Mensaje cuando no hay items (opcional para este campo) -->
                                <div v-if="noIncluyeLista.length === 0" class="text-gray-500 text-sm mt-2">
                                    Sin elementos. Este campo es opcional.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="punto_salida" class="w-24 flex items-center gap-1">
                                Punto de salida:
                                <span class="text-red-500 font-bold">*</span>
                            </label>
                            <InputText
                                v-model.trim="tour.punto_salida"
                                id="punto_salida"
                                name="punto_salida"
                                :maxlength="200"
                                :class="{
                                    'p-invalid':
                                        submitted && (!tour.punto_salida || tour.punto_salida.length < 5),
                                }"
                                class="flex-1"
                                @input="validatePuntoSalida"
                            />
                        </div>
                        <!-- Mensaje cuando tiene menos de 5 caracteres -->
                        <small
                            class="text-red-500 ml-28"
                            v-if="tour.punto_salida && tour.punto_salida.length < 5"
                            >Debe tener al menos 5 caracteres. Actual: {{ tour.punto_salida.length }}/5</small
                        >
                        <!-- Mensaje cuando está cerca del límite -->
                        <small
                            class="text-orange-500 ml-28"
                            v-if="tour.punto_salida && tour.punto_salida.length >= 180 && tour.punto_salida.length <= 200"
                            >Caracteres restantes: {{ 200 - tour.punto_salida.length }}</small
                        >
                        <!-- Mensaje de error cuando está vacío y se intentó enviar -->
                        <small
                            class="text-red-500 ml-28"
                            v-if="submitted && !tour.punto_salida"
                            >El punto de salida es obligatorio.</small
                        >
                        <!-- Mensaje de error cuando es menor a 5 caracteres al enviar -->
                        <small
                            class="text-red-500 ml-28"
                            v-if="submitted && tour.punto_salida && tour.punto_salida.length < 5"
                            >El punto de salida debe tener al menos 5 caracteres.</small
                        >
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label for="cupo_min" class="block mb-2"
                                >Cupo mínimo:</label
                            >
                            <InputNumber
                                v-model="tour.cupo_min"
                                id="cupo_min"
                                name="cupo_min"
                                :min="1"
                                :max="tour.cupo_max ? tour.cupo_max - 1 : 50"
                                :step="1"
                                showButtons
                                :useGrouping="false"
                                :class="{
                                    'p-invalid': tour.cupo_min && tour.cupo_max && tour.cupo_min >= tour.cupo_max,
                                }"
                                class="w-full"
                                @input="validateCupos"
                                placeholder="0"
                            />
                            <small
                                class="text-red-500 block text-xs mt-1"
                                v-if="tour.cupo_min && tour.cupo_max && tour.cupo_min >= tour.cupo_max"
                                >Debe ser menor al máximo</small
                            >
                        </div>
                        <div class="flex-1">
                            <label for="cupo_max" class="block mb-2"
                                >Cupo máximo:</label
                            >
                            <InputNumber
                                v-model="tour.cupo_max"
                                id="cupo_max"
                                name="cupo_max"
                                :min="tour.cupo_min ? tour.cupo_min + 1 : 1"
                                :max="100"
                                :step="1"
                                showButtons
                                :useGrouping="false"
                                :class="{
                                    'p-invalid': tour.cupo_min && tour.cupo_max && tour.cupo_max <= tour.cupo_min,
                                }"
                                class="w-full"
                                @input="validateCupos"
                                placeholder="0"
                            />
                            <small
                                class="text-red-500 block text-xs mt-1"
                                v-if="tour.cupo_min && tour.cupo_max && tour.cupo_max <= tour.cupo_min"
                                >Debe ser mayor al mínimo</small
                            >
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label for="fecha_salida" class="flex items-center gap-1 mb-2">
                                Fecha y hora de salida
                                <span class="text-red-500 font-bold">*</span>
                            </label>
                            <DatePicker
                                v-model="tour.fecha_salida"
                                id="fecha_salida"
                                name="fecha_salida"
                                showIcon
                                showTime
                                hourFormat="12"
                                dateFormat="yy-mm-dd"
                                :minDate="getMinDate()"
                                :maxDate="getMaxDateSalida()"
                                :class="{
                                    'p-invalid': 
                                        (submitted && !tour.fecha_salida) ||
                                        (tour.fecha_salida && !validateFechaSalida())
                                }"
                                class="w-full"
                                :manualInput="false"
                                @dateSelect="validateFechaSalida"
                                @input="validateFechaSalida"
                            />
                            <!-- Mensaje de requerido -->
                            <small
                                class="text-red-500 block text-xs mt-1"
                                v-if="submitted && !tour.fecha_salida"
                                >Fecha y hora de salida requerida.</small
                            >
                        </div>
                        <div class="flex-1">
                            <label for="horaRegresoCalendar" class="flex items-center gap-1 mb-2">
                                Fecha y hora regreso
                                <span class="text-red-500 font-bold">*</span>
                            </label>
                            <DatePicker
                                v-model="horaRegresoCalendar"
                                id="horaRegresoCalendar"
                                name="horaRegresoCalendar"
                                showIcon
                                showTime
                                hourFormat="12"
                                dateFormat="yy-mm-dd"
                                :minDate="getMinDateRegreso()"
                                :manualInput="false"
                                :class="{
                                    'p-invalid': 
                                        (submitted && !horaRegresoCalendar) ||
                                        (horaRegresoCalendar && !validateFechaRegreso())
                                }"
                                class="w-full"
                                @dateSelect="validateFechaRegreso"
                                @input="validateFechaRegreso"
                            />
                            <!-- Mensaje de requerido -->
                            <small
                                class="text-red-500 block text-xs mt-1"
                                v-if="submitted && !horaRegresoCalendar"
                                >Fecha y hora de regreso requerida.</small
                            >
                        </div>
                    </div>
                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="precio" class="w-24 flex items-center gap-1">
                                Precio:
                                <span class="text-red-500 font-bold">*</span>
                            </label>
                            <InputNumber
                                v-model="tour.precio"
                                id="precio"
                                name="precio"
                                mode="currency"
                                currency="USD"
                                :locale="'en-US'"
                                :min="0.01"
                                :max="999.99"
                                :maxFractionDigits="2"
                                :minFractionDigits="2"
                                :class="{
                                    'p-invalid':
                                        submitted &&
                                        (!tour.precio ||
                                            tour.precio <= 0 ||
                                            tour.precio > 999.99),
                                }"
                                class="flex-1"
                            />
                        </div>
                        <small
                            class="text-red-500 ml-28"
                            v-if="
                                submitted &&
                                (tour.precio == null ||
                                    tour.precio <= 0 ||
                                    tour.precio > 999.99)
                            "
                        >
                            El precio es obligatorio, debe ser mayor a 0 y menor
                            o igual a 999.99.
                        </small>
                    </div>
                    <!--Categoría-->
                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="categoria" class="w-24 flex items-center gap-1">
                                Categoría:
                                <span class="text-red-500 font-bold">*</span>
                            </label>
                            
                            <Select
                                v-model="tour.categoria"
                                :options="categoriasOptions"
                                optionLabel="label"
                                optionValue="value"
                                id="categoria"
                                name="categoria"
                                class="flex-1"
                                placeholder="Selecciona una categoría"
                                :class="{
                                    'p-invalid':
                                        submitted && !tour.categoria,
                                }"
                            />
                        </div>
                        <small
                            class="text-red-500 ml-28"
                            v-if="submitted && !tour.categoria"
                            >La categoría es obligatoria.</small
                        >
                    </div>

                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="transporte_id" class="w-24 flex items-center gap-1">
                                Tipo de transporte:
                                <span class="text-red-500 font-bold">*</span>
                            </label>
                            <Select
                                v-model="tour.transporte_id"
                                :options="tipoTransportes"
                                optionLabel="nombre"
                                optionValue="id"
                                id="transporte_id"
                                name="transporte_id"
                                class="flex-1"
                                placeholder="Selecciona un tipo de transporte"
                                :class="{
                                    'p-invalid':
                                        submitted && !tour.transporte_id,
                                }"
                            />
                        </div>
                        <small
                            class="text-red-500 ml-28"
                            v-if="submitted && !tour.transporte_id"
                            >El tipo de transporte es obligatorio.</small
                        >
                    </div>
                    <div class="w-full flex flex-col">
                        <div class="flex items-center gap-4">
                            <label for="imagenes" class="w-24 flex items-center gap-1">
                                Imágenes:
                                <span class="text-red-500 font-bold">*</span>
                            </label>
                            <div class="flex-1">
                                <input
                                    type="file"
                                    id="imagenes"
                                    name="imagenes[]"
                                    accept="image/*"
                                    multiple
                                    @change="onImageSelect"
                                    class="hidden"
                                    ref="fileInput"
                                />
                                <Button
                                    label="Seleccionar imágenes"
                                    icon="pi pi-plus"
                                    style="background-color: white !important; border: 1px solid #ef4444 !important; color: #ef4444 !important; padding: 0.5rem 1.5rem; border-radius: 0.375rem; transition: all 0.2s ease;"
                                    onmouseover="this.style.backgroundColor='#fef2f2'; this.style.borderColor='#dc2626'; this.style.color='#dc2626'"
                                    onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#ef4444'; this.style.color='#ef4444'"
                                    @click="$refs.fileInput.click()"
                                />
                            </div>
                        </div>
                        <small
                            class="text-red-500 ml-28"
                            v-if="submitted && imagenPreviewList.length === 0"
                            >Al menos una imagen es obligatoria.</small
                        >
                    </div>
                    <div class="flex gap-4 flex-wrap mt-4 ml-28">
                        <div
                            v-for="(img, index) in imagenPreviewList"
                            :key="index"
                            class="relative w-32 h-32"
                        >
                            <img
                                :src="
                                    img.startsWith('data:image')
                                        ? img
                                        : IMAGE_PATH + img
                                "
                                alt="Vista previa"
                                class="w-full h-full object-cover rounded border"
                            />
                            <button
                                @click="removeImage(index)"
                                class="absolute top-0 right-0 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 shadow"
                                style="transform: translate(50%, -50%)"
                            >
                                <i class="pi pi-times text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <template #footer>
                    <div class="flex justify-center gap-4 w-full">
                        <Button
                            label="Cancelar"
                            icon="pi pi-times"
                            style="background-color: white !important; border: 1px solid #10b981 !important; color: #10b981 !important; padding: 0.5rem 1.5rem; border-radius: 0.375rem; transition: all 0.2s ease;"
                            onmouseover="this.style.backgroundColor='#f0fdf4'; this.style.borderColor='#059669'; this.style.color='#059669'"
                            onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#10b981'; this.style.color='#10b981'"
                            text
                            @click="hideDialog"
                        />
                        <Button
                            :label="btnTitle"
                            icon="pi pi-check"
                            style="background-color: #ef4444 !important; color: white !important; border: none !important; padding: 0.5rem 1.5rem; border-radius: 0.375rem; transition: all 0.2s ease;"
                            onmouseover="this.style.backgroundColor='#dc2626'"
                            onmouseout="this.style.backgroundColor='#ef4444'"
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
                        >¿Eliminar el tour <b>{{ tour.nombre }}</b
                        >?</span
                    >
                </div>
                <template #footer>
                    <div class="flex justify-end gap-4 w-full">
                        <Button
                            label="No"
                            icon="pi pi-times"
                            style="background-color: white !important; border: 1px solid #10b981 !important; color: #10b981 !important; padding: 0.5rem 1.5rem; border-radius: 0.375rem; transition: all 0.2s ease;"
                            onmouseover="this.style.backgroundColor='#f0fdf4'; this.style.borderColor='#059669'; this.style.color='#059669'"
                            onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#10b981'; this.style.color='#10b981'"
                            text
                            @click="deleteDialog = false"
                        />
                        <Button
                            label="Sí"
                            icon="pi pi-check"
                            style="background-color: #ef4444 !important; color: white !important; border: none !important; padding: 0.5rem 1.5rem; border-radius: 0.375rem; transition: all 0.2s ease;"
                            onmouseover="this.style.backgroundColor='#dc2626'"
                            onmouseout="this.style.backgroundColor='#ef4444'"
                            @click="deleteTour"
                        />
                    </div>
                </template>
            </Dialog>

            <!-- Diálogo de confirmación para cambios no guardados -->
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
                            style="background-color: white !important; border: 1px solid #10b981 !important; color: #10b981 !important; padding: 0.4rem 1rem; border-radius: 0.375rem; transition: all 0.2s ease; font-size: 0.875rem;"
                            onmouseover="this.style.backgroundColor='#f0fdf4'; this.style.borderColor='#059669'; this.style.color='#059669'"
                            onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#10b981'; this.style.color='#10b981'"
                            text
                            @click="continueEditing"
                        />
                        <Button
                            label="Salir sin guardar"
                            icon="pi pi-sign-out"
                            size="small"
                            style="background-color: #ef4444 !important; color: white !important; border: none !important; padding: 0.4rem 1rem; border-radius: 0.375rem; transition: all 0.2s ease; font-size: 0.875rem;"
                            onmouseover="this.style.backgroundColor='#dc2626'"
                            onmouseout="this.style.backgroundColor='#ef4444'"
                            @click="closeDialogWithoutSaving"
                        />
                    </div>
                </template>
            </Dialog>

            <Dialog
                v-model:visible="showImageDialog"
                header="Imágenes del tour"
                :modal="true"
                :closable="false"
                :style="{ width: '700px' }"
            >
                <div
                    v-if="selectedImages.length"
                    class="flex flex-col items-center justify-center"
                >
                    <!-- Carousel for images -->
                    <Carousel
                        :value="selectedImages"
                        :numVisible="1"
                        :numScroll="1"
                        :circular="true"
                        v-model:page="carouselIndex"
                        class="w-full"
                        :showIndicators="selectedImages.length > 1"
                        :showNavigators="selectedImages.length > 1"
                        style="max-width: 610px"
                    >
                        <template #item="slotProps">
                            <div
                                class="flex justify-center items-center w-full h-96"
                            >
                                <img
                                    :src="slotProps.data"
                                    alt="Imagen tour"
                                    class="w-auto h-full max-h-96 object-contain rounded shadow"
                                />
                            </div>
                        </template>
                    </Carousel>
                </div>
                <div v-else class="text-center text-gray-500 py-8">
                    No hay imágenes para este tour.
                </div>
                <template #footer>
                    <div class="flex justify-center w-full">
                        <Button
                            label="Cerrar"
                            icon="pi pi-times"
                            style="background-color: white !important; border: 1px solid #10b981 !important; color: #10b981 !important; padding: 0.5rem 1.5rem; border-radius: 0.375rem; transition: all 0.2s ease;"
                            onmouseover="this.style.backgroundColor='#f0fdf4'; this.style.borderColor='#059669'; this.style.color='#059669'"
                            onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#10b981'; this.style.color='#10b981'"
                            @click="showImageDialog = false"
                        />
                    </div>
                </template>
            </Dialog>
        </div>
    </AuthenticatedLayout>
</template>