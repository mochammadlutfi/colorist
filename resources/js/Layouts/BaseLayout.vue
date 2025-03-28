<template>
    <div id="page-wrapper" :class="classContainer">
        <base-sidebar/>

        <base-header/>
        <main id="page-container">
            <slot />
        </main>

    </div>
</template>
<script setup>
import BaseSidebar from './Partials/BaseSidebar.vue';
import BaseHeader from './Partials/BaseHeader.vue';
import { useAppBaseStore } from '@/Stores/base';
import { useAuthStore } from '@/Stores/auth';
import { storeToRefs } from "pinia";
import echo from "@/Plugins/echo";
import { ref, computed, watch } from 'vue';
import { ElNotification } from "element-plus";


const authStore = useAuthStore();
const { user } = storeToRefs(authStore); 

// Computed untuk menentukan channel
const uploadChannel = computed(() => {
    return user.value?.id ? `uploads.${user.value.id}` : null;
});

// Watch perubahan user untuk listen event
watch(uploadChannel, (channel) => {
    if (!channel) return; // Jika user belum login, hentikan

    console.log(`Mencoba listen pada channel: ${channel}`);

    echo.private(channel)
        .listen(".upload.processed", (data) => {
            // const status = data.upload.status === "completed" ? "success" : "error";
            let status = 'info';
            let message = '';
            if(data.upload.status == 'pending'){
                status = 'warning';
                message =  'Upload Data akan segera di proses';    
            }else if(data.upload.status == 'uploaded'){
                status = 'success';
                message =  'Upload Data berhasil di simpan';   
            }else if(data.upload.status == 'completed'){
                status = 'success';
                message =  'Upload Data berhasil di kirim';   
            }else{
                status = 'error';
                message =  'Upload Data Gagal, periksa file yang di upload';   
            }

            ElNotification({
                title: "Upload Status",
                message: message,
                type: status,
            });
        });
});
const appBase = useAppBaseStore();

const classContainer = computed(() => ({
    'sidebar-r': appBase.layout.sidebar && !appBase.settings.sidebarLeft,
    'sidebar-mini': appBase.layout.sidebar && appBase.settings.sidebarMini,
    'sidebar-o': appBase.layout.sidebar && appBase.settings.sidebarVisibleDesktop,
    'sidebar-o-xs': appBase.layout.sidebar && appBase.settings.sidebarVisibleMobile,
    'sidebar-dark': appBase.layout.sidebar && appBase.settings.sidebarDark,
    'side-overlay-o': appBase.layout.sideOverlay && appBase.settings.sideOverlayVisible,
    'side-overlay-hover': appBase.layout.sideOverlay && appBase.settings.sideOverlayHoverable,
    'enable-page-overlay': appBase.layout.sideOverlay && appBase.settings.pageOverlay,
    'page-header-fixed': appBase.layout.header && appBase.settings.headerFixed,
    'page-header-dark': appBase.layout.header && appBase.settings.headerDark,
    'main-content-boxed': appBase.settings.mainContent === 'boxed',
    'main-content-narrow': appBase.settings.mainContent === 'narrow',
    'rtl-support': appBase.settings.rtlSupport,
    'side-trans-enabled': appBase.settings.sideTransitions,
    'side-scroll': true
}));
</script>