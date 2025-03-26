<template>
    <el-date-picker class="!w-full" v-model="input" :type="type" @change="onChangeDate"
        :format="dateFormat" value-format="YYYY-MM-DD h:m" />
</template>

<script setup>
import { onMounted, ref, watch, computed } from 'vue';
import { useAppBaseStore } from "@/Stores/base";
import dayjs from 'dayjs';

const emit = defineEmits(['update:modelValue']);
const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: ''
    },
    placeholder : {
        type : String,
        default : ''
    },
    type : {
        type : String,
        default : "datetime"
    },
    today : {
        type : Boolean,
        default :false
    }
});
const appBase = useAppBaseStore();

const input = ref(null);

const dateFormat = computed(() => {
    
    const dateFormat = appBase.app.date_format || 'YYYY-MM-DD'; 
    const timeFormat = appBase.app.time_format || 'HH:mm:ss';
        
    const formatString = props.type == 'datetime' ? `${dateFormat} ${timeFormat}` : dateFormat;

    return formatString;
});

watch(() => props.modelValue, (newValue) => {
    input.value = newValue;
});

const onChangeDate = (value) => {
    emit('update:modelValue', value);
}

onMounted(() => {
    if(props.today){
        input.value = new dayjs().format();
        emit('update:modelValue', input.value);
    }
});
</script>