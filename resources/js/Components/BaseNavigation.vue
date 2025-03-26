<template>
    <ul :class="classContainer">
        <li v-for="(node, index) in nodes" :key="`node-${index}`" :class="{
        'menu-title': node.heading,
        'menu-item': !node.heading,
        'open': node.sub && node.subActivePaths ? subIsActive(node.subActivePaths) : false}">
            {{ node.heading ? node.name : '' }}
            <template v-if="node.sub">
                <a v-if="!node.heading" href="#" class="menu-link"
                    :class="{ 'active' : route().current(node.to) == true }"
                    :event="node.sub ? '' : 'click'" @click="linkClicked($event, node.sub)">
                <i v-if="node.icon" :class="`${node.icon}`" class="menu-icon"></i>
                <span v-if="node.name" class="menu-text">{{ node.name }}</span>
                <span class="menu-arrow"></span>
                </a>
            </template>
            <template v-else>
                <Link v-if="!node.heading" :href="node.to ? route(node.to) : '#'"
                    :class="(route().current(node.to)) ? 'active' : ''" class="menu-link"
                    :event="node.sub ? '' : 'click'" @click="linkClicked($event, node.sub)">
                <span class="menu-icon" v-if="node.icon"><i :class="`${node.icon}`"></i></span>
                <span v-if="node.name" class="menu-text">{{ node.name }}</span>
                <span v-if="node.badge" class="nav-main-link-badge badge badge-pill badge-primary"
                    :class="node['badge-variant'] ? `badge-${node['badge-variant']}` : 'badge-primary' ">{{ node.badge }}</span>
                </Link>
            </template>
            <base-navigation v-if="node.sub" :nodes="node.sub" sub-menu></base-navigation>
        </li>
    </ul>
</template>

<script>
    import { Link } from '@inertiajs/vue3';
    export default {
        name: 'BaseNavigation',
        components: {
            Link
        },
        props: {
            nodes: {
                type: Array,
                description: 'The nodes of the navigation'
            },
            subMenu: {
                type: Boolean,
                default: false,
                description: 'If true, a submenu will be rendered'
            },
            dark: {
                type: Boolean,
                default: false,
                description: 'Dark mode for menu'
            },
            horizontal: {
                type: Boolean,
                default: false,
                description: 'Horizontal menu in large screen width'
            },
            horizontalHover: {
                type: Boolean,
                default: false,
                description: 'Hover mode for horizontal menu'
            },
            horizontalCenter: {
                type: Boolean,
                default: false,
                description: 'Center mode for horizontal menu'
            },
            horizontalJustify: {
                type: Boolean,
                default: false,
                description: 'Justify mode for horizontal menu'
            }
        },
        computed: {
            classContainer() {
                return {
                    'menu': !this.subMenu,
                    'sub-menu hidden': this.subMenu,
                    'nav-main-dark': this.dark,
                    'nav-main-horizontal': this.horizontal,
                    'nav-main-hover': this.horizontalHover,
                    'nav-main-horizontal-center': this.horizontalCenter,
                    'nav-main-horizontal-justify': this.horizontalJustify
                }
            }
        },
        methods: {
            subIsActive(paths) {
                const activePaths = Array.isArray(paths) ? paths : [paths]
                if(this.horizontal){
                    return false;
                }
                return this.route().current(paths);
            },
            linkClicked(e, submenu) {
                // Get window width
                let windowW = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth
                // console.log(e.target);
                if (submenu) {
                    // Get closest li element
                    let el = e.target;
                    let submenu = el.nextElementSibling;

                    if (el.classList.contains('open')) {
                        el.classList.remove('open')
                        if(submenu){
                            submenu.classList.add('hidden');
                        }
                    } else {
                        var children = Array.prototype.slice.call(el.closest('ul').children);
                        children.forEach(element => {
                            element.classList.remove('open')
                        })

                        if(submenu){
                            submenu.classList.remove('hidden');
                        }

                        el.classList.add('open');
                    }
                } else {
                    // If we are in mobile, close the sidebar
                    if (windowW < 992) {
                        this.$store.commit('sidebar', {
                            mode: 'close'
                        })
                    }
                }
            }
        }
    }

</script>
