import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue'
import { router } from '@inertiajs/vue3'

export function useUnsavedChanges(initialData, saveCallback) {
    // Estados reactivos
    const hasUnsavedChanges = ref(false)
    const showUnsavedModal = ref(false)
    const currentFormData = ref({})
    const originalData = ref({})
    
    // Función para inicializar los datos
    const initializeData = (data) => {
        // Formatear datos según el tipo de configuración
        let formattedData = {}
        
        // Para configuración general
        if (data.systemName !== undefined || data.system_name !== undefined) {
            formattedData = {
                systemName: data.systemName || data.system_name || '',
                version: data.version || '',
                contactEmail: data.contactEmail || data.contact_email || '',
                contactPhone: data.contactPhone || data.contact_phone || '',
                ...data
            }
        }
        // Para configuración de seguridad
        else if (data.requireEmailVerification !== undefined || data.enableTwoFactor !== undefined) {
            formattedData = {
                requireEmailVerification: data.requireEmailVerification || false,
                enableTwoFactor: data.enableTwoFactor || false,
                sessionTimeout: data.sessionTimeout || 60,
                ...data
            }
        }
        // Para configuración de notificaciones
        else if (data.emailNotifications !== undefined || data.smsNotifications !== undefined) {
            formattedData = {
                emailNotifications: data.emailNotifications || false,
                smsNotifications: data.smsNotifications || false,
                smtpServer: data.smtpServer || '',
                ...data
            }
        }
        // Para configuración corporativa
        else if (data.description !== undefined || data.mission !== undefined || data.vision !== undefined) {
            formattedData = {
                description: data.description || '',
                mission: data.mission || '',
                vision: data.vision || '',
                ...data
            }
        }
        // Para otros casos, usar directamente
        else {
            formattedData = { ...data }
        }
        
        originalData.value = JSON.parse(JSON.stringify(formattedData))
        currentFormData.value = JSON.parse(JSON.stringify(formattedData))
        hasUnsavedChanges.value = false
        
        // Asegurar que el modal esté cerrado al inicializar
        showUnsavedModal.value = false
        pendingNavigation.value = null
    }

    // Función para detectar cambios
    const checkForChanges = () => {
        const hasChanges = JSON.stringify(currentFormData.value) !== JSON.stringify(originalData.value)
        hasUnsavedChanges.value = hasChanges
        return hasChanges
    }

    // Función para guardar cambios
    const saveChanges = async () => {
        try {
            if (saveCallback) {
                await saveCallback(currentFormData.value)
                // Solo actualizar originalData si el guardado fue exitoso
                originalData.value = JSON.parse(JSON.stringify(currentFormData.value))
                hasUnsavedChanges.value = false
            }
        } catch (error) {
            console.error('Error al guardar:', error)
            throw error
        }
    }

    // Función para restablecer cambios
    const resetChanges = () => {
        currentFormData.value = JSON.parse(JSON.stringify(originalData.value))
        hasUnsavedChanges.value = false
    }

    // Función para confirmar salida sin guardar
    const confirmExit = () => {
        showUnsavedModal.value = false
        hasUnsavedChanges.value = false
        
        // Solo manejar navegación de Inertia
        if (pendingNavigation.value) {
            const navigation = pendingNavigation.value
            pendingNavigation.value = null
            router.visit(navigation.url, navigation.options)
        }
    }

    // Función para cancelar salida
    const cancelExit = () => {
        showUnsavedModal.value = false
        pendingNavigation.value = null
    }

    // Variable para almacenar la navegación pendiente
    const pendingNavigation = ref(null)

    // Solo interceptor para navegación de Inertia (no beforeunload)
    const removeInertiaListener = router.on('before', (event) => {
        if (hasUnsavedChanges.value) {
            // No bloquear peticiones POST al mismo endpoint (cuando estamos guardando)
            const currentUrl = window.location.pathname
            const targetUrl = event.detail.visit.url
            const isPostRequest = event.detail.visit.method === 'post'
            const isSameEndpoint = targetUrl.includes('settings')
            
            // Si es una petición POST a settings, permitir que continúe (es un guardado)
            if (isPostRequest && isSameEndpoint) {
                return true
            }
            
            // Para otras navegaciones, mostrar el modal
            pendingNavigation.value = {
                url: event.detail.visit.url,
                options: {
                    method: event.detail.visit.method,
                    data: event.detail.visit.data,
                    replace: event.detail.visit.replace,
                    preserveScroll: event.detail.visit.preserveScroll,
                    preserveState: event.detail.visit.preserveState,
                    only: event.detail.visit.only,
                    headers: event.detail.visit.headers,
                    errorBag: event.detail.visit.errorBag,
                    forceFormData: event.detail.visit.forceFormData,
                    queryStringArrayFormat: event.detail.visit.queryStringArrayFormat
                }
            }
            
            showUnsavedModal.value = true
            return false // Cancelar la navegación
        }
    })

    // Computed para el estado del botón guardar
    const canSave = computed(() => hasUnsavedChanges.value)
    const canReset = computed(() => hasUnsavedChanges.value)

    // Observar cambios en initialData
    watch(() => initialData, (newData) => {
        if (newData) {
            initializeData(newData.value || newData)
        }
    }, { immediate: true, deep: true })

    // Observar cambios en currentFormData para detectar modificaciones
    watch(currentFormData, () => {
        checkForChanges()
    }, { deep: true })

    // Lifecycle hooks
    onMounted(() => {
        // Ya no necesitamos beforeunload listener
    })

    onBeforeUnmount(() => {
        removeInertiaListener()
    })

    return {
        // Estados
        hasUnsavedChanges,
        showUnsavedModal,
        currentFormData,
        originalData,
        pendingNavigation,
        
        // Computed
        canSave,
        canReset,
        
        // Métodos
        initializeData,
        checkForChanges,
        saveChanges,
        resetChanges,
        confirmExit,
        cancelExit
    }
}
