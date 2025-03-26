import SettingSystem from '../Pages/Settings/System/Index.vue';
import SystemLanguage from '../Pages/Settings/System/Languages/Index.vue';
import SystemLanguageForm from '../Pages/Settings/System/Languages/Index.vue';

export default [{
    path: '/settings/',
    children: [
        {
            path: 'system',
            name: 'settings.system',
            component: SettingSystem,
            children: [{
                    path: '',
                    name: 'settings.system.general',
                    component: () => import("../Pages/Settings/System/General.vue")
                },
                {
                    path: 'email',
                    name: 'settings.system.email',
                    component: () => import("../Pages/Settings/System/Email.vue")
                },
            ]
        },
        {
            path: 'user',
            children: [{
                    path: '',
                    name: 'settings.user.index',
                    component: () => import("../Pages/Settings/User/Index.vue")
                },
                {
                    path: 'create',
                    name: 'settings.user.create',
                    component: () => import("../Pages/Settings/User/Form.vue")
                },
                {
                    path: ':id/edit',
                    name: 'settings.user.edit',
                    component: () => import("../Pages/Settings/User/Form.vue")
                },
            ]
        },
        {
            path: 'permission',
            children: [{
                    path: '',
                    name: 'permission.index',
                    component: () => import("../Pages/Settings/Permission/Index.vue")
                },
                {
                    path: 'create',
                    name: 'permission.create',
                    component: () => import("../Pages/Settings/Permission/Form.vue")
                },
                {
                    path: ':id/edit',
                    name: 'permission.edit',
                    component: () => import("../Pages/Settings/Permission/Form.vue")
                },
            ]
        },
        {
            path: 'outlet',
            name: 'settings.outlet',
            component: () => import("../Pages/Outlet.vue")
        },
        {
            path: 'colorant',
            name: 'settings.colorant',
            component: () => import("../Pages/Settings/Colorant.vue")
        },
        {
            path: 'product',
            name: 'settings.product',
            component: () => import("../Pages/Settings/Product.vue")
        },
        {
            path: 'branch',
            name: 'settings.branch',
            component: () => import("../Pages/Settings/Branch.vue")
        },
        {
            path: 'base-paint',
            name: 'settings.base_paint',
            component: () => import("../Pages/Settings/BasePaint.vue")
        },
    ]

}]