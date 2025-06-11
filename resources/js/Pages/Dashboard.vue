<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Chart from 'primevue/chart';
import { ref, onMounted } from "vue";

const chartDataPie = ref();
const chartDataBar = ref();
const chartDataDoughnut = ref();
const chartOptionsPie = ref();
const chartOptionsBar = ref();
const chartOptionsDoughnut = ref();

// Tarjetas de métricas con naranja, rojo y azul
const metrics = [
    { label: 'Ganancias', value: '$628', icon: 'pi pi-wallet', color: 'from-orange-500 to-yellow-300', text: 'text-orange-900' },
    { label: 'Reservas de Hoteles', value: '2434', icon: 'pi pi-calendar', color: 'from-pink-600 to-red-300', text: 'text-red-900' },
    { label: 'Reservas de Tours', value: '2434', icon: 'pi pi-calendar', color: 'from-orange-500 to-orange-400', text: 'text-red-900' },
    { label: 'Clientes', value: '1259', icon: 'pi pi-users', color: 'from-green-700 to-green-400', text: 'text-blue-900' },
    { label: 'Calificación', value: '8.5', icon: 'pi pi-star', color: 'from-yellow-400 to-yellow-400', text: 'text-yellow-900' }
];

// Gráfica de ventas de boletos (Pie)
const setChartDataPie = () => ({
    labels: ['Boletos Nacionales', 'Boletos Internacionales', 'Boletos Promocionales'],
    datasets: [
        {
            label: 'Ventas de Boletos',
            data: [120, 80, 45],
            backgroundColor: ['#f97316', '#ef4444', '#2563eb'], // naranja, rojo, azul
            hoverBackgroundColor: ['#fb923c', '#f87171', '#3b82f6']
        }
    ]
});

// Gráfica de reservaciones de hoteles (Bar)
const setChartDataBar = () => ({
    labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
    datasets: [
        {
            label: '2019',
            data: [40, 60, 80, 50, 70, 90, 100, 80, 60, 70, 90, 110],
            backgroundColor: '#E868DD' // naranja
        },
        {
            label: '2020',
            data: [50, 70, 60, 80, 90, 100, 120, 110, 90, 100, 120, 130],
            backgroundColor: '#E68943' // rojo
        }
    ]
});

// Gráfica de paquetes turísticos (Doughnut)
const setChartDataDoughnut = () => ({
    labels: ['Cerro Verde y el Lago de Ilopango', 'Festival del Melocotón, El Pital y Mirador de Cristal', 'Cerro El Pital, Chalatenango'],
    datasets: [
        {
            label: 'Paquetes Turísticos',
            data: [45, 30, 25],
            backgroundColor: ['#E65142', '#ef4444', '#2563eb'],
            hoverBackgroundColor: ['#E65154', '#f87171', '#3b82f6']
        }
    ]
});

// Opciones para Pie
const setChartOptionsPie = () => ({
    maintainAspectRatio: false,
    plugins: {
        title: {
            display: true,
            text: 'Ventas de Boletos',
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
                    return `${context.label}: ${context.parsed} ventas`;
                }
            }
        }
    }
});

// Opciones para Bar
const setChartOptionsBar = () => ({
    maintainAspectRatio: false,
    plugins: {
        title: {
            display: true,
            text: 'Reservaciones de Hoteles',
            font: { size: 18 }
        },
        legend: {
            display: false
        },
        tooltip: {
            callbacks: {
                label: function(context) {
                    return `${context.label}: ${context.parsed.y} reservaciones`;
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

// Opciones para Doughnut
const setChartOptionsDoughnut = () => ({
    maintainAspectRatio: false,
    plugins: {
        title: {
            display: true,
            text: 'Reservaciones de Tours',
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
                    return `${context.label}: ${context.parsed} reservaciones`;
                }
            }
        }
    }
});

onMounted(() => {
    chartDataPie.value = setChartDataPie();
    chartDataBar.value = setChartDataBar();
    chartDataDoughnut.value = setChartDataDoughnut();
    chartOptionsPie.value = setChartOptionsPie();
    chartOptionsBar.value = setChartOptionsBar();
    chartOptionsDoughnut.value = setChartOptionsDoughnut();
});
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <!-- Tarjetas de métricas -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-6 pt-10">
            <div v-for="metric in metrics" :key="metric.label"
                class="flex flex-col justify-between rounded-xl shadow-lg bg-gradient-to-br"
                :class="metric.color + ' p-6 min-h-[110px]'">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-lg text-white">{{ metric.label }}</span>
                    <i :class="metric.icon + ' text-2xl text-white'"></i>
                </div>
                <span class="text-3xl font-extrabold mt-2 text-white">{{ metric.value }}</span>
            </div>
        </div>

        <!-- Gráficas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 justify-items-center pt-10">
            <div class="w-full max-w-lg h-96 rounded-lg shadow-lg bg-white overflow-hidden flex flex-col items-center p-4">
                <Chart type="pie" :data="chartDataPie" :options="chartOptionsPie" class="w-full h-full" />
            </div>
            <div class="w-full max-w-lg h-96 rounded-lg shadow-lg bg-white overflow-hidden flex flex-col items-center p-4">
                <Chart type="bar" :data="chartDataBar" :options="chartOptionsBar" class="w-full h-full" />
            </div>
            <div class="w-full max-w-lg h-96 rounded-lg shadow-lg bg-white overflow-hidden flex flex-col items-center p-4">
                <Chart type="doughnut" :data="chartDataDoughnut" :options="chartOptionsDoughnut" class="w-full h-full" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
