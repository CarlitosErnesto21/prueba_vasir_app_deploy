<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from "vue";
import Chart from 'primevue/chart';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faTimes, faCheck } from '@fortawesome/free-solid-svg-icons';
import axios from 'axios';

// Importar componentes separados
import LoadingState from '@/Components/DashboardViews/LoadingState.vue';
import MetricasCard from '@/Components/DashboardViews/MetricasCard.vue';
import WidgetsSecundarios from '@/Components/DashboardViews/WidgetsSecundarios.vue';
import GraficosSection from '@/Components/DashboardViews/GraficosSection.vue';
import ActividadReciente from '@/Components/DashboardViews/ActividadReciente.vue';
import ModalesInteractivos from '@/Components/DashboardViews/ModalesInteractivos.vue';

const chartDataPie = ref();
const chartDataBar = ref();
const chartDataDoughnut = ref();
const chartOptionsPie = ref();
const chartOptionsBar = ref();
const chartOptionsDoughnut = ref();

// ✅ DATOS REALES de tu sistema
const loading = ref(true);
const dashboardData = ref({});

// 📊 Métricas DINÁMICAS con datos reales - Layout más compacto
const metrics = ref([
    { 
        label: 'Ventas Hoy', 
        value: '0', 
        icon: 'pi pi-shopping-cart', 
        color: 'from-emerald-500 to-emerald-400', 
        text: 'text-emerald-900',
        key: 'ventas_hoy',
        category: 'ventas',
        description: 'Ventas del día'
    },
    { 
        label: 'Ingresos Hoy', 
        value: '$0', 
        icon: 'pi pi-dollar', 
        color: 'from-blue-500 to-blue-400', 
        text: 'text-blue-900',
        key: 'ingresos_hoy',
        category: 'ventas',
        description: 'Ingresos del día'
    },
    { 
        label: 'Reservas del Mes', 
        value: '0', 
        icon: 'pi pi-calendar-plus', 
        color: 'from-purple-500 to-purple-400', 
        text: 'text-purple-900',
        key: 'reservas_mes',
        category: 'reservas',
        description: 'Reservas este mes'
    },
    { 
        label: 'Tours Activos', 
        value: '0', 
        icon: 'pi pi-map-marker', 
        color: 'from-orange-500 to-orange-400', 
        text: 'text-orange-900',
        key: 'tours_activos',
        category: 'reservas',
        description: 'Tours disponibles'
    }
]);

// 📈 Widgets adicionales para el dashboard
const widgets = ref([
    {
        title: 'Reservas Pendientes',
        value: '0',
        icon: 'pi pi-clock',
        color: 'bg-amber-50 border-amber-200',
        iconColor: 'text-amber-600',
        key: 'reservas_pendientes'
    },
    {
        title: 'Productos Stock Bajo',
        value: '0',
        icon: 'pi pi-exclamation-triangle',
        color: 'bg-red-50 border-red-200',
        iconColor: 'text-red-600',
        key: 'productos_stock_bajo'
    },
    {
        title: 'Valor Inventario',
        value: '$0',
        icon: 'pi pi-wallet',
        color: 'bg-green-50 border-green-200',
        iconColor: 'text-green-600',
        key: 'valor_total_inventario'
    },
    {
        title: 'Clientes Activos',
        value: '0',
        icon: 'pi pi-users',
        color: 'bg-indigo-50 border-indigo-200',
        iconColor: 'text-indigo-600',
        key: 'clientes_activos'
    }
]);

// 🎯 Estados para interactividad
const showReservasPendientesModal = ref(false);
const showProductosStockBajoModal = ref(false);

const toggleReservasPendientesModal = () => {
    showReservasPendientesModal.value = !showReservasPendientesModal.value;
};

const toggleProductosStockBajoModal = () => {
    showProductosStockBajoModal.value = !showProductosStockBajoModal.value;
};

