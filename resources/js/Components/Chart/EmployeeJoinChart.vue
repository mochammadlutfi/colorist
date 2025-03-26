<template>
    <el-card class="shadow-sm sm:rounded-lg" body-class="p-2">
        <canvas ref="chartCanvas"></canvas>
    </el-card>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import { Chart, LineController, CategoryScale, LinearScale, PointElement, LineElement, Tooltip, Legend } from 'chart.js';
  
  Chart.register(LineController, CategoryScale, LinearScale, PointElement, LineElement, Tooltip, Legend);
  
  const chartCanvas = ref(null);
  
  const chartData = {
    labels: [], // Tahun
    datasets: [
      {
        label: 'Statistik Karyawan Baru',
        borderColor: '#42b983',
        data: [],
      },
    ],
  };
  
  const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
  };
  
  async function fetchJoinTrend() {
    try {
      const response = await axios.get('/employee-stats/join');
      const stats = response.data;
  
      chartData.labels = stats.map(stat => stat.year);
      chartData.datasets[0].data = stats.map(stat => stat.total);
    } catch (error) {
      console.error('Gagal mengambil data tren bergabung:', error);
    }
  }
  
  onMounted(async () => {
    await fetchJoinTrend();
    new Chart(chartCanvas.value, {
      type: 'line',
      data: chartData,
      options: chartOptions,
    });
  });
  </script>