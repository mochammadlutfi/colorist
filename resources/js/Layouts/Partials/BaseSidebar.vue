<template>
    <div id="page-sidebar">
        <div class="sidebar-content justify-end">
            <router-link to="/dashboard" class="logo">
                <!-- Light Brand Logo -->
                <div class="logo-light">
                    <img :src="app.logo_dark" class="logo-lg h-10 smini-hidden" alt="Light logo">
                    <img :src="app.logo_dark_sm" class="logo-sm smini-visible" alt="Small logo">
                </div>

                <!-- Dark Brand Logo -->
                <div class="logo-dark">
                    <img :src="app.logo_light" class="logo-lg h-10 smini-hidden" alt="Dark logo">
                    <img :src="app.logo_light_sm" class="logo-sm smini-visible" alt="Small logo">
                </div>
            </router-link>

            <el-scrollbar class="md:h-[550px] p-4">
                <base-navigation :nodes="navigation"></base-navigation>
            </el-scrollbar>
        </div>
    </div>
</template>
<script setup>
import menuList from '@/menu.js';
import { useAppBaseStore } from '@/Stores/base';
import { ref, computed, onMounted, watch } from 'vue';
import BaseNavigation from './BaseNavigation.vue';
import { useI18n } from 'vue-i18n';
import { useAuthStore } from '@/Stores/auth';
import { useAbility } from '@casl/vue';

const { can } = useAbility();
const authStore = useAuthStore();
const permissions = computed(() => authStore.permissionData);
const menu = computed(() => menuList.main);
const appBase = useAppBaseStore();
const { t } = useI18n();
const app = computed(() => appBase.app);
const navigation = ref([]);

function filterMenuByPermission(menuNodes) {
  return menuNodes.filter(node => {
    // Jika node memiliki sub-menu, filter sub-menu terlebih dahulu
    if (node.sub) {
      node.sub = filterMenuByPermission(node.sub);
      // Tampilkan menu utama jika setidaknya satu sub-menu dapat diakses
      return node.sub.length > 0;
    }

    // Jika tidak ada permission, tampilkan menu (default behavior)
    if (!node.permission) {
      return true;
    }

    // Periksa izin menggunakan CASL
    const [subject, action] = node.permission.split('.');
    const hasPermission = can(action, subject);
    // console.log(`Checking permission for ${node.name}:`,{
    //     permission : node.permission,
    //     action : action,
    //     subject : subject,
    //     hasPermission
    // })
    return hasPermission;
  });
}

// Gunakan computed untuk navigation
// const filteredNavigation = computed(() => filterMenuByPermission(menu.value));

// Pantau perubahan permissions
watch(permissions, (newPermissions) => {
  if (newPermissions && newPermissions.length > 0) {
    navigation.value = filterMenuByPermission(menu.value);
  }
}, { immediate: true });

onMounted(() => {
  if (permissions.value && permissions.value.length > 0) {
    navigation.value = filterMenuByPermission(menu.value);
  }
});

const isCollapse = computed(() => appBase.settings.sidebarMini);
</script>