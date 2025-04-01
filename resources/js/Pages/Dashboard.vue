<template>
    <div class="content">
        <div class="content-header">
            <div class="mt-auto mb-0">
                <div class="text-lg font-semibold">{{ $t('base.dashboard',2) }}</div>
            </div>
            <div class="mt-auto mb-0">
                
            </div>
        </div>
        <el-row :gutter="20">
            <el-col :md="6" :span="12" class="mb-4">
                <stats-card label="Total Sales" icon="line-md:account" v-loading="isLoading">
                    {{ data?.sales ?? 0 }}
                </stats-card>
            </el-col>
            <el-col :md="6" :span="12" class="mb-4">
                <stats-card label="Total Outlet" icon="mynaui:store" v-loading="isLoading">
                    {{ data?.outlet ?? 0 }}
                </stats-card>
            </el-col>
            <el-col :md="6" :span="12" class="mb-4">
                <stats-card label="Total Upload" icon="line-md:file-upload" v-loading="isLoading">
                    {{ data?.uploaded ?? 0 }}
                </stats-card>
            </el-col>
            <el-col :md="6" :span="12" class="mb-4">
                <stats-card label="Failed Upload" icon="line-md:upload-off-outline-loop" v-loading="isLoading">
                    {{ data?.failed_upload ?? 0 }}
                </stats-card>
            </el-col>
        </el-row>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import { useHead } from '@/Composable/useBase.js';
import StatsCard from '@/Components/StatsCard.vue';
import { useQuery } from '@tanstack/vue-query';

const { t } = useI18n();
const { setTitle } = useHead(); 


const stats = ref({
    sales : 0,
    outlet : 0,
    uploaded : 0,
    upload_failed : 0,
})

onMounted(() => {
    setTitle(t('base.dashboard'));
});
const fetchData = async ({
    queryKey
}) => {
    const [_key, queryParams] = queryKey;
    const response = await axios.get(`/dashboard`, {
        params: queryParams,
    });
    return response.data.result;
};

const {
    data,
    isLoading,
    isError,
    error,
    refetch
} = useQuery({
    queryKey: [`showDashboard`],
    queryFn: fetchData,
    keepPreviousData: true,
});
</script>
