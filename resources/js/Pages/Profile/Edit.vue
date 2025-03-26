<template>
    <el-card class="shadow-sm sm:rounded-lg" v-loading="formLoading">
        <el-form ref="formRef" :model="form" :rules="formRules" @submit.prevent="onSubmit" label-position="top">
            <el-row :gutter="20">
                <el-col :md="16">
                    <el-form-item :label="$t('common.name')" prop="name">
                        <el-input v-model="form.name" />
                    </el-form-item>
                    <el-form-item :label="$t('common.email')" prop="email">
                        <el-input v-model="form.email" />
                    </el-form-item>
                </el-col>
                <el-col :md="8">
                    <el-form-item :label="$t('common.image')">
                        <image-upload v-model="form.image" size="small"/>
                    </el-form-item>
                </el-col>
            </el-row>
            <div class="text-left">
                <el-button native-type="submit" type="primary">
                    {{ $t('common.save') }}
                </el-button>
            </div>
        </el-form>
    </el-card>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue';
import ImageUpload from '@/Components/Form/ImageUpload.vue';
import { useI18n } from 'vue-i18n';
import { useAuthStore } from '@/Stores/auth';
import { useHead } from '@/Composable/useBase.js';
import { useFormatter } from '@/Composable/useFormatter';

const { formatDate, objectToFormData } = useFormatter();
const { t } = useI18n();
const { setTitle } = useHead();
const auth = useAuthStore();

const emit = defineEmits(['childinit']);

const user = computed(() => auth.user);

const formRef = ref(null);
const formLoading = ref(false);
const form = ref({
    name : null,
    email : null,
    image : null,
});

const formRules = ref({
    name: [
        { required: true, message: t('validation.required', { attribute: t('common.name') }), trigger: 'blur' },
    ],
    email: [
        { required: true, message: t('validation.required', { attribute: t('common.email') }), trigger: 'blur' },
    ]
});

const onSubmit = async () => {
    if (!formRef.value) return;
    formRef.value.validate(async (valid) => {
        if (valid) {
            try {
                formLoading.value = true;
                await axios({
                    method : 'post',
                    url : '/profile/update',
                    data: objectToFormData(form.value),
                    headers: {
                        "Content-Type": "multipart/form-data"
                    },
                });
                auth.getUser();
                ElMessage({
                    message: t('message.success_save'),
                    type: 'success',
                });
                formLoading.value = false;
            } catch (error) {
                console.log('Form Error:',error);
                formLoading.value = false;
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
    emit('childinit', 'Edit Profile');
    setTitle('Edit Profile');
    form.value.name = user.value.name;
    form.value.email = user.value.email;
    form.value.image = user.value.image;
});

</script>


