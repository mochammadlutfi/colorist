<template>
    <div class="content">
        <div class="content-header">
            <div class="mt-auto mb-0">
                <div class="text-lg font-semibold">{{ $t('base.transaction_detail') }}</div>
            </div>
            <div class="mt-auto mb-0">
                
            </div>
        </div>
        
        <el-card class="!shadow-sm !rounded-lg" body-class="!p-0" v-loading="isLoading">
            <div class="p-4" v-if="data">
                <h3 class="mb-4 text-lg">{{ data.batch_number }}</h3>
                <el-descriptions :column="3" border class="mb-4">
                    <el-descriptions-item label="Mixing Date">
                        {{ formatDate(data.color_mixing_time) }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Outlet">
                        {{  data.outlet.name }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Series">
                        {{ data.series.name }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Product">
                        {{ data.product.code ? `[${ data.product.code}] ` : '' }} {{ data.product.name }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Base Paint">
                        {{ data.base_paint.code ? `[${ data.base_paint.code}] ` : '' }} {{ data.base_paint.name }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Color Card">
                        {{ data.color_card.code ? `[${ data.color_card.code}] ` : '-' }} {{ data.color_card.name }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Filling Volume">
                        {{ data.filling_volume }} {{ data.unit_name }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Bucket Qty">
                        {{ data.bucket_quantity }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Bucket No">
                        {{ data.bucket_no }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Base Paint Price">
                        {{ formatCurrency(data.base_price) }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Price">
                        {{ formatCurrency(data.price) }}
                    </el-descriptions-item>
                </el-descriptions>

                <el-table class="min-w-full" border 
                    :data="data.lines">
                    <el-table-column :label="$t('base.colorant')" sortable>
                        <template #default="scope">
                            {{ scope.row.colorant.code ? `[${ scope.row.colorant.code}] ` : '-' }} {{ scope.row.colorant.name }}
                        </template>    
                    </el-table-column>
                    <el-table-column prop="final_used_amount" label="Used Amount" sortable/>
                    <el-table-column prop="price" label="Price">
                        <template #default="scope">
                            {{ formatCurrency(scope.row.price) }}
                        </template>
                    </el-table-column>
                </el-table>
            </div>
            <div class="h-40" v-else></div>
        </el-card>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Icon } from '@iconify/vue';
import { useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { useQuery } from '@tanstack/vue-query';
import { useHead } from '@/Composable/useBase.js';
import { useFormatter } from '@/Composable/useFormatter';

const { t } = useI18n();
const { setTitle } = useHead(); 

const { formatDate, formatCurrency } = useFormatter();
const route = useRoute();
const fetchData = async ({
    queryKey
}) => {
    const [_key, queryParams] = queryKey;
    const response = await axios.get(`/transaction/${ route.params.id }`, {
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
    queryKey: [`showTransaction-${route.params.id}`],
    queryFn: fetchData,
    keepPreviousData: true,
});
</script>
