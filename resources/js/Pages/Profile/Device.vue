<template>
    <el-card class="shadow-sm sm:rounded-lg" body-class="p-0" v-loading="loading">
        <el-table class="min-w-full sm:rounded-lg" :data="dataList" v-loading="loading">
            <el-table-column prop="name" :label="$t('common.device')" header-align="center">
                <template #default="scope">
                <div class="font-semibold">
                    {{ scope.row.name }}
                </div>
                <div class="font-medium text-sm">IP: {{ scope.row.ip }}</div>
                </template>
            </el-table-column>
            <el-table-column :label="$t('common.last_access')" header-align="center">
                <template #default="scope">
                    {{ formatDate(scope.row.last_used_at) }}
                </template>
            </el-table-column>
            <el-table-column :label="$t('common.action')" align="center">
                <template #default="scope">
                    <el-button type="primary" :disabled="scope.row.is">
                        {{ $t('common.disconnect') }}
                    </el-button>
                </template>
            </el-table-column>
        </el-table>
    </el-card>
</template>

<script setup>
import { defineProps, onMounted, defineEmits, ref } from 'vue';
import { useI18n } from 'vue-i18n';
const { t } = useI18n();
import { useHead } from '@/Composable/useBase.js';
import { useFormatter } from '@/Composable/useFormatter';
const { formatDate, objectToFormData } = useFormatter();

const emit = defineEmits(['childinit']);
const {
    setTitle
} = useHead();

const form = ref({
    avatar: null,
    name: null,
    username: null,
    email: null,
});
const errors = ref({});
const loading = ref(false);
const dataList = ref([]);

const onSubmit = async () => {
    loading.value = true;
    const url = '/settings/email/update';
    try {
        const response = await axios.post(url, form.value);
        console.log(response.data.result);
        ElMessage({
            message: t('success_msg'),
            type: 'success'
        });
    } catch (error) {
        errors.value = error.validation;
        ElMessage({
            message: t('error_msg'),
            type: 'error'
        });
    } finally {
        loading.value = false;
    }
};

const fetchData = async () => {
    try {
        errors.value = {};
        loading.value = true;
        const response = await axios.get('/profile/device');
        if (response.status === 200) {
            dataList.value = response.data;
        }
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
    }
};
onMounted(() => {
    setTitle(t('common.device', 2))
    emit('childinit', t('common.device', 2));
    fetchData();
});
</script>


