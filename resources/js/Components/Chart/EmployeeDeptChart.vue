<template>
    <el-card class="shadow-sm sm:rounded-lg" body-class="p-2" v-loading="isLoading">
        <template #header>
            <div class="card-header">
                <span>{{ $t('employee.employee_by_dept') }}</span>
            </div>
        </template>
        <canvas ref="chartCanvas" class="!h-[280px]"></canvas>
    </el-card>
  </template>
  
<script setup>
import { ref, onMounted } from 'vue';
import { Chart, BarController, CategoryScale, LinearScale, BarElement, Tooltip, Legend } from 'chart.js';

Chart.register(BarController, CategoryScale, LinearScale, BarElement, Tooltip, Legend);

const chartCanvas = ref(null);
const isLoading = ref(false);
const chartData = {
  labels: [],
  datasets: [
    {
      label: 'Jumlah Karyawan',
      backgroundColor: '#42b983',
      data: [],
    },
  ],
};

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    indexAxis: 'y',
    plugins: {
        legend: {
            display: false
        },
    },
    scales: {
        x: {
            grid: {
                display: false // Menghapus garis grid sumbu X
            },
        },
        y: {
            grid: {
                display: true // Menghapus garis grid sumbu Y
            },
            ticks: {
                backdropPadding: 30 // Menambahkan padding di bagian Y
            }
        }
    }
};

// Fungsi untuk mengambil data dari API
async function fetchData() {
    isLoading.value = true;
    try {
        const response = await axios.get('/employee-stats/department');
        const stats = response.data;

        // Update chartData dengan data dari API
        chartData.labels = stats.map(stat => stat.department);
        chartData.datasets[0].data = stats.map(stat => stat.total);
    } catch (error) {
        console.error('Gagal mengambil data statistik karyawan:', error);
    }
    isLoading.value = false;
}

// Buat chart setelah komponen dimount
onMounted(async () => {
    await fetchData(); // Ambil data dari API
    new Chart(chartCanvas.value, {
        type: 'bar',
        data: chartData,
        options: chartOptions,
    });
});
</script>