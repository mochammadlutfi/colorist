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
            <el-col :md="6">
                <stats-card label="Total Sales" icon="line-md:account">
                    {{ stats.sales }}
                </stats-card>
            </el-col>
            <el-col :md="6">
                <stats-card label="Total Outlet" icon="mynaui:store">
                    {{ stats.outlet }}
                </stats-card>
            </el-col>
            <el-col :md="6">
                <stats-card label="Total Upload" icon="line-md:file-upload">
                    {{ stats.sales }}
                </stats-card>
            </el-col>
            <el-col :md="6">
                <stats-card label="Failed Upload" icon="line-md:upload-off-outline-loop">
                    {{ stats.sales }}
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

const { t } = useI18n();
const { setTitle } = useHead(); 


const stats = ref({
    sales : 40,
    outlet : 40,
    uploaded : 40,
    upload_failed : 4,
})

onMounted(() => {
    setTitle(t('base.dashboard'));
});
</script>
