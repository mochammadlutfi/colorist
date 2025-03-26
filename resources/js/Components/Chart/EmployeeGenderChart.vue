<template>
    <el-card class="shadow-sm sm:rounded-lg" body-class="p-2">
        <canvas ref="chartCanvas"></canvas>
    </el-card>
  </template>
  
<script setup>
import { ref, onMounted } from 'vue';
import { Chart, PieController, ArcElement, Tooltip, Legend } from 'chart.js';

Chart.register(PieController, ArcElement, Tooltip, Legend);

const chartCanvas = ref(null);

const chartData = {
  labels: ['Laki-laki', 'Perempuan'],
  datasets: [
    {
      label: 'Distribusi Gender',
      backgroundColor: ['#42b983', '#f87979'],
      data: [],
    },
  ],
};

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
};

async function fetchGenderStats() {
  try {
    const response = await axios.get('/employee-stats/gender');
    const stats = response.data;

    chartData.datasets[0].data = [stats.male, stats.female];
  } catch (error) {
    console.error('Gagal mengambil data statistik gender:', error);
  }
}

onMounted(async () => {
  await fetchGenderStats();
  new Chart(chartCanvas.value, {
    type: 'pie',
    data: chartData,
    options: chartOptions,
  });
});
</script>