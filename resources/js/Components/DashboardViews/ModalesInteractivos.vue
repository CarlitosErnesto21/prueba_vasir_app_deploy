<template>
    <!-- 🚀 MODALES INTERACTIVOS - Responsive -->
    
    <!-- Modal: Reservas Pendientes -->
    <div v-if="showReservasPendientesModal" 
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
        @click="$emit('close-reservas-modal')">
        <div class="bg-white rounded-lg p-4 sm:p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto"
            @click.stop>
            <div class="mb-3 sm:mb-4">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900">
                    <i class="pi pi-clock text-amber-500 mr-2"></i>
                    <span class="hidden sm:inline">Reservas Pendientes</span>
                    <span class="sm:hidden">Pendientes</span>
                </h3>
            </div>
            
            <div v-if="dashboardData.reservas && dashboardData.reservas.filter(r => r.estado && r.estado.toLowerCase() === 'pendiente').length > 0"
                class="space-y-2 sm:space-y-3">
                <div v-for="reserva in dashboardData.reservas.filter(r => r.estado && r.estado.toLowerCase() === 'pendiente')" 
                    :key="reserva.id"
                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-3 sm:p-4 bg-amber-50 rounded-lg border border-amber-200 hover:bg-amber-100 transition-colors space-y-2 sm:space-y-0">
                    <div class="flex-1">
                        <div class="flex items-start sm:items-center space-x-3">
                            <div class="p-1.5 sm:p-2 bg-amber-100 rounded-full flex-shrink-0">
                                <i class="pi pi-calendar text-amber-600 text-sm sm:text-base"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-medium text-gray-900 text-sm sm:text-base truncate">{{ reserva.entidad_nombre || 'Tour no especificado' }}</p>
                                <p class="text-xs sm:text-sm text-gray-600 truncate">{{ reserva.cliente?.nombres || 'Cliente no asignado' }}</p>
                                <p class="text-xs text-gray-500">{{ new Date(reserva.fecha_reserva || reserva.fecha).toLocaleDateString('es-ES') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="text-left sm:text-right pl-8 sm:pl-0">
                        <p class="font-semibold text-gray-900 text-sm sm:text-base">${{ Number(reserva.total || 0).toLocaleString('es-ES') }}</p>
                        <p class="text-xs text-amber-600">{{ reserva.mayores_edad || 0 }} adultos, {{ reserva.menores_edad || 0 }} niños</p>
                    </div>
                </div>
            </div>
            
            <div v-else class="text-center py-6 sm:py-8">
                <i class="pi pi-check-circle text-green-400 text-3xl sm:text-4xl mb-3"></i>
                <p class="text-gray-500 text-sm sm:text-base">¡Excelente! No hay reservas pendientes</p>
            </div>
            
            <div class="flex justify-end gap-2 mt-4 sm:mt-6">
                <button 
                    type="button" 
                    class="bg-white hover:bg-green-100 text-green-600 border border-green-600 px-6 py-2 rounded-md transition-all duration-200 ease-in-out flex items-center gap-2" 
                    @click="$emit('close-reservas-modal')"
                >
                    <FontAwesomeIcon :icon="faTimes" class="h-5 text-green-600" />
                    Cancelar
                </button>
                <Link 
                    href="/gestion-reserva-tours"
                    class="bg-red-500 hover:bg-red-700 text-white border-none px-6 py-2 rounded-md transition-all duration-200 ease-in-out flex items-center gap-2"
                >
                    <FontAwesomeIcon :icon="faCheck" class="h-5 text-white" />
                    Ver Reservas
                </Link>
            </div>
        </div>
    </div>

    <!-- Modal: Productos Stock Bajo -->
    <div v-if="showProductosStockBajoModal" 
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
        @click="$emit('close-stock-modal')">
        <div class="bg-white rounded-lg p-4 sm:p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto"
            @click.stop>
            <div class="mb-3 sm:mb-4">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900">
                    <i class="pi pi-exclamation-triangle text-red-500 mr-2"></i>
                    <span class="hidden sm:inline">Productos con Stock Bajo</span>
                    <span class="sm:hidden">Stock Bajo</span>
                </h3>
            </div>
            
            <div v-if="dashboardData.stockBajo && dashboardData.stockBajo.length > 0"
                class="space-y-2 sm:space-y-3">
                <div v-for="producto in dashboardData.stockBajo" 
                    :key="producto.id"
                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-3 sm:p-4 bg-red-50 rounded-lg border border-red-200 hover:bg-red-100 transition-colors space-y-2 sm:space-y-0">
                    <div class="flex items-start sm:items-center space-x-3">
                        <div class="p-1.5 sm:p-2 bg-red-100 rounded-full flex-shrink-0">
                            <i class="pi pi-box text-red-600 text-sm sm:text-base"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-gray-900 text-sm sm:text-base truncate">{{ producto.nombre }}</p>
                            <p class="text-xs sm:text-sm text-gray-600 truncate">Categoría: {{ producto.categoria?.nombre || 'Sin categoría' }}</p>
                        </div>
                    </div>
                    <div class="text-left sm:text-right pl-8 sm:pl-0">
                        <div class="flex flex-col sm:flex-row sm:items-center space-y-1 sm:space-y-0 sm:space-x-2">
                            <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">
                                Stock: {{ producto.stock_actual }}
                            </span>
                            <span class="text-xs text-gray-500">
                                Min: {{ producto.stock_minimo }}
                            </span>
                        </div>
                        <p class="text-sm font-semibold text-gray-900 mt-1">
                            ${{ Number(producto.precio || 0).toLocaleString('es-ES') }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div v-else class="text-center py-6 sm:py-8">
                <i class="pi pi-check-circle text-green-400 text-3xl sm:text-4xl mb-3"></i>
                <p class="text-gray-500 text-sm sm:text-base">¡Perfecto! Todos los productos tienen stock suficiente</p>
            </div>
            
            <div class="flex justify-end gap-2 mt-4 sm:mt-6">
                <button 
                    type="button" 
                    class="bg-white hover:bg-green-100 text-green-600 border border-green-600 px-6 py-2 rounded-md transition-all duration-200 ease-in-out flex items-center gap-2" 
                    @click="$emit('close-stock-modal')"
                >
                    <FontAwesomeIcon :icon="faTimes" class="h-5 text-green-600" />
                    Cancelar
                </button>
                <Link 
                    href="/productos"
                    class="bg-red-500 hover:bg-red-700 text-white border-none px-6 py-2 rounded-md transition-all duration-200 ease-in-out flex items-center gap-2"
                >
                    <FontAwesomeIcon :icon="faCheck" class="h-5 text-white" />
                    Gestionar Inventario
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faTimes, faCheck } from '@fortawesome/free-solid-svg-icons';

defineProps({
    showReservasPendientesModal: {
        type: Boolean,
        required: true
    },
    showProductosStockBajoModal: {
        type: Boolean,
        required: true
    },
    dashboardData: {
        type: Object,
        required: true
    }
});

defineEmits(['close-reservas-modal', 'close-stock-modal']);
</script>
