<template>
    <div class="2xl:w-1/4 lg:w-1/3 md:w-1/2 w-full">
        <el-card class="overflow-hidden sm:rounded-md rounded-none">
            <div class="p-6">
                <a href="" class="block mb-8">
                    <img class="w-full block dark:hidden" :src="app.logo_light" alt="">
                    <img class="w-full hidden dark:block" :src="app.logo_dark" alt="">
                </a>

                <el-form ref="formRef" :model="form" :rules="formRules" label-position="top" @submit.prevent="submit">
                    <el-form-item :label="$t('common.email')" prop="email" :error="emailError">
                        <el-input v-model="form.email" type="text" @input="clearEmailError" />
                    </el-form-item>
                    <el-form-item :label="$t('common.password')" prop="password">
                        <el-input v-model="form.password" type="password" show-password />
                    </el-form-item>
                    <el-button native-type="submit" type="primary" class="w-full" :loading="loading">
                        {{ $t('common.login') }}
                    </el-button>
                </el-form>
            </div>
        </el-card>
    </div>
</template>
<script setup>

import  { ref, computed } from 'vue';
import { useAuthStore } from '@/Stores/auth';
import { useAppBaseStore } from "@/Stores/base";
import { useI18n } from 'vue-i18n';
import { useRouter } from "vue-router";
import { useTitle } from '@vueuse/core';


const { t } = useI18n();
useTitle(t('common.login'));

const router = useRouter();
const formRef = ref(null);
const form = ref({
  email: "",
  password: "",
});

const formRules = ref({
    email: [
        { required: true, message: t('validation.required', { attribute: t('common.email') }), trigger: 'blur' },
    ],
    password: [
        { required: true, message: t('validation.required', { attribute: t('common.password') }), trigger: 'blur' },
    ],
});
const emailError = ref('');
const loading = ref(false);

const authStore = useAuthStore();
const appBase = useAppBaseStore();
const app = computed(() => appBase.app);


const clearEmailError = () => {

};
const submit = async () => {
    if (!formRef.value) return;
    formRef.value.validate(async (valid) => {
        if (valid) {
            loading.value = true;
            try {
                await authStore.login(form.value);
                ElMessage({
                    message: t('message.success_login'),
                    type: 'success',
                });
                router.replace('/dashboard');
            } catch (error) {
                // Handle error dari backend
                if (error.validation?.email) {
                    emailError.value = error.validation?.email;
                }

                ElMessage({
                    message: t('message.error_login'),
                    type: 'error',
                });
            } finally {
                loading.value = false;
            }
        } else {
            ElMessage({
                message: t('message.error_input'),
                type: 'error',
            });
        }
    });
};

</script>