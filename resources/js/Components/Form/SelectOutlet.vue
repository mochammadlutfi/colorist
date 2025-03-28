<template>
    <el-select v-model="value" value-key="id" 
    class="w-100"
    filterable 
    clearable 
    remote
    :multiple="multiple"
    @change="selectChange"
    autocomplete="off"
    :placeholder="placeholder"
    :loading="isLoading">
        <el-option
            v-for="item in data"
            :key="item.id"
            :label="item.name"
            :value="item.id"
        />
    </el-select>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import { useQuery } from '@tanstack/vue-query';

const emit = defineEmits(['update:modelValue'])
// Define props
const props = defineProps({
    modelValue: {
        type: Number,
        default: null
    },
    placeholder : {
        type : String,
        default : 'Select'
    },
    multiple : {
        type : Boolean,
        default : false
    }
});

const params = ref({
    q : ""
});
const value = ref(props.modelValue);

watch(() => props.modelValue, (newValue) => {
    value.value = newValue;
    refetch();
});

const fetchData = async ({
    queryKey
}) => {
    const [_key, queryParams] = queryKey;
    const response = await axios.get("/outlet", {
        params: queryParams,
    });
    return response.data;
};

const {
    data,
    isLoading,
    isError,
    error,
    refetch
} = useQuery({
    queryKey: ['selectOutlet', params.value], // Query key unik
    queryFn: fetchData,
    keepPreviousData: true,
});

// Emit value change
const selectChange = (newValue) => {
    value.value = newValue;
    emit('update:modelValue', newValue);
};
</script>