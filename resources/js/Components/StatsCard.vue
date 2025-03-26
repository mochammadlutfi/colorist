<template>
    <el-card body-class="!flex !items-center !justify-between !p-5" class="!rounded-lg !shadow-md">
        <div>
            <h6 class="text-xs font-medium leading-none tracking-wider text-gray-500 uppercase">
                {{ label }}
            </h6>
            <div class="text-xl font-semibold">
                <slot></slot>
            </div>
        </div>
        <div>
            <Icon :icon="icon" class="w-12 h-12"/>
        </div>
    </el-card>
</template>

<script setup>
import { ref } from 'vue';
import { Icon } from '@iconify/vue';

const props = defineProps({
    label : String,
    value : [String,Number],
    icon : String
})

</script>