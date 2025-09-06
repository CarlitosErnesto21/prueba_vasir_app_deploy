<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from "vue";
import axios from 'axios';

const chartDataPie = ref();
const chartDataBar = ref();
const chartDataDoughnut = ref();
const chartOptionsPie = ref();
const chartOptionsBar = ref();
const chartOptionsDoughnut = ref();

// ✅ DATOS REALES de tu sistema
const loading = ref(true);
const dashboardData = ref({});

// 📊 Métricas DINÁMICAS con datos reales
const metrics = ref([
    { 
        label: 'Valor Inventario', 
        value: '$0', 
        icon: 'pi pi-wallet', 
        color: 'from-green-500 to-green-300', 
        text: 'text-green-900',
        key: 'valor_total_inventario' 
    },
    { 
        label: 'Total Productos', 
        value: '0', 
        icon: 'pi pi-box', 
        color: 'from-blue-600 to-blue-300', 
        text: 'text-blue-900',
        key: 'total_productos' 
    },
    { 
        label: 'Stock Bajo', 
        value: '0', 
        icon: 'pi pi-exclamation-triangle', 
        color: 'from-yellow-500 to-yellow-400', 
        text: 'text-yellow-900',
        key: 'productos_stock_bajo' 
    },
    { 
        label: 'Agotados', 
        value: '0', 
        icon: 'pi pi-times-circle', 
        color: 'from-red-600 to-red-400', 
        text: 'text-red-900',
        key: 'productos_agotados' 
    },
    { 
        label: 'Ventas Hoy', 
        value: '0', 
        icon: 'pi pi-shopping-cart', 
        color: 'from-purple-600 to-purple-400', 
        text: 'text-purple-900',
        key: 'ventas_hoy' 
    }
]);

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
        
        // ✅ Extracción de datos
        const inventarioData = inventarioResponse.data.data || inventarioResponse.data;
        const ventasData = ventasResponse.data.data || ventasResponse.data || [];
        const stockBajoData = stockBajoResponse.data.data || stockBajoResponse.data || [];
        
        // 🔄 Actualizar métricas y gráficos
        updateMetrics(inventarioData, ventasData);
        updateCharts(inventarioData, ventasData, stockBajoData);
        
        dashboardData.value = {
            inventario: inventarioData,
            ventas: ventasData,
            stockBajo: stockBajoData
        };
        
    } catch (error) {
        // En caso de error, usar valores por defecto
        updateMetrics({}, []);
        updateCharts({}, [], []);
    } finally {
        loading.value = false;
    }
};

