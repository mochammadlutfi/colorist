<template>
    <el-card class="shadow-sm sm:rounded-lg" v-loading="loading">
        
        <el-form :model="form" @submit.prevent="onSubmit" label-position="top">
            <el-row :gutter="20">
                <el-col :md="12">
                    <el-form-item :label="$t('host')" :error="errors.mail_host">
                        <el-input v-model="form.mail_host" />
                    </el-form-item>
                    <el-form-item :label="$t('username')" :error="errors.mail_username">
                        <el-input v-model="form.mail_username" />
                    </el-form-item>
                    <el-form-item :label="$t('name')" :error="errors.mail_from_name">
                        <el-input v-model="form.mail_from_name" />
                    </el-form-item>
                    <el-form-item :label="$t('encrypt')" :error="errors.mail_encryption">
                        <el-radio-group v-model="form.mail_encryption" class="ml-4">
                            <el-radio value="ssl">SSL</el-radio>
                            <el-radio value="lts">LTS</el-radio>
                        </el-radio-group>
                    </el-form-item>
                </el-col>
                <el-col :md="12">
                    <el-form-item :label="$t('port')" :error="errors.mail_port">
                        <el-input v-model="form.mail_port" />
                    </el-form-item>
                    <el-form-item :label="$t('password')" :error="errors.mail_password">
                        <el-input v-model="form.mail_password" />
                    </el-form-item>
                    <el-form-item :label="$t('email')" :error="errors.mail_from_address">
                        <el-input v-model="form.mail_from_address" />
                    </el-form-item>
                </el-col>
            </el-row>
            <div class="text-right">
                <el-button native-type="submit" type="primary">
                    <i class="mgc_check_fill me-2"></i>
                    {{ $t('save') }}
                </el-button>
            </div>
        </el-form>
    </el-card>
</template>

<script setup>
import { defineProps, onMounted, defineEmits, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { useHead } from '@/Composable/useBase.js';

const { t } = useI18n();
const { setTitle } = useHead();

const emit = defineEmits(['childinit']);

const props = defineProps({
  title: {
    type: String,
    default: '',
  },
});

const form = ref({
    mail_host : null,
    mail_port : null,
    mail_username : null,
    mail_password : null,
    mail_encryption : 'SSL',
    mail_from_address : null,
    mail_from_name : null,
});
const errors = ref({});
const loading = ref(false);

const fetchData = async () => {
  try {
    errors.value = {};
    loading.value = true;
    const response = await axios.get('/settings/email');
    if (response.status === 200) {
      form.value.mail_host = response.data.mail_host;
      form.value.mail_port = response.data.mail_port;
      form.value.mail_username = response.data.mail_username;
      form.value.mail_password = response.data.mail_password;
      form.value.mail_encryption = response.data.mail_encryption;
      form.value.mail_from_name = response.data.mail_from_name;
    }
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
};
const onSubmit = async () => {
    loading.value = true;
    const url = '/settings/email/update';
    try {
        const response = await axios.post(url, form.value);
        console.log(response.data.result);
        ElMessage({message: t('success_msg'), type: 'success'});
    } catch (error) {
        errors.value = error.validation;
        ElMessage({message: t('error_msg'), type: 'error'});
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
  emit('childinit', 'email_setting');
  setTitle(t('base.email_setting', 2));
  fetchData();
});
</script>