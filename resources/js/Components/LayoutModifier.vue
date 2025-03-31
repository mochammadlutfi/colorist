<template>
    <component
        :is="tag"
        :type="tag === 'button' ? 'button' : false"
        :href="tag === 'a' ? '#' : false"
        @click.prevent="executeAction">
        <slot></slot>
    </component>
</template>
  
<script setup>
import { useAppBaseStore } from '@/Stores/base';
import { defineProps }from 'vue';


const props = defineProps({
    tag: {
        type: String,
        default: 'button',
        description: 'The HTML tag of the component (button, a)'
    },
    size: {
        type: String,
        description: 'Button size (sm, lg)'
    },
    variant: {
        type: String,
        default: 'primary',
        description: 'Button variant (primary, alt-primary, outline-primary, secondary, alt-secondar' +
                'y, outline-secondary, light, alt-light, outline-light, dark, alt-dark, outline' +
                '-dark, danger, alt-danger, outline-danger, info, alt-info, outline-info, succe' +
                'ss, alt-success, outline-success, warning, alt-warning, outline-warning, dual)'
    },
    action: {
        type: String,
        description: 'Specify the layout modifier mode to apply on click'
    }
});

const appBase = useAppBaseStore();

const layoutAPI = {
    sidebarOpen: () => appBase.sidebar('open'),
    sidebarClose: () => appBase.sidebar('close'),
    sidebarToggle: () => appBase.sidebar('toggle'),
    //   sidebarPositionLeft: () => store.commit('sidebarPosition', { mode: 'left' }),
    //   sidebarPositionRight: () => store.commit('sidebarPosition', { mode: 'right' }),
    //   sidebarPositionToggle: () => store.commit('sidebarPosition', { mode: 'toggle' }),
    //   sidebarStyleDark: () => store.commit('sidebarStyle', { mode: 'dark' }),
    //   sidebarStyleLight: () => store.commit('sidebarStyle', { mode: 'light' }),
    //   sidebarStyleToggle: () => store.commit('sidebarStyle', { mode: 'toggle' }),
    sidebarMiniOn: () => appBase.sidebarMini('on'),
    sidebarMiniOff: () => appBase.sidebarMini('off'),
    sidebarMiniToggle: () => appBase.sidebarMini('toggle'),
    //   sideOverlayOpen: () => store.commit('sideOverlay', { mode: 'open' }),
    //   sideOverlayClose: () => store.commit('sideOverlay', { mode: 'close' }),
    //   sideOverlayToggle: () => store.commit('sideOverlay', { mode: 'toggle' }),
    //   sideOverlayHoverOn: () => store.commit('sideOverlayHover', { mode: 'on' }),
    //   sideOverlayHoverOff: () => store.commit('sideOverlayHover', { mode: 'off' }),
    //   sideOverlayHoverToggle: () => store.commit('sideOverlayHover', { mode: 'toggle' }),
    //   headerFixed: () => store.commit('header', { mode: 'fixed' }),
    //   headerStatic: () => store.commit('header', { mode: 'static' }),
    //   headerToggle: () => store.commit('header', { mode: 'toggle' }),
    //   headerStyleDark: () => store.commit('headerStyle', { mode: 'dark' }),
    //   headerStyleLight: () => store.commit('headerStyle', { mode: 'light' }),
    //   headerStyleToggle: () => store.commit('headerStyle', { mode: 'toggle' }),
    //   headerSearchOn: () => store.commit('headerSearch', { mode: 'on' }),
    //   headerSearchOff: () => store.commit('headerSearch', { mode: 'off' }),
    //   headerSearchToggle: () => store.commit('headerSearch', { mode: 'toggle' }),
    //   headerLoaderOn: () => store.commit('headerLoader', { mode: 'on' }),
    //   headerLoaderOff: () => store.commit('headerLoader', { mode: 'off' }),
    //   headerLoaderToggle: () => store.commit('headerLoader', { mode: 'toggle' }),
    //   pageLoaderOn: () => store.commit('pageLoader', { mode: 'on' }),
    //   pageLoaderOff: () => store.commit('pageLoader', { mode: 'off' }),
    //   pageLoaderToggle: () => store.commit('pageLoader', { mode: 'toggle' }),
    //   pageOverlayOn: () => store.commit('pageOverlay', { mode: 'on' }),
    //   pageOverlayOff: () => store.commit('pageOverlay', { mode: 'off' }),
    //   pageOverlayToggle: () => store.commit('pageOverlay', { mode: 'toggle' }),
    //   mainContentFull: () => store.commit('mainContent', { mode: 'full' }),
    //   mainContentBoxed: () => store.commit('mainContent', { mode: 'boxed' }),
    //   mainContentNarrow: () => store.commit('mainContent', { mode: 'narrow' }),
};

const executeAction = () => {
    if (props.action && layoutAPI[props.action]) {
        layoutAPI[props.action]();
    }
};

</script>