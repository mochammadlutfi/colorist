<template>
    <el-card class="shadow-sm sm:rounded-lg" body-class="p-0" v-loading="isLoading">
        <template #header>
        <div class="card-header">
            <span>{{ $t('employee.employee_by_status') }}</span>
        </div>
        </template>
        <el-row :gutter="20">
            <el-col :md="12">
                <canvas ref="chartCanvas" class="!h-[190px] p-2"></canvas>
            </el-col>
            <el-col :md="12">
                <el-row class="border-l ">
                    <el-col :md="12" v-for="(d,i) in data" :key="i">
                        <div class="p-2 flex-fill border-r border-b">
                            <div class="text-sm mb-0">
                                <!-- <i class="ti ti-square-filled text-primary fs-12 me-2"></i> -->
                                {{ d.status === 'Undefined' ? d.status : $t(`employee.${d.status}`) }}
                                <span class="text-gray-9">(48%)</span></div>
                            <h2 class="text-lg">{{ d.total }}</h2>
                        </div>
                    </el-col>
                </el-row>
            </el-col>
        </el-row>
    </el-card>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import { Chart, DoughnutController, ArcElement, Tooltip, Legend } from 'chart.js';

Chart.register(DoughnutController, ArcElement, Tooltip, Legend);

const { t } = useI18n();
const chartCanvas = ref(null);
const isLoading = ref(false);
const data = ref([]);
const chartData = {
    labels: [],
    datasets: [
        {
            label: 'Jumlah Karyawan',
            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'], // Warna berbeda untuk setiap segmen
            data: [],
        },
    ],
};

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: true, // Menampilkan legenda untuk menjelaskan warna
            position: 'right',
        },
        tooltip: {
            enabled: true, // Menampilkan tooltip saat hover
        },
    },
};

// Fungsi untuk mengambil data dari API
async function fetchData() {
    isLoading.value = true;
    try {
        const response = await axios.get('/employee-stats/status');
        const stats = response.data;

        // Update chartData dengan data dari API
        data.value = stats;
        chartData.labels = stats
            .filter(stat => stat.status !== 'Undefined')
            .map(stat => t(`employee.${stat.status}`));

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
        type: 'doughnut',
        data: chartData,
        options: chartOptions,
    });
});
</script>
