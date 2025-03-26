<template>
    <div class="content">
        <div class="content-header">
            <div class="mt-auto mb-0">
                <div class="text-lg font-semibold">{{ $t('base.transaction', 2) }}</div>
            </div>
            <div class="mt-auto mb-0">
                <el-breadcrumb separator="/">
                    <el-breadcrumb-item :to="{ path: '/' }">{{ $t('base.dashboard')}}</el-breadcrumb-item>
                    <el-breadcrumb-item>{{ $t('base.transaction', 2) }}</el-breadcrumb-item>
                </el-breadcrumb>
            </div>
        </div>

        <el-card body-class="!p-0" class="!rounded-lg !shadow-md !mb-6">
            <div class="flex justify-between items-center p-4">
                <el-select v-model="params.limit" 
                :placeholder="$t('common.select')" 
                :disabled="isLoading"
                class="w-20" @change="refetch()">
                    <el-option label="25" value="25"/>
                    <el-option label="50" value="50"/>
                    <el-option label="100" value="100"/>
                </el-select>

                <div class="flex items-center gap-2">
                    <el-button type="primary" 
                    :disabled="isLoading" @click.prevent="filterShow = !filterShow" 
                    :plain="filterShow">
                        <icon icon="fluent:filter-24-filled" class="me-2"/>
                        {{ $t('common.filter') }}
                    </el-button> 
                </div>
            </div>
                    
            <transition name="slide-down">
                <div class="card-filter p-4" v-if="filterShow">
                    <el-form label-position="top" @submit.prevent="refetch">
                        <el-row :gutter="20">
                            <el-col :md="8">
                                <el-form-item :label="$t('base.batch_number')">
                                    <el-input v-model="params.batch_number" clearable/>
                                </el-form-item>
                            </el-col>
                            <el-col :md="8">
                                <el-form-item :label="$t('base.outlet')">
                                    <select-outlet v-model="params.outlet_id"/>
                                </el-form-item>
                            </el-col>
                            <el-col :md="8">
                                <el-form-item :label="$t('common.date')">
                                    <el-date-picker
                                        v-model="params.date"
                                        type="daterange"
                                        range-separator="To"
                                        start-placeholder="Start date"
                                        end-placeholder="End date"
                                        format="DD MMMM YYYY"
                                        value-format="YYYY-MM-DD"
                                    />
                                </el-form-item>
                            </el-col>
                        </el-row>
                        
                        <div class="flex items-center gap-2">
                            <el-button type="primary" @click.prevent="resetFilter">
                                <Icon icon="fluent:arrow-clockwise-24-regular" class="me-2"/>
                                {{ $t('common.reset')}}
                            </el-button>
                        </div>
                    </el-form>
                </div>
            </transition>
            <el-skeleton :loading="isLoading" animated>
                <template #template>
                    <skeleton-table :rows="15"/>
                </template>
                <template #default v-if="data">
                    <el-table class="!w-full" row-class-name="!cursor-pointer"
                    header-cell-class-name="!bg-neutral-100"
                        scrollbar-always-on
                        @row-click="onClickRow"
                        :data="data.data" @sort-change="sortChange">
                        <el-table-column prop="outlet.name" :label="$t('base.outlet')" sortable/>
                        <el-table-column prop="color_mixing_time" :label="$t('common.date')" sortable>
                            <template #default="scope">
                                {{ scope.row.color_mixing_time ? formatDate(scope.row.color_mixing_time) : '-' }}
                            </template>
                        </el-table-column>
                        <el-table-column prop="batch_number" :label="$t('base.batch_number')" sortable>
                            <template #default="scope">
                                {{ scope.row.batch_number ? scope.row.batch_number : '-' }}
                            </template>
                        </el-table-column>
                        <el-table-column prop="series.name" :label="$t('base.series')" sortable/>
                        <el-table-column prop="product.name" :label="$t('base.product')" sortable/>
                        <el-table-column prop="price.name" :label="$t('base.price')" sortable>
                            <template #default="scope">
                                {{ scope.row.price ? scope.row.price : '-' }}
                            </template>
                        </el-table-column>
                        <!-- <el-table-column prep="ptkp" :label="$t('employee.ptkp')" sortable width="90">
                            <template #default="scope">
                                {{ scope.row.ptkp ?? '-' }}
                            </template>
                        </el-table-column>
                        <el-table-column prep="status" :label="$t('common.status')" sortable width="140">
                            <template #default="scope">
                                {{ scope.row.status ? $t(`employee.${scope.row.status}`) : '-' }}
                            </template>
                        </el-table-column> -->
                    </el-table>
                    <div class="flex justify-between items-center p-4">
                        <div class="flex items-center gap-2">
                            <span>{{ $t('common.table_paginate', { from: data.from, to: data.to, total:data.total }) }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <el-pagination class="float-end" background layout="prev, pager, next"  
                                :page-size="data.per_page" 
                                :total="data.total" 
                                :current-page="params.page" 
                                @current-change="changePage"
                            />
                        </div>
                    </div>
                </template>
            </el-skeleton>
        </el-card>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { ElMessageBox, ElMessage } from 'element-plus';
import { Icon } from '@iconify/vue';
import _ from 'lodash';
import SkeletonTable from '@/Components/SkeletonTable.vue';
import { useQuery } from '@tanstack/vue-query';
import { useI18n } from 'vue-i18n';
import { useHead } from '@/Composable/useBase.js';
import { useAbility } from '@casl/vue';
import { useFormatter } from '@/Composable/useFormatter.js';
import SelectOutlet from '@/Components/Form/SelectOutlet.vue';
import { useRouter } from 'vue-router';
import InputFile from '@/Components/Form/InputFile.vue';

const { t } = useI18n();
const { can } = useAbility();
const { setTitle } = useHead();
const { formatDate, objectToFormData } = useFormatter();
const router = useRouter();

onMounted(() => {
    setTitle(t('base.transaction', 2));
});

const filterShow = ref(false);
const params = ref({
    limit: 25,
    page : 1,
    q : "",
    batch_number : null,
    outlet_id : null,
    date : null
});

const resetFilter = () => {
    params.value.name = null;
    params.value.code = null;
    params.value.status = null;
    params.value.dept = null;
    params.value.pst = null;
    params.value.superior = null;
    refetch();
};

const onClickRow = (data) =>{
    return router.push(`/transaction/${data.id}`);
}

const fetchData = async ({
    queryKey
}) => {
    const [_key, queryParams] = queryKey;
    const response = await axios.get("/transaction", {
        params: queryParams,
    });
    // params.value.page = response.data.page;
    return response.data;
};

const {
    data,
    isLoading,
    isError,
    error,
    refetch
} = useQuery({
    queryKey: ['transactionList', params.value],
    queryFn: fetchData,
    keepPreviousData: true,
});

const doSearch = _.debounce(() => {
    params.value.page = 1;
    refetch();
}, 1000);

const sortChange = () => {
    refetch();
}

const changePage = (newPage) => {
    params.value.page = newPage;
    refetch(); 
};

const onDelete = (id) => {
    ElMessageBox.confirm(t("message.delete_confirm"), t('message.delete_confirm_title'), {
        confirmButtonText: t("common.ok"),
        cancelButtonText: t("common.cancel"),
        type: 'warning',
    }).then(() => {
        axios.delete(`/transaction/${id}/delete`).then(() => {
            fetchData();
            ElMessage({
                type: 'success',
                message: t('message.delete_success'),
            });
        }).catch(error => {
            ElMessage({
                type: 'error',
                message: t('message.delete_cancel'),
            });
        });
    }).catch(() => {
        ElMessage({
            type: 'info',
            message: t('message.delete_cancel')
        });
    });
};

</script>