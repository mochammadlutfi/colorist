// src/composables/usePageTitle.js
import { useTitle } from '@vueuse/core';
import { ref, computed } from 'vue';
import { useAppBaseStore } from '@/Stores/base';
import { useAuthStore } from '@/Stores/auth';

export function useHead() {
    const base = useAppBaseStore();
    const dynamicTitle = ref(''); 

    const pageTitle = computed(() => {
        return dynamicTitle.value ? `${dynamicTitle.value} | ${base.app.app_name}` : base.app.app_name;
    });

    useTitle(pageTitle);

    function setTitle(title) {
        dynamicTitle.value = title;
    }


    return {
        setTitle
    };
}

export function useBase(){
    const authStore = useAuthStore();
    const permissions = computed(() => authStore.permissions);
    console.log(permissions.value);
    const can = (permission) => {
        if (!permission || permissions.length === 0) {
          return false;
        }
    
        // Jika permission adalah array, periksa apakah semua izin ada di daftar izin pengguna
        if (Array.isArray(permission)) {
          return permission.every(perm => permissions.value.includes(perm));
        }
    
        // Jika permission adalah string, periksa apakah izin ada di daftar izin pengguna
        return permissions.value.includes(permission);
    };

    return {
        can
    };
}