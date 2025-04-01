<template>
    <div class="content">
        <div class="content-header">
            <h2 class="content-title">{{ title }}</h2>
            <div class="md:flex hidden items-center gap-2.5 text-sm font-semibold">
                <el-breadcrumb separator="/">
                    <el-breadcrumb-item :to="{ path: '/' }">{{ $t('base.dashboard')}}</el-breadcrumb-item>
                    <el-breadcrumb-item :to="{ path: '/settings/system' }">{{ $t('base.setting',2)}}</el-breadcrumb-item>
                    <el-breadcrumb-item :to="{ path: '/settings/user' }">{{ $t('base.user',2)}}</el-breadcrumb-item>
                    <el-breadcrumb-item>{{ route.params.id ? $t('common.edit') :$t('common.create') }}</el-breadcrumb-item>
                </el-breadcrumb>
            </div>
        </div>
        <el-card body-class="p-0" v-loading="isLoading">
            <el-form :model="form" :rules="formRules" ref="formRef" @submit.prevent="onSubmit" label-position="top">
                <div class="p-4">
                    <el-row :gutter="20">
                        <el-col :md="12">
                            <el-form-item :label="$t('common.image')">
                                <image-upload v-model="form.image" size="small"/>
                            </el-form-item>
                            <el-form-item :label="$t('common.name')" prop="name">
                                <el-input v-model="form.name" :readonly="form.employee_id"/>
                            </el-form-item>
                            <el-form-item :label="$t('common.email')" prop="email">
                                <el-input v-model="form.email" :readonly="form.employee_id"/>
                            </el-form-item>
                        </el-col>
                        <el-col :md="12">
                            <el-form-item :label="$t('base.outlet')" prop="outlet_ids">
                                <select-outlet v-model="form.outlet_ids" multiple/>
                            </el-form-item>
                            <el-form-item :label="$t('base.role')" prop="role">
                                <select-role v-model="form.role"/>
                            </el-form-item>
                            <el-form-item :label="$t('common.password')" prop="password" >
                                <el-input v-model="form.password" type="password" show-password/>
                            </el-form-item>
                            <el-form-item :label="$t('common.password_confirmation')" prop="password_confirmation">
                                <el-input v-model="form.password_confirmation" type="password" show-password/>
                            </el-form-item>
                        </el-col>
                    </el-row>
                </div>

                <div class="p-4">
                    <el-button native-type="submit" type="primary">
                        {{ $t('common.save') }}
                    </el-button>
                </div>
            </el-form>
        </el-card>
    </div>
</template>

<script setup>
import ImageUpload from '@/Components/Form/ImageUpload.vue';
import SelectRole from '@/Components/Form/SelectRole.vue';
import SelectOutlet from '@/Components/Form/SelectOutlet.vue';
import { useRoute, useRouter } from 'vue-router';
import { ref, watch, computed, onMounted } from 'vue';
import { useFormatter } from '@/Composable/useFormatter';
import { useHead } from '@/Composable/useBase.js';

import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const { setTitle } = useHead();

const route = useRoute();
const router = useRouter();

const title = ref(`${ t('common.create') } ${t('base.user')}`);
const form = ref({
    id: route.params.id ? route.params.id : null,
    name: null,
    outlet_ids : null,
    email : null,
    password : null,
    password_confirmation : null,
    role : null,
    image : null,
});

const formRef = ref(null);
const formRules = ref({
    name: [
        { required: true, message: t('validation.required', { attribute: t('common.name') }), trigger: 'blur' },
    ],
    email: [
        { required: true, message: t('validation.required', { attribute: t('common.email') }), trigger: 'blur' },
        { type: 'email', message: t('validation.email', { attribute: t('common.email') }), trigger: 'blur' },
    ],
    role: [
        { required: true, message: t('validation.required', { attribute: t('base.role') }), trigger: 'blur' },
    ],
    // password: [
    //     { required: true, message: t('validation.required', { attribute: t('common.password') }), trigger: 'blur' },
    // ],
    // password_confirmation: [
    //     { required: true, message: t('validation.required', { attribute: t('common.password_confirmation') }), trigger: 'blur' },
    //     { validator: (rule, value, callback) => {
    //         if (value !== form.value.password) {
    //             callback(new Error(t('validation.password_mismatch')));
    //         } else {
    //             callback();
    //         }
    //     }, trigger: 'blur' },
    // ],
})
const isLoading = ref(false);

const fetchEmployee = async (v) => {
    if(v){
        try {
            isLoading.value = true;
            const response = await axios.get(`/employee/${v}`);
            if (response.status === 200) {
                const data = response.data.result;

                form.value.name = data.name;
                form.value.email = data.email;
                console.log(data);

                isLoading.value = false;
            }
        } catch (error) {
            console.error(error);
        }
    }else{
        form.value.name = null;
        form.value.email = null;
    }
};

const fetchData = async () => {
    try {
        isLoading.value = true;
        const response = await axios.get(`/settings/user/${route.params.id}`);
        if (response.status === 200) {
            const data = response.data;
            form.value.id = data.id;
            form.value.employee_id = data.employee_id;
            form.value.name = data.name;
            form.value.email = data.email;
            form.value.username = data.username;
            form.value.role = data.roles[0].id;

            form.value.outlet_ids = data.outlet.map(outlet => outlet.id);
            isLoading.value = false;
        }
    } catch (error) {
        console.error(error);
    }
};

const onSubmit = async () => {
    if (!formRef.value) return;
    formRef.value.validate(async (valid) => {
        if (valid) {
            try {
                isLoading.value = true;
                const url = form.value.id ? 
                `/settings/user/${form.value.id}/update` : 
                '/settings/user/store';
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
                router.replace({ path: '/settings/user' });
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
    if(route.params.id){
        title.value = `${ t('common.edit') } ${t('base.user')}`;
        fetchData();
    }

    setTitle(title.value);
})
</script>