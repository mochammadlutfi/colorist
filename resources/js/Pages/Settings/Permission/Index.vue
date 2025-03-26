<template>
    <div class="content">
        <div class="content-header">
            <h2 class="content-title">{{ $t('base.role_permission') }}</h2>
            <div class="md:flex hidden items-center gap-2.5 text-sm font-semibold">
                <el-breadcrumb separator="/">
                    <el-breadcrumb-item :to="{ path: '/' }">{{ $t('base.dashboard')}}</el-breadcrumb-item>
                    <el-breadcrumb-item :to="{ path: '/settings' }">{{ $t('base.settings')}}</el-breadcrumb-item>
                    <el-breadcrumb-item>{{ $t('base.role_permission') }}</el-breadcrumb-item>
                </el-breadcrumb>
            </div>
        </div>

        <el-card body-class="!p-0" class="!rounded-lg !shadow-md">
            <div class="flex justify-between items-center p-4">
                <el-select v-model="params.limit" :placeholder="$t('common.select')" class="w-20" @change="fetchData(1)">
                    <el-option label="25" value="25"/>
                    <el-option label="50" value="50"/>
                    <el-option label="100" value="100"/>
                </el-select>

                <div class="flex items-center gap-2">
                    <el-input
                        v-model="params.q"
                        @input="doSearch"
                        clearable
                        >
                        <template #prefix>
                            <Icon icon="mingcute:search-line"/>
                        </template>
                    </el-input>
                    <el-button to="/settings/permission/create" tag="router-link" type="primary">
                        <icon icon="mingcute:add-line" class="me-2"/>
                        {{ $t('common.add') }}
                    </el-button>
                </div>
            </div>
            
            <el-table class="min-w-full" 
                :data="dataList" v-loading="loading">
                <el-table-column prop="name" :label="$t('common.name')" sortable/>
                <el-table-column prop="users_count" :label="$t('base.user')" sortable/>
                <el-table-column :label="$t('common.action')" align="center" width="110">
                    <template #default="scope">
                        <el-dropdown popper-class="dropdown-action" trigger="click" >
                            <el-button type="primary">
                                {{ $t('common.action') }}
                            </el-button>
                            <template #dropdown>
                                <el-dropdown-menu>
                                    <el-dropdown-item class="flex justify-between">
                                        <router-link :to="`/settings/permission/${scope.row.id}/edit`" class="flex justify-between">
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
                    <span>{{ $t('common.table_paginate', { from: params.from, to: params.to, total:params.total }) }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <el-pagination class="float-end" background layout="prev, pager, next"  
                        :page-size="params.pageSize" 
                        :total="params.total" 
                        :current-page="params.page" 
                        @current-change="fetchData"
                    />
                </div>
            </div>
        </el-card>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { ElMessageBox, ElMessage } from 'element-plus';
import { Icon } from '@iconify/vue';
import _ from 'lodash';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const loading = ref(false);
const dataList = ref([]);
const params = reactive({
    page: 1,
    limit: 25,
    from: 0,
    to: 0,
    total: 0,
    pageSize: 0,
    q : null
});

const doSearch = _.debounce(() => {
    loading.value = true;
    fetchData();
}, 1000);

const sortChange = () => {
    fetchData();
}

const fetchData = async (page = 1) => {
    try {
        loading.value = true;
        const response = await axios.get("/settings/permissions", {
            params: { ...params, page }
        });
        if (response.status === 200) {
            dataList.value = response.data.data;
            params.from = response.data.from;
            params.to = response.data.to;
            params.page = response.data.current_page;
            params.total = response.data.total;
            params.pageSize = response.data.per_page;
        }
        loading.value = false;
    } catch (error) {
        console.error(error);
        loading.value = false;
    }
};

onMounted(() => {
    fetchData();
});

const onDelete = (id) => {
    ElMessageBox.confirm(t("message.delete_confirm"), t('message.delete_confirm_title'), {
        confirmButtonText: t("common.ok"),
        cancelButtonText: t("common.cancel"),
        type: 'warning',
    }).then(() => {
        axios.delete(`/settings/permissions/${id}/delete`).then(() => {
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
