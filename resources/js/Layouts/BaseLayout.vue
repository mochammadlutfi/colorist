<template>
    <div id="page-wrapper" :class="classContainer">
        <base-sidebar/>

        <base-header/>
        <main id="page-container">
            <slot />
        </main>

    </div>
</template>
<script>
import BaseSidebar from './Partials/BaseSidebar.vue';
import BaseHeader from './Partials/BaseHeader.vue';
import { useAppBaseStore } from '@/Stores/base';

export default {
    name : "BaseLayout",
    components : {
        BaseSidebar,
        BaseHeader
    },
        data() {
            return {
                appBase : useAppBaseStore(),
            }
        },
        computed:{
            classContainer () {
                return {
                    'sidebar-r': this.appBase.layout.sidebar && !this.appBase.settings.sidebarLeft,
                    'sidebar-mini': this.appBase.layout.sidebar && this.appBase.settings.sidebarMini,
                    'sidebar-o':  this.appBase.layout.sidebar && this.appBase.settings.sidebarVisibleDesktop,
                    'sidebar-o-xs': this.appBase.layout.sidebar && this.appBase.settings.sidebarVisibleMobile,
                    'sidebar-dark': this.appBase.layout.sidebar && this.appBase.settings.sidebarDark,
                    'side-overlay-o': this.appBase.layout.sideOverlay && this.appBase.settings.sideOverlayVisible,
                    'side-overlay-hover': this.appBase.layout.sideOverlay && this.appBase.settings.sideOverlayHoverable,
                    'enable-page-overlay': this.appBase.layout.sideOverlay && this.appBase.settings.pageOverlay,
                    'page-header-fixed': this.appBase.layout.header && this.appBase.settings.headerFixed,
                    'page-header-dark': this.appBase.layout.header && this.appBase.settings.headerDark,
                    'main-content-boxed': this.appBase.settings.mainContent === 'boxed',
                    'main-content-narrow': this.appBase.settings.mainContent === 'narrow',
                    'rtl-support': this.appBase.settings.rtlSupport,
                    'side-trans-enabled': this.appBase.settings.sideTransitions,
                    'side-scroll': true
                }
            }
        },
}
</script>