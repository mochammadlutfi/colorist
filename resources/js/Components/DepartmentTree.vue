<template>
    <el-card class="shadow-sm sm:rounded-lg" body-class="p-2" v-loading="isLoading">
        <template #header>
            {{ $t('employee.org_structure') }}
        </template>
        <div class="flex justify-center">
            <blocks-tree :data="data" :horizontal="false" :collapsable="false"
            :props="{label: 'name', children: 'children',  key:'id'}">
            </blocks-tree>
        </div>
    </el-card>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';

const isLoading = ref(false);
const data = ref({
    name: 'PT Pakarti Tirtoagung',
    id: 0,
    children: [

    ]
});
const fetchData = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get('/settings/department',{
            params : {
                tree : true,
            }
        });
        // const res = response.data;
        data.value.children = response.data;
        
    } catch (error) {
        console.error('Gagal mengambil data statistik karyawan:', error);
    }
    isLoading.value = false;
}


onMounted(async () => {
    await fetchData();
});
</script>