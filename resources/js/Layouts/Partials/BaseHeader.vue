<template>
    <el-header id="page-header" class="flex justify-between items-center px-4 gap-3">
        <!-- Sidenav Menu Toggle Button -->
        
        
        <!-- Tombol ini hanya tampil pada layar ≥ 992px (tablet besar & desktop) -->
        <layout-modifier 
            tag="button" 
            id="button-toggle-menu-desktop" 
            action="sidebarMiniToggle" 
            class="hidden lg:flex nav-link p-2 me-auto">
            <span class="flex items-center justify-center h-6 w-6">
                <Icon icon="fluent:line-horizontal-3-20-filled" class="text-2xl"/>
            </span>
        </layout-modifier>

        <!-- Tombol ini hanya tampil pada layar < 992px (mobile & tablet kecil) -->
        <layout-modifier 
            tag="button" 
            id="button-toggle-menu-mobile" 
            action="sidebarToggle" 
            class="flex lg:hidden p-2 nav-link">
            <span class="flex items-center justify-center h-6 w-6">
                <Icon icon="fluent:line-horizontal-3-20-filled" class="text-2xl"/>
            </span>
        </layout-modifier>


        <!-- Language Dropdown Button -->
        <div class="relative">
            <el-dropdown trigger="click" @command="handleLocale">
                <button data-fc-type="dropdown" data-fc-placement="bottom-end" type="button" class="p-2 fc-dropdown">
                    <span class="flex items-center justify-center h-6 w-6">
                         <Icon :icon="`circle-flags:lang-${locale}`" class="text-2xl"/>
                    </span>
                </button>
                <template #dropdown>
                    <el-dropdown-menu>
                        <el-dropdown-item v-for="(value, key) in app.locales" :key="key" :command="value.language" class="flex items-center gap-2.5 py-2 px-3">
                            <Icon :icon="`circle-flags:lang-${value.language}`" class="h-4 w-6"/>
                            <span class="align-middle">{{ value.name }}</span>
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </template>
            </el-dropdown>
        </div>
        
        <!-- Profile Dropdown Button -->
        <div class="relative">
            <el-dropdown trigger="click" v-if="user">
                <button data-fc-type="dropdown" data-fc-placement="bottom-end" type="button">
                    <div class="flex align-middle">
                        <img :src="user.image_url" alt="user-image" class="rounded-md h-8">
                        <div class="ml-2 text-left my-auto">
                            <div>{{ user.name }}</div>
                            <div class="text-sm">{{ user.email }}</div>
                        </div>
                    </div>
                </button>
                <template #dropdown>
                    <el-dropdown-menu class="w-44 p-2 rounded-md">
                        <el-dropdown-item class="rounded">
                            <router-link to="/profile" class="flex align-middle">
                                <icon icon="hugeicons:user-square" class="me-3.5 w-5" width="24" height="24"/>
                                <div>{{ $t('common.profile') }}</div>
                            </router-link>
                        </el-dropdown-item>
                        <el-dropdown-item class="rounded">
                            <router-link to="/profile/password" class="flex align-middle">
                                <icon icon="hugeicons:lock-password" class="me-3.5 w-5" width="24" height="24"/>
                                <div>{{ $t('common.password')}}</div>
                            </router-link>
                        </el-dropdown-item>
                        <el-dropdown-item @click.prevent="logout" class="rounded flex align-middle">
                            <icon icon="hugeicons:logout-03" class="me-3.5 w-5" width="24" height="24"/>
                            <span>{{ $t('common.logout') }}</span>
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </template>
            </el-dropdown>
        </div>
    </el-header>
</template>

<style scoped>
.nav-link {
    border-radius: 9999px;
    background-color: transparent;
    -webkit-transition-property: all;
    transition-property: all;
    -webkit-transition-duration: 150ms;
    transition-duration: 150ms;
    -webkit-transition-timing-function: cubic-bezier(0.4,0,0.2,1);
    transition-timing-function: cubic-bezier(0.4,0,0.2,1);
}
</style>

<script setup>
import LayoutModifier from '@/Components/LayoutModifier.vue';
import { useAppBaseStore } from '@/Stores/base';
import { useDark, useToggle } from "@vueuse/core";
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { Icon } from '@iconify/vue';
import { useAuthStore } from '@/Stores/auth';
import { useRouter } from "vue-router";
const { t, locale } = useI18n({ useScope: 'global' })

const router = useRouter();
const appBase = useAppBaseStore();
const auth = useAuthStore();
const darkMode = useDark();
const app = computed(() => appBase.app);
const user = computed(() => useAuthStore().user);

const toggleDark = useToggle(darkMode);

const isDark = computed(() => (darkMode.value ? 'solar:sun-linear' : 'solar:moon-linear'));

const handleLocale = async (lang) => {
    locale.value = lang;
    appBase.setLocale(lang);
};

const logout = async () => {
  await auth.logout();
  router.replace('/');
};


</script>
