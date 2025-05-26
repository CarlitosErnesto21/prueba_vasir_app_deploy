<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Chart from 'primevue/chart';
import { ref, onMounted } from "vue";

const chartDataPie = ref();
const chartDataBar = ref();
const chartDataDoughnut = ref();
const chartOptions = ref();

const setChartDataPie = () => ({
    labels: ['A', 'B', 'C'],
    datasets: [
        {
            data: [540, 325, 702],
            backgroundColor: ['#06b6d4', '#22d3ee', '#0e7490'], // cyan
            hoverBackgroundColor: ['#67e8f9', '#38bdf8', '#0ea5e9']
        }
    ]
});

const setChartDataBar = () => ({
    labels: ['A', 'B', 'C'],
    datasets: [
        {
            data: [540, 325, 702],
            backgroundColor: ['#f59e42', '#fbbf24', '#f97316'], // orange
            hoverBackgroundColor: ['#fdba74', '#fcd34d', '#fb923c']
        }
    ]
});

const setChartDataDoughnut = () => ({
    labels: ['A', 'B', 'C'],
    datasets: [
        {
            data: [540, 325, 702],
            backgroundColor: ['#6b7280', '#9ca3af', '#d1d5db'], // gray
            hoverBackgroundColor: ['#a1a1aa', '#d4d4d8', '#e5e7eb']
        }
    ]
});

const setChartOptions = () => ({
    plugins: {
        legend: {
            labels: {
                usePointStyle: true,
                color: '#374151'
            }
        }
    }
});

onMounted(() => {
    chartDataPie.value = setChartDataPie();
    chartDataBar.value = setChartDataBar();
    chartDataDoughnut.value = setChartDataDoughnut();
    chartOptions.value = setChartOptions();
});
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 justify-items-center pt-10">
            <div class="w-full max-w-lg rounded-lg shadow-lg bg-white overflow-hidden flex flex-col items-center p-4">
                <Chart type="pie" :data="chartDataPie" :options="chartOptions" class="w-full aspect-square" />
            </div>
            <div class="w-full max-w-lg rounded-lg shadow-lg bg-white overflow-hidden flex flex-col items-center p-4">
                <Chart type="pie" :data="chartDataBar" :options="chartOptions" class="w-full aspect-square" />
            </div>
            <div class="w-full max-w-lg rounded-lg shadow-lg bg-white overflow-hidden flex flex-col items-center p-4">
                <Chart type="pie" :data="chartDataDoughnut" :options="chartOptions" class="w-full aspect-square" />
            </div>
            <div class="w-full max-w-lg rounded-lg shadow-lg bg-white overflow-hidden flex flex-col items-center p-4">
                <Chart type="pie" :data="chartDataPie" :options="chartOptions" class="w-full aspect-square" />
            </div>
            <div class="w-full max-w-lg rounded-lg shadow-lg bg-white overflow-hidden flex flex-col items-center p-4">
                <Chart type="pie" :data="chartDataBar" :options="chartOptions" class="w-full aspect-square" />
            </div>
            <div class="w-full max-w-lg rounded-lg shadow-lg bg-white overflow-hidden flex flex-col items-center p-4">
                <Chart type="pie" :data="chartDataDoughnut" :options="chartOptions" class="w-full aspect-square" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
