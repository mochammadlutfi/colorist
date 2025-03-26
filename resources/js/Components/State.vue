<template>
    <el-tag :type="tagType">
        {{ $t(`common.${modelValue}`) }}
    </el-tag>
</template>

<script setup>
import { defineProps, computed } from 'vue';

const props = defineProps({
    modelValue : String,
});

const tagType = computed(() => {
    const typeMapping = {
        draft: 'warning',
        confirmed: 'primary',
        unpaid : 'danger',
        paid : 'success',
        partially : 'info',
        cancel : 'danger',
        delivered : 'success',
        sent : 'info',
        draft : 'warning',
        received : 'success',
    };

    // Default type is 'danger' if modelValue is not in the mapping
    return typeMapping[props.modelValue] || 'danger';
});
</script>