// 🔄 FUNCIÓN para actualizar métricas
const updateMetrics = (inventarioData, ventasData) => {
    const ventas = Array.isArray(ventasData) ? ventasData : [];
    
    // Calcular ventas de hoy
    const hoy = new Date().toISOString().split('T')[0];
    const ventasHoy = ventas.filter(venta => {
        if (!venta.fecha) return false;
        const fechaVenta = venta.fecha.split('T')[0];
        return fechaVenta === hoy;
    }).length;
    
    // ✅ Actualizar valores con los datos correctos
    metrics.value[0].value = `$${Number(inventarioData.valor_total_inventario || 0).toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    metrics.value[1].value = (inventarioData.total_productos || 0).toString();
    metrics.value[2].value = (inventarioData.productos_stock_bajo || 0).toString();
    metrics.value[3].value = (inventarioData.productos_agotados || 0).toString();
    metrics.value[4].value = (inventarioData.movimientos_hoy || ventasHoy || 0).toString();
};

// 📈 FUNCIÓN para actualizar gráficos
const updateCharts = (inventarioData, ventasData, stockBajoData) => {
    const totalProductos = inventarioData.total_productos || 0;
    const stockBajo = inventarioData.productos_stock_bajo || 0;
    const agotados = inventarioData.productos_agotados || 0;
    const disponibles = inventarioData.productos_disponibles || Math.max(0, totalProductos - stockBajo - agotados);
    const ventas = Array.isArray(ventasData) ? ventasData : [];
    
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
    
    // 📊 Gráfico BAR: Ventas por estado
    const ventasPorEstado = ventas.reduce((acc, venta) => {
        const estado = venta.estado || 'Sin estado';
        acc[estado] = (acc[estado] || 0) + 1;
        return acc;
    }, {});
    
    if (Object.keys(ventasPorEstado).length === 0) {
        ventasPorEstado['Sin ventas'] = 0;
    }
    
    chartDataBar.value = {
        labels: Object.keys(ventasPorEstado),
        datasets: [{
            label: 'Ventas por Estado',
            data: Object.values(ventasPorEstado),
            backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444']
        }]
    };
    
    // 🍩 Gráfico DOUGHNUT: Top productos más vendidos
    const productosVendidos = {};
    
    ventas.forEach(venta => {
        if (venta.detalle_ventas && Array.isArray(venta.detalle_ventas)) {
            venta.detalle_ventas.forEach(detalle => {
                const nombre = detalle.producto?.nombre || 'Producto desconocido';
                productosVendidos[nombre] = (productosVendidos[nombre] || 0) + (detalle.cantidad || 0);
            });
        }
    });
    
    let topProductos = Object.entries(productosVendidos)
        .sort(([,a], [,b]) => b - a)
        .slice(0, 5);
    
    if (topProductos.length === 0) {
        topProductos = [['Sin ventas', 0]];
    }
    
    chartDataDoughnut.value = {
        labels: topProductos.map(([nombre]) => nombre),
        datasets: [{
            label: 'Productos Más Vendidos',
            data: topProductos.map(([, cantidad]) => cantidad),
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
            text: 'Ventas por Estado',
            font: { size: 18 }
        },
        legend: {
            display: false
        },
        tooltip: {
            callbacks: {
                label: function(context) {
                    return `${context.label}: ${context.parsed.y} ventas`;
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
            text: 'Productos Más Vendidos',
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
                    return `${context.label}: ${context.parsed} unidades`;
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
        <!-- ✅ MOSTRAR ESTADO DE CARGA O ERROR -->
        <div v-if="loading" class="flex justify-center items-center py-12">
            <div class="text-center">
                <i class="pi pi-spin pi-spinner text-4xl text-indigo-600 mb-4"></i>
                <p class="text-gray-600">Cargando datos del dashboard...</p>
            </div>
        </div>

        <!-- Contenido principal -->
        <div v-else>
            <!-- ✅ TARJETAS COMPLETAMENTE RESPONSIVAS -->
            <div class="pt-6">
                <!-- DESKTOP: Mostrar todos los 5 cards en una fila -->
                <div class="hidden md:grid md:grid-cols-5 gap-4 xl:gap-6 mt-6">
                    <div v-for="metric in metrics" :key="metric.label"
                        class="flex flex-col justify-between rounded-xl shadow-lg bg-gradient-to-br p-4 xl:p-6 min-h-[100px] xl:min-h-[110px]"
                        :class="metric.color">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-white leading-tight"
                                  :class="metric.label.length > 12 ? 'text-sm xl:text-base' : 'text-base xl:text-lg'">
                                {{ metric.label }}
                            </span>
                            <i :class="metric.icon + ' text-xl xl:text-2xl text-white flex-shrink-0'"></i>
                        </div>
                        <span class="font-extrabold text-white leading-none"
                              :class="metric.value.length > 8 ? 'text-xl xl:text-2xl' : 'text-2xl xl:text-3xl'">
                            {{ metric.value }}
                        </span>
                    </div>
                </div>

                <!-- TABLET: Mostrar en 2 filas adaptativas -->
                <div class="hidden sm:grid md:hidden">
                    <!-- Primera fila: 3 cards -->
                    <div class="grid grid-cols-3 gap-3 mb-3">
                        <div v-for="(metric, index) in metrics.slice(0, 3)" :key="metric.label"
                            class="flex flex-col justify-between rounded-xl shadow-lg bg-gradient-to-br p-3 min-h-[85px]"
                            :class="metric.color">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-white text-xs leading-tight">
                                    {{ metric.label.split(' ').map(word => word.slice(0, 6)).join(' ') }}
                                </span>
                                <i :class="metric.icon + ' text-lg text-white flex-shrink-0'"></i>
                            </div>
                            <span class="font-extrabold text-white text-lg leading-none">
                                {{ metric.value.length > 10 ? metric.value.slice(0, 10) + '...' : metric.value }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Segunda fila: 2 cards centrados -->
                    <div class="grid grid-cols-2 gap-3 max-w-md mx-auto">
                        <div v-for="(metric, index) in metrics.slice(3, 5)" :key="metric.label"
                            class="flex flex-col justify-between rounded-xl shadow-lg bg-gradient-to-br p-3 min-h-[85px]"
                            :class="metric.color">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-white text-xs leading-tight">
                                    {{ metric.label.split(' ').map(word => word.slice(0, 6)).join(' ') }}
                                </span>
                                <i :class="metric.icon + ' text-lg text-white flex-shrink-0'"></i>
                            </div>
                            <span class="font-extrabold text-white text-lg leading-none">
                                {{ metric.value }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- ✅ MOBILE PEQUEÑO: Layout ultra compacto -->
                <div class="sm:hidden">
                    <!-- Primera fila: 3 cards muy compactos -->
                    <div class="grid grid-cols-3 gap-2 mb-2">
                        <div v-for="(metric, index) in metrics.slice(0, 3)" :key="metric.label"
                            class="flex flex-col justify-between rounded-lg shadow-lg bg-gradient-to-br p-2 min-h-[70px]"
                            :class="metric.color">
                            <div class="flex flex-col items-center text-center">
                                <i :class="metric.icon + ' text-base text-white mb-1'"></i>
                                <span class="font-bold text-white text-xs leading-none mb-1">
                                    {{ metric.label.split(' ')[0] }}
                                </span>
                                <span class="font-extrabold text-white text-sm leading-none">
                                    {{ index === 0 ? formatValueForMobile(metric.value, dashboardData.inventario) : metric.value }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Segunda fila: 2 cards -->
                    <div class="grid grid-cols-2 gap-2">
                        <div v-for="(metric, index) in metrics.slice(3, 5)" :key="metric.label"
                            class="flex flex-col justify-between rounded-lg shadow-lg bg-gradient-to-br p-2 min-h-[70px]"
                            :class="metric.color">
                            <div class="flex flex-col items-center text-center">
                                <i :class="metric.icon + ' text-base text-white mb-1'"></i>
                                <span class="font-bold text-white text-xs leading-none mb-1">
                                    {{ metric.label.split(' ')[0] }}
                                </span>
                                <span class="font-extrabold text-white text-sm leading-none">
                                    {{ metric.value }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ✅ GRÁFICAS RESPONSIVAS -->
                <div class="pt-6 sm:pt-8 md:pt-10">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 md:gap-6 mb-4 sm:mb-6 lg:mb-0">
                        <!-- Gráfico PIE -->
                        <div class="w-full h-64 sm:h-80 md:h-96 rounded-lg shadow-lg bg-white overflow-hidden flex flex-col items-center p-2 sm:p-3 md:p-4">
                            <Chart v-if="chartDataPie" 
                                type="pie" 
                                :data="chartDataPie" 
                                :options="chartOptionsPie" 
                                class="w-full h-full" />
                            <div v-else class="flex items-center justify-center w-full h-full">
                                <p class="text-gray-500 text-xs sm:text-sm">No hay datos de inventario</p>
                            </div>
                        </div>
                        
                        <!-- Gráfico BAR -->
                        <div class="w-full h-64 sm:h-80 md:h-96 rounded-lg shadow-lg bg-white overflow-hidden flex flex-col items-center p-2 sm:p-3 md:p-4">
                            <Chart v-if="chartDataBar" 
                                type="bar" 
                                :data="chartDataBar" 
                                :options="chartOptionsBar" 
                                class="w-full h-full" />
                            <div v-else class="flex items-center justify-center w-full h-full">
                                <p class="text-gray-500 text-xs sm:text-sm">No hay datos de ventas</p>
                            </div>
                        </div>
                        
                        <!-- Gráfico DOUGHNUT -->
                        <div class="w-full h-64 sm:h-80 md:h-96 rounded-lg shadow-lg bg-white overflow-hidden flex flex-col items-center p-2 sm:p-3 md:p-4 sm:col-span-2 lg:col-span-1 sm:mx-auto lg:mx-0 sm:max-w-md lg:max-w-none">
                            <Chart v-if="chartDataDoughnut" 
                                type="doughnut" 
                                :data="chartDataDoughnut" 
                                :options="chartOptionsDoughnut" 
                                class="w-full h-full" />
                            <div v-else class="flex items-center justify-center w-full h-full">
                                <p class="text-gray-500 text-xs sm:text-sm">No hay productos vendidos</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ✅ TABLA RESPONSIVA -->
                <div v-if="dashboardData.stockBajo && dashboardData.stockBajo.length > 0" 
                    class="mt-6 sm:mt-8 md:mt-10 bg-white rounded-lg shadow-lg p-3 sm:p-4 md:p-6">
                    <h3 class="text-sm sm:text-base md:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">
                        <i class="pi pi-exclamation-triangle text-yellow-500 mr-2"></i>
                        Productos con Stock Bajo
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-2 sm:px-3 md:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                    <th class="px-2 sm:px-3 md:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Actual</th>
                                    <th class="px-2 sm:px-3 md:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Mínimo</th>
                                    <th class="px-2 sm:px-3 md:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="producto in dashboardData.stockBajo" :key="producto.id">
                                    <td class="px-2 sm:px-3 md:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium text-gray-900">
                                        {{ producto.nombre }}
                                    </td>
                                    <td class="px-2 sm:px-3 md:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500">
                                        {{ producto.stock_actual }}
                                    </td>
                                    <td class="px-2 sm:px-3 md:px-6 py-2 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500">
                                        {{ producto.stock_minimo }}
                                    </td>
                                    <td class="px-2 sm:px-3 md:px-6 py-2 sm:py-4 whitespace-nowrap">
                                        <span class="inline-flex px-1 sm:px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Stock Bajo
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>