// ✅ FUNCIÓN para formatear valores en mobile
const formatValueForMobile = (value, originalData = null) => {
    // Si es el valor del inventario, usar el valor original sin formatear
    if (value.includes('$')) {
        let numericValue;
        
        // Si tenemos acceso a los datos originales, usarlos
        if (originalData && originalData.valor_total_inventario) {
            numericValue = parseFloat(originalData.valor_total_inventario);
        } else {
            // Intentar extraer el número del valor formateado
            numericValue = parseFloat(value.replace(/[$,.\s]/g, ''));
        }
        
        if (isNaN(numericValue)) {
            return '$0';
        }
        
        if (numericValue >= 1000000) {
            return '$' + (numericValue / 1000000).toFixed(1) + 'M';
        } else if (numericValue >= 1000) {
            return '$' + (numericValue / 1000).toFixed(0) + 'k';
        } else {
            return '$' + numericValue.toFixed(0);
        }
    }
    return value;
};

// ✅ FUNCIÓN para obtener datos del dashboard
const fetchDashboardData = async () => {
    loading.value = true;
    
    try {
        // 📊 Obtener datos del resumen de inventario
        const inventarioResponse = await axios.get('/api/inventario/resumen', {
            headers: { 'Accept': 'application/json' }
        });
        
        // 🛍️ Obtener ventas recientes
        const ventasResponse = await axios.get('/api/ventas', {
            headers: { 'Accept': 'application/json' }
        });
        
        // 📦 Obtener productos con stock bajo
        const stockBajoResponse = await axios.get('/api/inventario/stock-bajo', {
            headers: { 'Accept': 'application/json' }
        });
        
        // 🗓️ Obtener reservas con relaciones incluidas
        const reservasResponse = await axios.get('/api/reservas?per_page=50', {
            headers: { 'Accept': 'application/json' }
        });
        
        // 📈 Obtener resumen de reservas
        const resumenReservasResponse = await axios.get('/api/reservas/resumen', {
            headers: { 'Accept': 'application/json' }
        });
        
        // 🗺️ Obtener tours disponibles
        const toursResponse = await axios.get('/api/tours', {
            headers: { 'Accept': 'application/json' }
        });
        
        // ✅ Extracción de datos
        const inventarioData = inventarioResponse.data.data || inventarioResponse.data;
        const ventasData = ventasResponse.data.data || ventasResponse.data || [];
        const stockBajoData = stockBajoResponse.data.data || stockBajoResponse.data || [];
        const reservasData = reservasResponse.data.data || reservasResponse.data || [];
        const resumenReservasData = resumenReservasResponse.data.data || resumenReservasResponse.data || [];
        const toursData = toursResponse.data.data || toursResponse.data || [];
        
        // 🔄 Actualizar métricas y gráficos
        updateMetrics(inventarioData, ventasData, reservasData, resumenReservasData, toursData);
        updateCharts(inventarioData, ventasData, stockBajoData, reservasData, resumenReservasData);
        
        dashboardData.value = {
            inventario: inventarioData,
            ventas: ventasData,
            stockBajo: stockBajoData,
            reservas: reservasData,
            resumenReservas: resumenReservasData,
            tours: toursData
        };
        
    } catch (error) {
        console.error('Error al cargar datos del dashboard:', error);
        // En caso de error, usar valores por defecto
        updateMetrics({}, [], [], [], []);
        updateCharts({}, [], [], [], []);
    } finally {
        loading.value = false;
    }
};

