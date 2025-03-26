import './bootstrap';

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './Router'
import '@/Plugins/axios';
import setupI18n from '@/Plugins/i18n';
import { VueQueryPlugin } from '@tanstack/vue-query';
import { createHead } from '@unhead/vue'
import { abilitiesPlugin } from '@casl/vue';
import { ability } from './Plugins/ability';
import VueBlocksTree from 'vue3-blocks-tree';

(async () => {
    const i18n = await setupI18n();
    const app = createApp(App)
    app.use(createPinia())
    app.use(router)
    app.use(createHead)
    app.use(i18n)
    app.use(VueQueryPlugin);
    app.use(abilitiesPlugin, ability);
    app.use(VueBlocksTree,{treeName:'blocks-tree'})
    app.mount('#app');
})();