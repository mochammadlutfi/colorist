<template>
    <div class="content">
        <div class="content-header">
            <h2 class="content-title">{{ $t('base.user', 2) }}</h2>
            <div class="md:flex hidden items-center gap-2.5 text-sm font-semibold">
                <el-breadcrumb separator="/">
                    <el-breadcrumb-item :to="{ path: '/' }">{{ $t('base.dashboard')}}</el-breadcrumb-item>
                    <el-breadcrumb-item :to="{ path: '/settings' }">{{ $t('base.setting', 2)}}</el-breadcrumb-item>
                    <el-breadcrumb-item>{{ $t('base.user', 2) }}</el-breadcrumb-item>
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
                    <el-button tag="router-link" type="primary" to="/settings/user/create" 
                    :disabled="isLoading">
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
                        :data="data.data" v-loading="loading">
                        <el-table-column prop="name" :label="$t('common.name')"/>
                        <el-table-column prop="email" :label="$t('common.email')"/>
                        <el-table-column prop="phone" :label="$t('common.phone')">
                            <template #default="scope">
                                {{ scope.row.phone ?? '-' }}
                            </template>
                        </el-table-column>
                        <el-table-column :label="$t('base.role')">
                            <template #default="scope">
                                {{ (scope.row.roles.length) ? scope.row.roles[0].name  : '-'}}
                            </template>
                        </el-table-column>
                        <el-table-column :label="$t('common.action')" align="center" width="110">
                            <template #default="scope">
                                <el-dropdown popper-class="dropdown-action" trigger="click" >
                                    <el-button type="primary">
                                        {{ $t('common.action') }}
                                    </el-button>
                                    <template #dropdown>
                                        <el-dropdown-menu>
                                            <el-dropdown-item class="flex justify-between">
                                                <router-link :to="`/settings/user/${scope.row.id}/edit`" class="flex justify-between">
                                                    <Icon icon="mingcute:edit-line" class="me-2"/>
                                                    {{  $t('common.edit') }}
                                                </router-link>
                                            </el-dropdown-item>
                                            <el-dropdown-item class="flex justify-between" @click.prevent="onDelete(scope.row.id)" v-if="scope.row.id != 1">
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
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import { ElMessageBox, ElMessage } from 'element-plus';
import { Icon } from '@iconify/vue';
import _ from 'lodash';
import { useI18n } from 'vue-i18n';
import SkeletonTable from '@/Components/SkeletonTable.vue';
import { useQuery } from '@tanstack/vue-query';
import { useHead } from '@/Composable/useBase.js';

const  { t } = useI18n();
const { setTitle } = useHead(); 

onMounted(() => {
    setTitle(t('base.user', 2));
});

const params = ref({
    page: 1,
    limit: 25,
    q : ""
});


const fetchData = async ({
    queryKey
}) => {
    const [_key, queryParams] = queryKey;
    const response = await axios.get("/settings/user", {
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
    queryKey: ['userList', params.value], // Query key unik
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

const onDelete = (id) => {
    ElMessageBox.confirm(t("message.delete_confirm"), t('message.delete_confirm_title'), {
        confirmButtonText: t("common.ok"),
        cancelButtonText: t("common.cancel"),
        type: 'warning',
    }).then(() => {
        axios.delete(`/settings/user/${id}/delete`).then(() => {
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
