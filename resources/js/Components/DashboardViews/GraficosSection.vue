<template>
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 sm:gap-6">
        <!-- Gráfico PIE: Estado del inventario -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 sm:p-6">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Inventario por Estado</h3>
            <div class="h-48 sm:h-64">
                <Chart v-if="chartDataPie" 
                    type="pie" 
                    :data="chartDataPie" 
                    :options="chartOptionsPie" 
                    class="w-full h-full" />
                <div v-else class="flex items-center justify-center h-full">
                    <p class="text-gray-500 text-xs sm:text-sm">No hay datos de inventario</p>
                </div>
            </div>
        </div>

        <!-- Gráfico BAR: Reservas por estado -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 sm:p-6">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Reservas por Estado</h3>
            <div class="h-48 sm:h-64">
                <Chart v-if="chartDataBar" 
                    type="bar" 
                    :data="chartDataBar" 
                    :options="chartOptionsBar" 
                    class="w-full h-full" />
                <div v-else class="flex items-center justify-center h-full">
                    <p class="text-gray-500 text-xs sm:text-sm">No hay datos de reservas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Top 5 Tours Más Reservados -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 sm:p-6 mt-4 sm:mt-6">
        <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">
            <i class="pi pi-star text-yellow-500 mr-2"></i>
            <span class="hidden sm:inline">Top 5 Tours Más Reservados</span>
            <span class="sm:hidden">Top Tours</span>
        </h3>
        <div class="h-48 sm:h-64">
            <Chart v-if="chartDataDoughnut" 
                type="doughnut" 
                :data="chartDataDoughnut" 
                :options="chartOptionsDoughnut" 
                class="w-full h-full" />
            <div v-else class="flex items-center justify-center h-full">
                <p class="text-gray-500 text-xs sm:text-sm">No hay reservas registradas</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import Chart from 'primevue/chart';

defineProps({
    chartDataPie: {
        type: Object,
        required: true
    },
    chartDataBar: {
        type: Object,
        required: true
    },
    chartDataDoughnut: {
        type: Object,
        required: true
    },
    chartOptionsPie: {
        type: Object,
        required: true
    },
    chartOptionsBar: {
        type: Object,
        required: true
    },
    chartOptionsDoughnut: {
        type: Object,
        required: true
    }
});
</script>
