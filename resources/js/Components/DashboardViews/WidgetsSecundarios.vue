<template>
    <!-- COLUMNA IZQUIERDA: Widgets Secundarios - Responsive -->
    <div class="lg:space-y-4 grid grid-cols-2 lg:grid-cols-1 gap-3 lg:gap-4">
        <!-- Reservas Pendientes - Expandible -->
        <div class="p-3 sm:p-4 rounded-lg border-2 border-dashed cursor-pointer transform transition-all duration-200 hover:scale-105 hover:shadow-md active:scale-95"
            :class="widgets[0].color"
            @click="$emit('toggle-reservas-modal')">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">{{ widgets[0].title }}</p>
                    <p class="text-lg sm:text-xl font-bold text-gray-900 mt-1">{{ widgets[0].value }}</p>
                    <p class="text-xs text-gray-500 mt-1 hidden sm:block">Click para ver detalles</p>
                    <p class="text-xs text-gray-500 mt-1 sm:hidden">Tap detalles</p>
                </div>
                <i :class="widgets[0].icon + ' text-xl sm:text-2xl ' + widgets[0].iconColor + ' flex-shrink-0 ml-2'"></i>
            </div>
        </div>

        <!-- Productos Stock Bajo - Expandible -->
        <div class="p-3 sm:p-4 rounded-lg border-2 border-dashed cursor-pointer transform transition-all duration-200 hover:scale-105 hover:shadow-md active:scale-95"
            :class="widgets[1].color"
            @click="$emit('toggle-stock-modal')">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">{{ widgets[1].title }}</p>
                    <p class="text-lg sm:text-xl font-bold text-gray-900 mt-1">{{ widgets[1].value }}</p>
                    <p class="text-xs text-gray-500 mt-1 hidden sm:block">Click para ver productos</p>
                    <p class="text-xs text-gray-500 mt-1 sm:hidden">Ver productos</p>
                </div>
                <i :class="widgets[1].icon + ' text-xl sm:text-2xl ' + widgets[1].iconColor + ' flex-shrink-0 ml-2'"></i>
            </div>
        </div>

        <!-- Valor Inventario - No clickeable -->
        <div class="p-3 sm:p-4 rounded-lg border-2 border-dashed col-span-2 lg:col-span-1"
            :class="widgets[2].color">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">{{ widgets[2].title }}</p>
                    <!-- Valor responsivo para inventario -->
                    <p class="text-lg sm:text-xl font-bold text-gray-900 mt-1">
                        <span class="hidden sm:inline">{{ widgets[2].value }}</span>
                        <span class="sm:hidden">{{ formatValueForMobile(widgets[2].value, dashboardData.inventario) }}</span>
                    </p>
                </div>
                <i :class="widgets[2].icon + ' text-xl sm:text-2xl ' + widgets[2].iconColor + ' flex-shrink-0 ml-2'"></i>
            </div>
        </div>

        <!-- Clientes Activos - No clickeable (solo visible en lg+) -->
        <div class="hidden lg:block p-3 sm:p-4 rounded-lg border-2 border-dashed"
            :class="widgets[3].color">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">{{ widgets[3].title }}</p>
                    <p class="text-lg sm:text-xl font-bold text-gray-900 mt-1">{{ widgets[3].value }}</p>
                </div>
                <i :class="widgets[3].icon + ' text-xl sm:text-2xl ' + widgets[3].iconColor + ' flex-shrink-0 ml-2'"></i>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    widgets: {
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

defineEmits(['toggle-reservas-modal', 'toggle-stock-modal']);
</script>
