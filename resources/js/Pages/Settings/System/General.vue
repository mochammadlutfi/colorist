<template>
    <el-card class="shadow-sm sm:rounded-lg" v-loading="loading">
        <el-form :model="form" @submit.prevent="onSubmit" label-position="top">
            <el-row :gutter="20">
                <el-col :md="12">
                    <el-form-item :label="$t('setting.app_name')" prop="app_name">
                        <el-input v-model="form.app_name" />
                    </el-form-item>
                </el-col>
                <el-col :md="12">
                    <el-form-item :label="$t('setting.company_name')" prop="company_name">
                        <el-input v-model="form.company_name" />
                    </el-form-item>
                </el-col>
                <el-col :md="12">
                    <el-form-item :label="$t('setting.company_email')" prop="company_email">
                        <el-input v-model="form.company_email" />
                    </el-form-item>
                </el-col>
                <el-col :md="12">
                    <el-form-item :label="$t('setting.company_phone')" prop="company_phone">
                        <el-input v-model="form.company_phone" />
                    </el-form-item>
                </el-col>
            </el-row>
            <el-form-item :label="$t('setting.company_address')" >
                <el-input type="textarea" v-model="form.company_address" />
            </el-form-item>
            <el-row :gutter="20">
                <el-col :md="12">
                    <el-form-item :label="$t('setting.date_format')">
                        <select-date-format v-model="form.date_format"/>
                    </el-form-item>
                </el-col>
                <el-col :md="12">
                    <el-form-item :label="$t('setting.time_format')">
                        <select-time-format v-model="form.time_format"/>
                    </el-form-item>
                </el-col>
                <el-col :md="12">
                    <el-form-item :label="$t('setting.timezone')">
                        <select-time-zone v-model="form.timezone"/>
                    </el-form-item>
                </el-col>
                <el-col :md="12">
                    <el-form-item :label="$t('setting.default_language')">
                        <select-locale v-model="form.locale"/>
                    </el-form-item>
                </el-col>
            </el-row>
            <el-row :gutter="20">
                <el-col :md="12">
                    <el-form-item :label="$t('setting.logo_light')" >
                        <image-upload v-model="form.logo_light" class="!h-20 !w-96" :cropper="false"/>
                    </el-form-item>
                </el-col>
                <el-col :md="12">
                    <el-form-item :label="$t('setting.logo_dark')" >
                        <image-upload v-model="form.logo_dark" class="!h-20 !w-96" :cropper="false"/>
                    </el-form-item>
                </el-col>
                <el-col :md="8">
                    <el-form-item :label="$t('setting.logo_light_sm')" >
                        <image-upload v-model="form.logo_light_sm" :cropper="false"/>
                    </el-form-item>
                </el-col>
                <el-col :md="8">
                    <el-form-item :label="$t('setting.logo_dark_sm')" >
                        <image-upload v-model="form.logo_dark_sm" :cropper="false"/>
                    </el-form-item>
                </el-col>
                <el-col :md="8">
                    <el-form-item :label="$t('setting.favicon')" >
                        <image-upload v-model="form.favicon" :cropper="false"/>
                    </el-form-item>
                </el-col>
            </el-row>
            <div class="text-right">
                <el-button native-type="submit" type="primary">
                    {{ $t('common.save') }}
                </el-button>
            </div>
        </el-form>
    </el-card>
</template>

<script setup>
import { defineProps, onMounted, defineEmits, ref } from 'vue';
import SelectDateFormat from '@/Components/Form/SelectDateFormat.vue';
import SelectTimeFormat from '@/Components/Form/SelectTimeFormat.vue';
import SelectTimeZone from '@/Components/Form/SelectTimeZone.vue';
import SelectLocale from '@/Components/Form/SelectLocale.vue';
import ImageUpload from '@/Components/Form/ImageUpload.vue';
import { useI18n } from 'vue-i18n';
import { convertToFormData } from '@/Plugins/utility';
import { useAppBaseStore } from "@/Stores/base";
import { useHead } from '@/Composable/useBase.js';
const appBase = useAppBaseStore();
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
    app_name : null,
    company_name : null,
    company_email : null,
    company_phone : null,
    company_address : null,
    logo_light : null,
    logo_dark : null,
    logo_light_sm : null,
    logo_dark_sm : null,
    timezone : null,
    date_format : "d-m-Y",
    time_format : "HH:mm",
    locale : null
});

const errors = ref({});
const loading = ref(false);

const onSubmit = async () => {
    loading.value = true;
    const url = '/settings/general/update';
    try {
        const formData = convertToFormData(form.value);
        const response = await axios.post(url, formData,{
            headers: {
                "Content-Type": "multipart/form-data"
            },
        });
        appBase.reinitApp();
        ElMessage({message: t('success_msg'), type: 'success'});
    } catch (error) {
        errors.value = error.validation;
        ElMessage({
            message: t('message.error_server'),
            type: 'error',
        });
    } finally {
        loading.value = false;
    }
};

const fetchData = async () => {
  try {
    errors.value = {};
    loading.value = true;
    const response = await axios.get('/settings/general');
    if (response.status === 200) {
      form.value.app_name = response.data.app_name;
      form.value.company_name = response.data.company_name;
      form.value.company_email = response.data.company_email;
      form.value.company_phone = response.data.company_phone;
      form.value.company_address = response.data.company_address;
      form.value.logo_light = response.data.logo_light;
      form.value.logo_dark = response.data.logo_dark;
      form.value.logo_light_sm = response.data.logo_light_sm;
      form.value.logo_dark_sm = response.data.logo_dark_sm;
      form.value.time_format = response.data.time_format;
      form.value.date_format = response.data.date_format;
      form.value.timezone = response.data.timezone;
      form.value.locale = response.data.locale;
      form.value.favicon = response.data.favicon;
    }
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
    setTitle(t('base.general_setting', 2));
    emit('childinit', 'general_setting');
    fetchData();
});
</script>


