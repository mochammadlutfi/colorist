<template>
    <div class="content">
        <div class="content-header">
            <div class="mt-auto mb-0">
                <div class="text-lg font-semibold">{{ $t('base.upload_data',2) }}</div>
            </div>
            <div class="mt-auto mb-0">
                <el-breadcrumb separator="/">
                    <el-breadcrumb-item :to="{ path: '/' }">{{ $t('base.dashboard')}}</el-breadcrumb-item>
                    <el-breadcrumb-item>{{ $t('base.setting', 2) }}</el-breadcrumb-item>
                    <el-breadcrumb-item>{{ $t('base.upload_data',2) }}</el-breadcrumb-item>
                </el-breadcrumb>
            </div>
        </div>

        <el-card body-class="!p-0" class="!rounded-lg !shadow-md">
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
                    <el-input
                        v-model="params.q"
                        @input="doSearch"
                        clearable
                        :disabled="isLoading"
                        >
                        <template #prefix>
                            <Icon icon="mingcute:search-line"/>
                        </template>
                    </el-input>
                    <el-button type="primary" 
                    :disabled="isLoading" @click.prevent="openModal">
                        <icon icon="mingcute:add-line" class="me-2"/>
                        {{ $t('common.add')  }}
                    </el-button>
                </div>
            </div>
            <el-skeleton :loading="isLoading" animated>
                <template #template>
                    <skeleton-table/>
                </template>
                <template #default>
                    <el-table class="min-w-full" 
                        row-key="id"
                        default-expand-all
                        :data="data.data" @sort-change="sortChange">
                        <el-table-column :label="$t('common.date')">
                            <template #default="scope">
                                {{ formatDate(scope.row.created_at) }}
                            </template>
                        </el-table-column>
                        <el-table-column prop="user.name" :label="$t('common.uploaded_by')" sortable/>
                        <el-table-column prop="outlet.name" :label="$t('base.outlet')" sortable/>
                        <el-table-column prop="file_name" :label="$t('common.file_name')" sortable/>
                        <el-table-column :label="$t('common.record')" align="center">
                            <el-table-column prop="total_records" :label="$t('common.total')" width="100"/>
                            <el-table-column prop="processed_records" :label="$t('common.processed')" width="100"/>
                        </el-table-column>
                        <el-table-column prop="price" :label="$t('common.status')">
                            <template #default="scope">
                                <el-tag :type="getTypeStatus(scope.row.status)">{{ $t(`base.${scope.row.status}`) }}</el-tag>
                            </template>
                        </el-table-column>
                    </el-table>
                    <div class="flex justify-between items-center p-4">
                        <div class="flex items-center gap-2">
                            <span>{{ $t('common.table_paginate', { from: data.from, to: data.to, total:data.total }) }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <el-pagination class="float-end" background layout="prev, pager, next"  
                                :page-size="data.per_page" 
                                :total="data.total" 
                                :current-page="data.current_page" 
                                @current-change="changePage"
                            />
                        </div>
                    </div>
                </template>
            </el-skeleton>
        </el-card>
        
        <el-dialog
        id="modalForm"
        v-model="formShow"
        :title="formTitle" 
        class="!sm:w-full !w-1/3 rounded-lg"
        :close-on-click-modal="false"
        :close-on-press-escape="false">
            <el-form label-position="top" ref="formRef" :model="form" :rules="formRules" @submit.prevent="onSubmit">
                <el-row :gutter="20">
                    <el-col :md="24">
                        <el-form-item :label="$t('base.outlet')" prop="outlet_id">
                            <select-outlet v-model="form.outlet_id"/>
                        </el-form-item>
                    </el-col>
                    <el-col :md="24">
                        <el-form-item label="File Dokumen" prop="file">
                            <input-file v-model="form.file" accept=".csv"/>
                        </el-form-item>
                    </el-col>
                </el-row>
                <div class="text-end">
                    <el-button @click.prevent="onResetForm">
                        {{ $t('common.cancel') }}
                    </el-button>
                    <el-button type="primary" native-type="submit">
                        {{ $t('common.save') }}
                    </el-button>
                </div>
            </el-form>
        </el-dialog>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { ElMessageBox, ElMessage, ElLoading } from 'element-plus';
import { Icon } from '@iconify/vue';
import _ from 'lodash';
import { useI18n } from 'vue-i18n';
import SkeletonTable from '@/Components/SkeletonTable.vue';
import { useQuery } from '@tanstack/vue-query';
import { useFormatter } from '@/Composable/useFormatter';
import SelectOutlet from '@/Components/Form/SelectOutlet.vue';
import { useHead } from '@/Composable/useBase.js';
import InputFile from '@/Components/Form/InputFile.vue';

const { formatDate, objectToFormData } = useFormatter();
const { t } = useI18n();
const { setTitle } = useHead(); 

onMounted(() => {
    setTitle('Upload Data');
});


const getTypeStatus = (status) => {
    if(status == 'failed'){
        return 'danger';
    }else if(status == 'processing'){
        return 'info';
    }else if(status == 'completed'){
        return 'success';
    }else if(status == 'pending'){
        return 'warning';
    }
}


const params = ref({
    page: 1,
    limit: 25,
    q : "",
    tree : true,
});
const selected = ref([]);

const fetchData = async ({
    queryKey
}) => {
    const [_key, queryParams] = queryKey;
    const response = await axios.get("/upload", {
        params: queryParams,
    });
    params.value.page = response.data.page;
    return response.data;
};

const {
    data,
    isLoading,
    isError,
    error,
    refetch
} = useQuery({
    queryKey: ['uploadList', params.value], // Query key unik
    queryFn: fetchData,
    keepPreviousData: true,
});

const doSearch = _.debounce(() => {
    params.value.page = 1;
    refetch();
}, 5000);

const sortChange = () => {
    refetch();
}
const changePage = (newPage) => {
    params.value.page = newPage;
    refetch(); 
};

const formRef = ref(null);
const formShow = ref(false);
const formTitle = ref('');
const form = ref({
    id : null,
    outlet_id : null,
    file : null,
});
const formRules = ref({
    outlet_id: [
        { required: true, message: t('validation.required', { attribute: t('base.outlet') }), trigger: 'blur' },
    ],
    file: [
        { required: true, message: t('validation.required', { attribute: 'File Document' }), trigger: 'change' },
    ],
});
const formLoading = ref(false);

const openModal = () => {
    formShow.value = true;
    form.value.outlet_id = null;
    form.value.file = null;
}

const onResetForm = () => {
    formShow.value = false;
    form.value.outlet_id = null;
    form.value.file = null;
}

const onSubmit = async () => {
    if (!formRef.value) return;
    formRef.value.validate(async (valid) => {
        if (valid) {
            const loading = ElLoading.service({
                customClass: 'rounded-md',
                target: document.querySelector('#modalForm')
            });
            try {
                formLoading.value = true;
                const url = '/upload/store';
                const method = 'post';
                await axios({
                    method,
                    url,
                    data: objectToFormData(form.value),
                    headers: {
                        "Content-Type": "multipart/form-data"
                    },
                });
                formShow.value = false;
                refetch();
                onResetForm();
                ElMessage({
                    message: t('message.success_save'),
                    type: 'success',
                });
            } catch (error) {
                formLoading.value = false;
                ElMessage({
                    message: t('message.error_server'),
                    type: 'error',
                });
            }
            loading.close();
        } else {
            ElMessage({
                message: t('message.error_input'),
                type: 'error',
            });
        }
    });
};
</script>
