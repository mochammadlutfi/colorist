<template>
    <div>
    <el-card class="shadow-sm sm:rounded-lg" body-class="p-0">
        <div class="flex justify-between items-center p-4">
            <el-select v-model="params.limit" placeholder="Pilih" style="width: 115px" @change="fetchData(1)">
                <el-option label="25" value="25"/>
                <el-option label="50" value="50"/>
                <el-option label="100" value="100"/>
            </el-select>

            <div class="flex items-center gap-2">
                <el-button type="primary" @click.prevent="onCreate">
                    <i class="mgc_add_line me-2"></i>
                    {{ $t('common.create') }}
                </el-button>
            </div>
        </div>
        <el-table class="min-w-full" 
            :data="dataList" v-loading="loading">
            <el-table-column prop="name" :label="$t('common.name')" header-align="center"/>
            <el-table-column prop="key" :label="$t('common.code')" header-align="center"/>
            <el-table-column :label="$t('common.action')" align="center" width="100">
                <template #default="scope">
                    <el-dropdown popper-class="dropdown-action" trigger="click">
                        <el-button type="primary">
                        {{ $t('common.action') }}
                        </el-button>
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item @click.prevent="onEdit(scope.row)">
                                    <i class="mgc_edit_line"></i>
                                    {{  $t('common.edit') }}
                                </el-dropdown-item>
                                <el-dropdown-item @click.prevent="onDelete(scope.row.id)" v-if="scope.row.id != 1">
                                    <i class="mgc_delete_2_line"></i>
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
    
    <el-dialog
    v-model="modalForm"
    :title="modalTitle"
    class="rounded-md">
        <el-form :model="form" @submit.prevent="onSubmit" label-position="top">
            <el-row :gutter="20">
                <el-col :md="12">
                    <el-form-item :label="$t('common.name')" :error="errors.name">
                        <el-input v-model="form.name" />
                    </el-form-item>
                    <el-form-item :label="$t('common.display_mode')" :error="errors.name">
                        <el-radio-group v-model="form.type" class="ml-4">
                            <el-radio value="ltr">LTR</el-radio>
                            <el-radio value="rtl">RTL</el-radio>
                        </el-radio-group>
                    </el-form-item>
                </el-col>
                <el-col :md="12">
                    <el-form-item :label="$t('common.code')" :error="errors.key">
                        <el-input v-model="form.key" />
                    </el-form-item>
                    <el-form-item :label="$t('common.status')" :error="errors.status">
                        <el-radio-group v-model="form.status" class="ml-4">
                            <el-radio :value="1">{{ $t('common.active') }}</el-radio>
                            <el-radio :value="0">{{ $t('common.inactive') }}</el-radio>
                        </el-radio-group>
                    </el-form-item>
                </el-col>
            </el-row>
            <div class="text-right">
                <el-button plain @click.prevent="modalForm = false">
                    {{ $t('common.cancel') }}
                </el-button>
                <el-button native-type="submit" type="primary">
                    {{ $t('common.save') }}
                </el-button>
            </div>
        </el-form>
    </el-dialog>
</div>
</template>

<script setup>
import { defineProps, onMounted, defineEmits, ref } from 'vue';
import { useI18n } from 'vue-i18n';
const { t } = useI18n();

const dataList = ref([]);
const params = ref({
  page: 1,
  limit: 25,
  from: 0,
  to: 0,
  total: 0,
  pageSize: 0,
  name: null,
  manager_id: null,
  address: null,
});

const modalForm = ref(false);
const modalTitle = ref(t('common.create') + ' ' + t('base.language'));
const filterShow = ref(false);
const errors = ref({});
const loading = ref(false);
const form = ref({
  id: null,
  name: null,
  key: null,
  type: "ltr",
  status : 1
});
const emit = defineEmits(['childinit']);

const fetchData = async (page = 1) => {
  try {
    errors.value = {};
    loading.value = true;
    const response = await axios.get('/settings/language', { params: params.value });
    if (response.status === 200) {
      dataList.value = response.data.data;
    //   Object.assign(params.value, response.data);
        params.value.total = response.data.total;
        params.value.from = response.data.from;
        params.value.to = response.data.to;
        params.value.pageSize = response.data.per_page;
    }
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
};


const onCreate = () => {
    modalForm.value = true;
    modalTitle.value = t('create') + ' ' + t('language');
};

const onEdit = (v) => {
    modalForm.value = true;
    modalTitle.value = t('edit') + ' ' + t('language');
    form.value.id = v.id;
    form.value.name = v.name;
    form.value.key = v.key;
    form.value.image = v.image;
    form.value.is_active = v.is_active;
};

const resetFilter = () => {
  params.value.name = null;
  params.value.address = null;
  fetchData(1);
};


const props = defineProps({
  title: {
    type: String,
    default: '',
  },
});

const onSubmit = async () => {
  loadingForm.value = true;
  const url = form.value.id ? `/settings/branch/${form.value.id}/update` : '/settings/branch/store';
  try {
    const response = await axios.post(url, form.value);
    console.log(response.data.result);
    ElMessage({
      message: t('success_msg'),
      type: 'success',
    });
    modalForm.value = false;
    resetForm();
    fetchData();
  } catch (error) {
    errors.value = error.validation;
    ElMessage({
      message: t('error_msg'),
      type: 'error',
    });
  } finally {
    loadingForm.value = false;
  }
};
onMounted(() => {
  emit('childinit', 'email_setting');
  fetchData(1);
});
</script>