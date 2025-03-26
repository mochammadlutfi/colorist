<template>    
    <div class="content">
        <div class="content-header">
            <h2 class="content-title">{{ title }}</h2>
            <div class="md:flex hidden items-center gap-2.5 text-sm font-semibold">
                <el-breadcrumb separator="/">
                    <el-breadcrumb-item :to="{ path: '/' }">{{ $t('base.dashboard')}}</el-breadcrumb-item>
                    <el-breadcrumb-item :to="{ path: '/settings' }">{{ $t('base.settings')}}</el-breadcrumb-item>
                    <el-breadcrumb-item>{{ $t('base.role_permission') }}</el-breadcrumb-item>
                    <el-breadcrumb-item>{{ $t(route.params.id ? 'common.create' : 'common.edit') }}</el-breadcrumb-item>
                </el-breadcrumb>
            </div>
        </div>

        <el-card body-class="p-0" v-loading="isLoading">
            <el-form :model="form" ref="formRef" @submit.prevent="onSubmit" label-position="top">
                <div class="p-4">
                    <el-form-item :label="$t('base.role')" prop="name" 
                    :rules="[{
                        required: true, message: $t('validation.required', { attribute: $t('base.role') }) 
                    }]">
                        <el-input v-model="form.name" />
                    </el-form-item>
                </div>
                <div class="mb-4">
                    <div class="md:columns-3 gap-4 px-4">
                        <el-card class="break-inside mb-4" body-class="!p-0" v-for="(actions, category) in permissionList" :key="category">
                            <div class="flex justify-between mb-2 border-b-2 p-4 bg-gray-100">
                                <h3 class="font-semibold ">{{ $t(category) }}</h3>
                                <label class="flex items-center">
                                    <input 
                                        type="checkbox" 
                                        @change="toggleCategory(category, $event)" 
                                        :checked="isCategoryChecked(category)" 
                                        class="mr-2 form-checkbox text-blue-500"
                                    />
                                    Select All
                                </label>
                            </div>
                            <div class="p-4">
                                <div class="grid grid-cols-2 gap-2">
                                    <div v-for="action in actions" :key="action.name" class="">
                                        <label class="flex items-center">
                                            <input 
                                            type="checkbox" 
                                            v-model="form.lines" 
                                            :value="action.name" 
                                            class="mr-2 form-checkbox text-blue-500"
                                            />
                                            {{ (category == 'base.setting') ? $t(`base.${action.action}`) : $t(`common.${action.action}`) }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </el-card>
                    </div>
                </div>

                <div class="p-4 ">
                    <el-button type="danger" plain @click.prevent="$router.go(-1)">
                        {{ $t('common.cancel') }}
                    </el-button>
                    <el-button native-type="submit" type="primary">
                        {{ $t('common.save') }}
                    </el-button>
                </div>
            </el-form>
        </el-card>
    </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import { useRoute, useRouter } from 'vue-router';

const { t } = useI18n();
const router = useRouter();
const route = useRoute();
const title = ref(t('common.create') + ' ' + t('base.role_permission'));
const form = ref({
    id : null,
    name : null,
    lines : [],
});
const isLoading = ref(false);
const permissionList = ref([]);
const formRef = ref(null);

const toggleCategory = (category, event) => {
  const isChecked = event.target.checked;
  permissionList.value[category].forEach(action => {
    if (isChecked) {
      if (!form.value.lines.includes(action.name)) {
        form.value.lines.push(action.name);
      }
    } else {
      form.value.lines = form.value.lines.filter(item => item !== action.name);
    }
  });
};

const isCategoryChecked = (category) => {
  return permissionList.value[category].every(action => form.value.lines.includes(action.name));
};

const fetchPermission = async () => {
    try {
        isLoading.value = true;
        const response = await axios.get('/settings/permissions/list');
        if(response.status == 200){
            permissionList.value = response.data;
        }
        isLoading.value = false;
    } catch (error) {
        console.error(error);
    }
}

const fetchData = async (id) => {
    try {
        const response = await axios.get('/settings/permissions/'+ id);
        if(response.status == 200){
            const data = response.data;
            form.value.id = data.id;
            form.value.name = data.name;
            data.permissions.forEach(item2 => {
                form.value.lines.push(item2.name);
            });
        }
    } catch (error) {
        console.error(error);
    }
}

const onSubmit = async () => {
    if (!formRef.value) return;
    formRef.value.validate(async (valid) => {
        if (valid) {
            try {
                isLoading.value = true;
                const url = form.value.id ? 
                `/settings/permissions/${form.value.id}/update` : 
                '/settings/permissions/store';
                const method = form.value.id ? 'put' : 'post';
                await axios({
                    method,
                    url,
                    data: form.value
                });

                isLoading.value = false;
                ElMessage({
                    message: t('message.success_input'),
                    type: 'success',
                });
                router.replace({ path: '/settings/permission' });
            } catch (error) {
                console.log(error);
                isLoading.value = false;
                ElMessage({
                    message: t('message.error_server'),
                    type: 'error',
                });
            }
        } else {
            ElMessage({
                message: t('message.error_input'),
                type: 'error',
            });
        }
    });
};

onMounted(() => {
    fetchPermission();
    if(route.params.id){
        fetchData(route.params.id);
    }
});
</script>
