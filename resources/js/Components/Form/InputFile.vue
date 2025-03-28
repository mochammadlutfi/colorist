<template>
    <div class="max-w-full w-full flex items-center">
        <!-- Label untuk input file -->
        <label class="el-button flex items-center justify-center transition-all duration-300"
            :class="hasFile ? 'w-3/4' : 'w-full'">
            <span v-if="!modelValue">{{ $t('common.choose_file') }}</span>
            <span v-else class="truncate">{{ displayFileName }}</span>
            <input type="file" name="file" class="!hidden" @change="handleFileChange" :accept="accept"/>
        </label>

        <!-- Tombol Hapus -->
        <el-button v-if="hasFile" @click="clearFile" type="danger" class="w-1/5">
            <i class="el-icon">
                <svg xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 1024 1024">
                    <path fill="currentColor"
                        d="m466.752 512-90.496-90.496a32 32 0 0 1 45.248-45.248L512 466.752l90.496-90.496a32 32 0 1 1 45.248 45.248L557.248 512l90.496 90.496a32 32 0 1 1-45.248 45.248L512 557.248l-90.496 90.496a32 32 0 0 1-45.248-45.248z">
                    </path>
                    <path fill="currentColor"
                        d="M512 896a384 384 0 1 0 0-768 384 384 0 0 0 0 768m0 64a448 448 0 1 1 0-896 448 448 0 0 1 0 896">
                    </path>
                </svg></i>
        </el-button>
    </div>
</template>

<script setup>
    import { defineProps, defineEmits, computed, watch } from 'vue';

    const props = defineProps({
        modelValue: {
            type: [File, String], // Menerima File atau String
            default: null,
        },
        accept : {
            type : String,
            default : "",
        }
    });

    const emit = defineEmits(['update:modelValue']);

    const hasFile = computed(() => {
        return props.modelValue !== null && props.modelValue !== undefined && props.modelValue !== '';
    });

    const displayFileName = computed(() => {
        if (props.modelValue instanceof File) {
            return props.modelValue.name; // Jika File, ambil nama file
        } else if (typeof props.modelValue === 'string') {
            // Jika string, ambil nama file dari path (misal: 'path/to/file.txt' -> 'file.txt')
            return props.modelValue.split('/').pop() || props.modelValue;
        }
        return '';
    });

    
    watch(() => props.modelValue, (newValue) => {

    });
    
    const handleFileChange = (event) => {
        const selectedFile = event.target.files[0];
        if (selectedFile) {
            emit('update:modelValue', selectedFile);
        }
    };

    const clearFile = () => {
        emit('update:modelValue', null);
    };
</script>
