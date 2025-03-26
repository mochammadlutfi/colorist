<template>
    <div v-if="loading" class="loading-screen">
      <div class="spinner"></div>
      <p>Loading, please wait...</p>
    </div>
    <el-config-provider  v-else>
      <Component :is="currentLayout" v-if="route">
        <RouterView />
      </Component>
    </el-config-provider>
  </template>
  
  <script setup>
  import { useRoute, RouterView } from 'vue-router';
  import BaseLayout from '@/Layouts/BaseLayout.vue';
  import GuestLayout from '@/Layouts/GuestLayout.vue';
  import { ref, onMounted, computed } from 'vue';
  import { useAppBaseStore } from "@/Stores/base";
  
  // Store untuk inisialisasi aplikasi
  const appBase = useAppBaseStore();

  
  // Data route dan layout
  const route = useRoute();
  const currentLayout = computed(() => {
    const layouts = new Map([
      ['BaseLayout', BaseLayout],
      ['GuestLayout', GuestLayout],
    ]);
    return layouts.get(`${route.meta.layout || 'Base'}Layout`) || BaseLayout;
  });

  onMounted(async () => {
      localStorage.setItem("vueuse-color-scheme", "light");
      document.documentElement.classList.remove("dark");

      if (!appBase.isInitialized) {
          appBase.initApp();
      }
  });

    const loading = computed(() => appBase.loading);

</script>

<style scoped>
.loading-screen {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background-color: rgba(255, 255, 255, 0.9);
  z-index: 9999;
}
.spinner {
  width: 50px;
  height: 50px;
  border: 5px solid #ccc;
  border-top-color: #007bff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>