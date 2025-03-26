/*
 * Main and demo navigation arrays
 */

export default {
    'main': [
        {
            name: 'base.dashboard',
            to: '/dashboard',
            icon: 'fluent:home-24-regular',
            permission : ""
        },
        {
            name: 'Upload Data',
            to: '/upload',
            icon: 'line-md:file-upload',
            permission : ""
        },
        {
            name: 'Transaksi',
            to: '/transaction',
            icon: 'iconoir:fill-color-solid',
            permission : ""
        },
        {
            name: 'Outlet',
            to: '/outlet',
            icon: 'mynaui:store',
            permission : ""
        },
        {
            name: 'base.setting',
            icon: 'solar:settings-linear',
            subActivePaths: '/settings/',
            sub: [{
                    name: 'base.system',
                    to: '/settings/system',
                    permission : ''
                },
                {
                    name: 'base.user',
                    to: '/settings/user',
                    permission : 'user.view'
                },
                {
                    name: 'base.role_permission',
                    to: '/settings/permission',
                    permission : ''
                },
                {
                    name: 'Branch',
                    to: '/settings/branch',
                    permission : ''
                },
                {
                    name: 'base.colorant',
                    to: '/settings/colorant',
                    permission : ''
                },
                {
                    name: 'base.product',
                    to: '/settings/product',
                    permission : ''
                },
                {
                    name: 'base.base_paint',
                    to: '/settings/base-paint',
                    permission : ''
                },
            ]
        },
    ],
}