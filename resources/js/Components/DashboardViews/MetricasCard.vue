<template>
    <!-- 📊 HEADER CON MÉTRICAS PRINCIPALES - Responsive Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Ventas Hoy - No clickeable -->
        <div class="bg-gradient-to-r rounded-lg shadow-sm border border-gray-100 p-3 sm:p-4"
            :class="metrics[0].color">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-white text-xs sm:text-sm font-medium opacity-90 truncate">{{ metrics[0].label }}</p>
                    <p class="text-white text-lg sm:text-2xl font-bold mt-1">{{ metrics[0].value }}</p>
                </div>
                <div class="p-1.5 sm:p-2 bg-white bg-opacity-20 rounded-lg flex-shrink-0 ml-2">
                    <i :class="metrics[0].icon + ' text-white text-lg sm:text-xl'"></i>
                </div>
            </div>
        </div>

        <!-- Ingresos Hoy - No clickeable -->
        <div class="bg-gradient-to-r rounded-lg shadow-sm border border-gray-100 p-3 sm:p-4"
            :class="metrics[1].color">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-white text-xs sm:text-sm font-medium opacity-90 truncate">{{ metrics[1].label }}</p>
                    <!-- Valor responsivo para ingresos -->
                    <p class="text-white text-lg sm:text-2xl font-bold mt-1">
                        <span class="hidden sm:inline">{{ metrics[1].value }}</span>
                        <span class="sm:hidden">{{ formatValueForMobile(metrics[1].value, dashboardData.inventario) }}</span>
                    </p>
                </div>
                <div class="p-1.5 sm:p-2 bg-white bg-opacity-20 rounded-lg flex-shrink-0 ml-2">
                    <i :class="metrics[1].icon + ' text-white text-lg sm:text-xl'"></i>
                </div>
            </div>
        </div>

        <!-- Reservas del Mes - Clickeable -->
        <Link href="/gestion-reserva-tours" 
            class="bg-gradient-to-r rounded-lg shadow-sm border border-gray-100 p-3 sm:p-4 cursor-pointer transform transition-all duration-200 hover:scale-105 hover:shadow-lg active:scale-95 block"
            :class="metrics[2].color">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-white text-xs sm:text-sm font-medium opacity-90 truncate">{{ metrics[2].label }}</p>
                    <p class="text-white text-lg sm:text-2xl font-bold mt-1">{{ metrics[2].value }}</p>
                    <p class="text-white text-xs opacity-80 mt-1 hidden sm:block">Click para ver todas</p>
                    <p class="text-white text-xs opacity-80 mt-1 sm:hidden">Tap aquí</p>
                </div>
                <div class="p-1.5 sm:p-2 bg-white bg-opacity-20 rounded-lg flex-shrink-0 ml-2">
                    <i :class="metrics[2].icon + ' text-white text-lg sm:text-xl'"></i>
                </div>
            </div>
        </Link>

        <!-- Tours Activos - Clickeable -->
        <Link href="/tours"
            class="bg-gradient-to-r rounded-lg shadow-sm border border-gray-100 p-3 sm:p-4 cursor-pointer transform transition-all duration-200 hover:scale-105 hover:shadow-lg active:scale-95 block"
            :class="metrics[3].color">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-white text-xs sm:text-sm font-medium opacity-90 truncate">{{ metrics[3].label }}</p>
                    <p class="text-white text-lg sm:text-2xl font-bold mt-1">{{ metrics[3].value }}</p>
                    <p class="text-white text-xs opacity-80 mt-1 hidden sm:block">Click para gestionar</p>
                    <p class="text-white text-xs opacity-80 mt-1 sm:hidden">Tap aquí</p>
                </div>
                <div class="p-1.5 sm:p-2 bg-white bg-opacity-20 rounded-lg flex-shrink-0 ml-2">
                    <i :class="metrics[3].icon + ' text-white text-lg sm:text-xl'"></i>
                </div>
            </div>
        </Link>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    metrics: {
        type: Array,
        required: true
    },
    dashboardData: {
        type: Object,
        required: true
    },
    formatValueForMobile: {
        type: Function,
        required: true
    }
});
</script>
