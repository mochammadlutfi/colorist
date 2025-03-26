<template>
    <div class="content">
        <div class="content-header">
            <div class="mt-auto mb-0">
                <div class="text-lg font-semibold">Outlet</div>
            </div>
            <div class="mt-auto mb-0">
                <el-breadcrumb separator="/">
                    <el-breadcrumb-item :to="{ path: '/' }">{{ $t('base.dashboard')}}</el-breadcrumb-item>
                    <el-breadcrumb-item>Outlet</el-breadcrumb-item>
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
                        {{ $t('common.create')  }}
                    </el-button>
                </div>
            </div>
            <el-skeleton :loading="isLoading" animated>
                <template #template>
                    <skeleton-table/>
                </template>
                <template #default>
                    <el-table class="min-w-full" 
                        :data="data.data" @sort-change="sortChange" @selection-change="onSelectionChange">
                        <el-table-column type="selection" width="55" />
                        <el-table-column prop="name" :label="$t('common.name')" sortable/>
                        <el-table-column prop="machine_type" label="Machine Type" sortable/>
                        <el-table-column prop="machine_code" label="Code ID" sortable/>
                        <el-table-column prop="machine_serial" label="Machine Serial" sortable/>
                        <el-table-column :label="$t('common.action')" align="center" width="150">
                            <template #default="scope">
                                <el-dropdown popper-class="dropdown-action" trigger="click" >
                                    <el-button type="primary">
                                        {{ $t('common.action') }}
                                    </el-button>
                                    <template #dropdown>
                                        <el-dropdown-menu>
                                            <el-dropdown-item class="flex justify-between" @click.prevent="onEdit(scope.row)">
                                                <Icon icon="mingcute:edit-line" class="me-2"/>
                                                {{  $t('common.edit') }}
                                            </el-dropdown-item>
                                            <el-dropdown-item class="flex justify-between" @click.prevent="onDelete(scope.row.id)">
                                                <Icon icon="mingcute:delete-2-line" class="me-2"/>
                                                {{ $t('common.delete') }}
                                            </el-dropdown-item>
                                        </el-dropdown-menu>
                                    </template>
                                </el-dropdown>
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
        class="!sm:w-full !md:w-[450px] rounded-lg"
        :close-on-click-modal="false"
        :close-on-press-escape="false">
            <el-form label-position="top" ref="formRef" :model="form" :rules="formRules" @submit.prevent="onSubmit">
                <el-row :gutter="20">
                    <el-col :md="12">
                        <el-form-item :label="$t('common.name')" prop="name">
                            <el-input v-model="form.name"/>
                        </el-form-item>
                    </el-col>
                    <el-col :md="12">
                        <el-form-item :label="$t('common.branch')" prop="branch_id">
                            <select-branch v-model="form.branch_id"/>
                        </el-form-item>
                    </el-col>
                    <el-col :md="12">
                        <el-form-item label="Machine Type" prop="machine_type">
                            <el-radio-group v-model="form.machine_type">
                                <el-radio value="Automatic">Automatic</el-radio>
                                <el-radio value="Manual">Manual</el-radio>
                            </el-radio-group>
                        </el-form-item>
                    </el-col>
                    <el-col :md="12">
                        <el-form-item label="Code ID" prop="machine_code">
                            <el-input v-model="form.machine_code"/>
                        </el-form-item>
                    </el-col>
                    <el-col :md="12">
                        <el-form-item label="Machine Serial" prop="machine_serial">
                            <el-input v-model="form.machine_serial"/>
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
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { ElMessageBox, ElMessage, ElLoading } from 'element-plus';
import { Icon } from '@iconify/vue';
import _ from 'lodash';
import { useI18n } from 'vue-i18n';
import SkeletonTable from '@/Components/SkeletonTable.vue';
import { useQuery } from '@tanstack/vue-query';
import { useFormatter } from '@/Composable/useFormatter';
import { useHead } from '@/Composable/useBase.js';
import SelectBranch from '@/Components/Form/SelectBranch.vue';

const { formatDate } = useFormatter();
const { t } = useI18n();
const { setTitle } = useHead(); 

onMounted(() => {
    setTitle(t('base.expertise', 2));
});

const params = ref({
    page: 1,
    limit: 25,
    q : ""
});
const selected = ref([]);

const onSelectionChange = (val) => {
    selected.value = val
}

const fetchData = async ({
    queryKey
}) => {
    const [_key, queryParams] = queryKey;
    const response = await axios.get("/outlet", {
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
    queryKey: ['expertiseList', params.value], // Query key unik
    queryFn: fetchData,
    keepPreviousData: true,
});

const doSearch = _.debounce(() => {
    params.value.page = 1;
    refetch();
}, 2000);

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
    name : null,
    branch_id : null,
    machine_type : 'Automatic',
    machine_code : null,
    machine_serial : null,
});
const formRules = ref({
    name: [
        { required: true, message: t('validation.required', { attribute: t('common.name') }), trigger: 'blur' },
    ],
    branch_id: [
        { required: true, message: t('validation.required', { attribute: t('base.branch') }), trigger: 'change' },
    ],
    machine_code: [
        { required: true, message: t('validation.required', { attribute: 'Code ID' }), trigger: 'blur' },
    ],
    machine_serial: [
        { required: true, message: t('validation.required', { attribute: 'Machine Serial' }), trigger: 'blur' },
    ]
});
const formLoading = ref(false);

const openModal = () => {
    formTitle.value = `Tambah Outlet`;
    formShow.value = true;
    form.value.id = null;
    form.value.name = null;
    form.value.branch_id = null;
    form.value.machine_type = 'Automatic';
    form.value.machine_code = null;
    form.value.machine_serial = null;
}

const onEdit = (data) => {
    formTitle.value = `Ubah Outlet`;
    formShow.value = true;
    form.value.id = data.id;
    form.value.name = data.name;
    form.value.branch_id = data.branch_id;
    form.value.machine_type = data.machine_type;
    form.value.machine_code = data.machine_code;
    form.value.machine_serial = data.machine_serial;
}

const onResetForm = () => {
    formShow.value = false;
    form.value.id = null;
    form.value.branch_id = null;
    form.value.machine_type = 'Automatic';
    form.value.machine_code = null;
    form.value.machine_serial = null;
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
                const url = form.value.id ? `/outlet/${form.value.id}/update` : '/outlet/store';
                const method = form.value.id ? 'put' : 'post';
                await axios({
                    method,
                    url,
                    data: form.value
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

const onDelete = (id) => {
    ElMessageBox.confirm(t("message.delete_confirm"), t('message.delete_confirm_title'), {
        confirmButtonText: t("common.ok"),
        cancelButtonText: t("common.cancel"),
        type: 'warning',
    }).then(() => {
        axios.delete(`/outlet/${id}/delete`).then(() => {
            refetch();
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
