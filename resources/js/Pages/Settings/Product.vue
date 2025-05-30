<template>
    <div class="content">
        <div class="content-header">
            <div class="mt-auto mb-0">
                <div class="text-lg font-semibold">{{ $t('base.product', 2) }}</div>
            </div>
            <div class="mt-auto mb-0">
                <el-breadcrumb separator="/">
                    <el-breadcrumb-item :to="{ path: '/' }">{{ $t('base.dashboard') }}</el-breadcrumb-item>
                    <el-breadcrumb-item>{{ $t('base.setting', 2) }}</el-breadcrumb-item>
                    <el-breadcrumb-item>{{ $t('base.product', 2) }}</el-breadcrumb-item>
                </el-breadcrumb>
            </div>
        </div>

        <el-card body-class="!p-0" class="!rounded-lg !shadow-md">
            <div class="flex justify-between items-center p-4">
                <el-select v-model="params.limit" :placeholder="$t('common.select')" :disabled="isLoading" class="w-20"
                    @change="refetch()">
                    <el-option label="25" value="25" />
                    <el-option label="50" value="50" />
                    <el-option label="100" value="100" />
                </el-select>

                <div class="flex items-center gap-2">
                    <el-input v-model="params.q" @input="doSearch" clearable :disabled="isLoading">
                        <template #prefix>
                            <Icon icon="mingcute:search-line" />
                        </template>
                    </el-input>
                    <el-button type="primary" :disabled="isLoading" @click.prevent="openModal">
                        <icon icon="mingcute:add-line" class="me-2" />
                        {{ $t('common.add') }}
                    </el-button>
                </div>
            </div>
            <el-skeleton :loading="isLoading" animated>
                <template #template>
                    <skeleton-table />
                </template>
                <template #default>
                    <el-table class="min-w-full" :data="data.data" @sort-change="sortChange"
                        @selection-change="onSelectionChange">
                        <el-table-column type="selection" width="55" />
                        <el-table-column prop="code" :label="$t('common.code')" sortable />
                        <el-table-column prop="name" :label="$t('common.name')" sortable />
                        <el-table-column :label="$t('common.created_at')">
                            <template #default="scope">
                                {{ formatDate(scope.row.created_at) }}
                            </template>
                        </el-table-column>
                        <el-table-column :label="$t('common.last_updated')">
                            <template #default="scope">
                                {{ formatDate(scope.row.updated_at) }}
                            </template>
                        </el-table-column>
                        <el-table-column :label="$t('common.action')" align="center" width="150">
                            <template #default="scope">
                                <el-dropdown popper-class="dropdown-action" trigger="click">
                                    <el-button type="primary">
                                        {{ $t('common.action') }}
                                    </el-button>
                                    <template #dropdown>
                                        <el-dropdown-menu>
                                            <el-dropdown-item class="flex justify-between"
                                                @click.prevent="onEdit(scope.row)">
                                                <Icon icon="mingcute:edit-line" class="me-2" />
                                                {{ $t('common.edit') }}
                                            </el-dropdown-item>
                                            <el-dropdown-item class="flex justify-between"
                                                @click.prevent="onDelete(scope.row.id)">
                                                <Icon icon="mingcute:delete-2-line" class="me-2" />
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
                            <span>{{ $t('common.table_paginate', { from: data.from, to: data.to, total: data.total })
                                }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <el-pagination class="float-end" background layout="prev, pager, next"
                                :page-size="data.per_page" :total="data.total" :current-page="data.current_page"
                                @current-change="changePage" />
                        </div>
                    </div>
                </template>
            </el-skeleton>
        </el-card>

        <el-dialog id="modalForm" v-model="formShow" :title="formTitle" class="!sm:w-full !w-1/3 rounded-lg"
            :close-on-click-modal="false" :close-on-press-escape="false">
            <el-form label-position="top" ref="formRef" :model="form" :rules="formRules" @submit.prevent="onSubmit">
                <el-form-item :label="$t('common.name')" prop="name">
                    <el-input v-model="form.name" />
                </el-form-item>
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

const { formatDate } = useFormatter();
const { t } = useI18n();
const { setTitle } = useHead();

onMounted(() => {
    setTitle(t('base.product', 2));
});
const params = ref({
    page: 1,
    limit: 25,
    q: ""
});
const selected = ref([]);

const onSelectionChange = (val) => {
    selected.value = val
}

const fetchData = async ({
    queryKey
}) => {
    const [_key, queryParams] = queryKey;
    const response = await axios.get("/settings/product", {
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
    queryKey: ['productList', params.value], // Query key unik
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
    id: null,
    name: null,
    short_name: null,
});
const formRules = ref({
    name: [
        { required: true, message: t('validation.required', { attribute: t('common.name') }), trigger: 'blur' },
    ]
});
const formLoading = ref(false);

const openModal = () => {
    formTitle.value = `${t('common.add')} ${t('base.product')}`;
    formShow.value = true;
    form.value.id = null;
    form.value.name = null;
}

const onEdit = (data) => {
    formTitle.value = `${t('common.edit')} ${t('base.product')}`;
    formShow.value = true;
    form.value.id = data.id;
    form.value.name = data.name;
}

const onResetForm = () => {
    formShow.value = false;
    form.value.id = null;
    form.value.name = null;
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
                const url = form.value.id ? `/settings/product/${form.value.id}/update` : '/settings/product/store';
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
        axios.delete(`/settings/product/${id}/delete`).then(() => {
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