// 🔄 FUNCIÓN para actualizar métricas
const updateMetrics = (inventarioData, ventasData, reservasData, resumenReservasData, toursData) => {
    const ventas = Array.isArray(ventasData) ? ventasData : [];
    const reservas = Array.isArray(reservasData) ? reservasData : [];
    const tours = Array.isArray(toursData) ? toursData : [];
    
    // Calcular ventas de hoy
    const hoy = new Date().toISOString().split('T')[0];
    const ventasHoy = ventas.filter(venta => {
        if (!venta.fecha) return false;
        const fechaVenta = venta.fecha.split('T')[0];
        return fechaVenta === hoy;
    }).length;
    
    // Calcular ingresos de hoy de ventas
    const ingresosHoyVentas = ventas.filter(venta => {
        if (!venta.fecha) return false;
        const fechaVenta = venta.fecha.split('T')[0];
        return fechaVenta === hoy;
    }).reduce((total, venta) => total + (parseFloat(venta.total) || 0), 0);
    
    // Calcular reservas del mes actual
    const mesActual = new Date().getMonth();
    const añoActual = new Date().getFullYear();
    const reservasDelMes = reservas.filter(reserva => {
        if (!reserva.fecha_reserva && !reserva.fecha) return false;
        const fechaReserva = new Date(reserva.fecha_reserva || reserva.fecha);
        return fechaReserva.getMonth() === mesActual && fechaReserva.getFullYear() === añoActual;
    }).length;
    
    // Tours activos (disponibles)
    const toursActivos = tours.filter(tour => 
        !tour.estado || 
        tour.estado.toLowerCase() === 'activo' || 
        tour.estado.toLowerCase() === 'disponible'
    ).length;
    
    // ✅ Actualizar valores principales
    metrics.value[0].value = ventasHoy.toString();
    metrics.value[1].value = `$${ingresosHoyVentas.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    metrics.value[2].value = reservasDelMes.toString();
    metrics.value[3].value = toursActivos.toString();
    
    // ✅ Actualizar widgets adicionales
    const reservasPendientes = reservas.filter(reserva => 
        reserva.estado && reserva.estado.toLowerCase() === 'pendiente'
    ).length;
    const clientesActivos = new Set(reservas.map(r => r.cliente?.id).filter(id => id)).size;
    
    widgets.value[0].value = reservasPendientes.toString();
    widgets.value[1].value = (inventarioData.productos_stock_bajo || 0).toString();
    widgets.value[2].value = `$${Number(inventarioData.valor_total_inventario || 0).toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    widgets.value[3].value = clientesActivos.toString();
};

// 📈 FUNCIÓN para actualizar gráficos
const updateCharts = (inventarioData, ventasData, stockBajoData, reservasData, resumenReservasData) => {
    const totalProductos = inventarioData.total_productos || 0;
    const stockBajo = inventarioData.productos_stock_bajo || 0;
    const agotados = inventarioData.productos_agotados || 0;
    const disponibles = inventarioData.productos_disponibles || Math.max(0, totalProductos - stockBajo - agotados);
    const ventas = Array.isArray(ventasData) ? ventasData : [];
    const reservas = Array.isArray(reservasData) ? reservasData : [];
    
    // 🥧 Gráfico PIE: Estado del inventario
    chartDataPie.value = {
        labels: ['Productos Disponibles', 'Stock Bajo', 'Agotados'],
        datasets: [{
            label: 'Estado del Inventario',
            data: [disponibles, stockBajo, agotados],
            backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
            hoverBackgroundColor: ['#34d399', '#fbbf24', '#f87171']
        }]
    };
    
    // 📊 Gráfico BAR: Reservas por estado
    const reservasPorEstado = reservas.reduce((acc, reserva) => {
        const estado = reserva.estado || 'Sin estado';
        acc[estado] = (acc[estado] || 0) + 1;
        return acc;
    }, {});
    
    if (Object.keys(reservasPorEstado).length === 0) {
        reservasPorEstado['Sin reservas'] = 0;
    }
    
    chartDataBar.value = {
        labels: Object.keys(reservasPorEstado),
        datasets: [{
            label: 'Reservas por Estado',
            data: Object.values(reservasPorEstado),
            backgroundColor: ['#f59e0b', '#10b981', '#ef4444', '#3b82f6']
        }]
    };
    
    // 🍩 Gráfico DOUGHNUT: Tours más reservados
    const toursReservados = {};
    
    // Usar resumenReservasData como fuente principal para tours
    if (resumenReservasData && resumenReservasData.length > 0) {
        resumenReservasData.forEach(resumen => {
            if (resumen.tipo === 'tours' && resumen.nombre) {
                const total = (resumen.total_pendientes || 0) + (resumen.total_confirmadas || 0);
                if (total > 0) {
                    // Truncar nombres muy largos para mejor visualización
                    const nombreCorto = resumen.nombre.length > 30 
                        ? resumen.nombre.substring(0, 27) + '...' 
                        : resumen.nombre;
                    toursReservados[nombreCorto] = total;
                }
            }
        });
    }
    
    // Si no hay datos en resumen, usar reservasData como fallback
    if (Object.keys(toursReservados).length === 0 && reservasData && reservasData.length > 0) {
        reservasData.forEach(reserva => {
            if (reserva.entidad_nombre) {
                const nombreCorto = reserva.entidad_nombre.length > 30 
                    ? reserva.entidad_nombre.substring(0, 27) + '...' 
                    : reserva.entidad_nombre;
                toursReservados[nombreCorto] = (toursReservados[nombreCorto] || 0) + 1;
            }
        });
    }
    
    let topTours = Object.entries(toursReservados)
        .sort(([,a], [,b]) => b - a)
        .slice(0, 5);
    
    if (topTours.length === 0) {
        topTours = [['Sin reservas', 0]];
    }
    
    chartDataDoughnut.value = {
        labels: topTours.map(([nombre]) => nombre),
        datasets: [{
            label: 'Tours Más Reservados',
            data: topTours.map(([, cantidad]) => cantidad),
            backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']
        }]
    };
};

// Opciones de gráficos
const setChartOptionsPie = () => ({
    maintainAspectRatio: false,
    plugins: {
        title: {
            display: true,
            text: 'Estado del Inventario',
            font: { size: 18 }
        },
        legend: {
            display: true,
            position: 'bottom',
            labels: {
                usePointStyle: true,
                color: '#374151'
            }
        },
        tooltip: {
            callbacks: {
                label: function(context) {
                    return `${context.label}: ${context.parsed} productos`;
                }
            }
        }
    }
});

const setChartOptionsBar = () => ({
    maintainAspectRatio: false,
    plugins: {
        title: {
            display: true,
            text: 'Reservas por Estado',
            font: { size: 18 }
        },
        legend: {
            display: false
        },
        tooltip: {
            callbacks: {
                label: function(context) {
                    return `${context.label}: ${context.parsed.y} reservas`;
                }
            }
        }
    },
    scales: {
        x: {
            ticks: { color: '#374151' },
            grid: { color: '#e5e7eb' }
        },
        y: {
            beginAtZero: true,
            ticks: { color: '#374151' },
            grid: { color: '#e5e7eb' }
        }
    }
});

const setChartOptionsDoughnut = () => ({
    maintainAspectRatio: false,
    plugins: {
        title: {
            display: true,
            text: 'Tours Más Reservados',
            font: { size: 18 }
        },
        legend: {
            display: true,
            position: 'bottom',
            labels: {
                usePointStyle: true,
                color: '#374151'
            }
        },
        tooltip: {
            callbacks: {
                label: function(context) {
                    return `${context.label}: ${context.parsed} reservas`;
                }
            }
        }
    }
});

onMounted(() => {
    chartOptionsPie.value = setChartOptionsPie();
    chartOptionsBar.value = setChartOptionsBar();
    chartOptionsDoughnut.value = setChartOptionsDoughnut();
    fetchDashboardData();
});
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <!-- Estado de carga -->
        <LoadingState v-if="loading" />

        <!-- Contenido principal -->
        <div v-else class="px-2 sm:px-4 lg:px-6 py-4 sm:py-6 space-y-4 sm:space-y-6 mt-4 sm:mt-6">
            <!-- Métricas principales -->
            <MetricasCard 
                :metrics="metrics" 
                :dashboard-data="dashboardData" 
                :format-value-for-mobile="formatValueForMobile" 
            />

            <!-- 📈 LAYOUT PRINCIPAL TIPO GRID - Responsive -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
                
                <!-- Widgets secundarios -->
                <WidgetsSecundarios 
                    :widgets="widgets" 
                    :dashboard-data="dashboardData" 
                    :format-value-for-mobile="formatValueForMobile"
                    @toggle-reservas-modal="toggleReservasPendientesModal"
                    @toggle-stock-modal="toggleProductosStockBajoModal"
                />

                <!-- COLUMNA CENTRAL: Gráfico Principal - Responsive -->
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

                <!-- COLUMNA DERECHA: Estadísticas de Reservas - Responsive -->
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

            <!-- 🏆 SECCIÓN INFERIOR: Top Tours y Ventas - Responsive -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 sm:gap-6">
                
                <!-- Top 5 Tours Más Reservados -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 sm:p-6">
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

                <!-- Actividad Reciente -->
                <ActividadReciente 
                    :dashboard-data="dashboardData" 
                    :format-value-for-mobile="formatValueForMobile" 
                />
            </div>
        </div>

        <!-- Modales interactivos -->
        <ModalesInteractivos 
            :show-reservas-pendientes-modal="showReservasPendientesModal"
            :show-productos-stock-bajo-modal="showProductosStockBajoModal"
            :dashboard-data="dashboardData"
            @close-reservas-modal="showReservasPendientesModal = false"
            @close-stock-modal="showProductosStockBajoModal = false"
        />
    </AuthenticatedLayout>
</template>