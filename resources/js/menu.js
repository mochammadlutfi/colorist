/*
 * Main and demo navigation arrays
 */

export default {
    main: [
        {
            name: "base.dashboard",
            to: "/dashboard",
            icon: "fluent:home-24-regular",
            permission: "",
            role: [],
        },
        {
            name: "Upload Data",
            to: "/upload",
            icon: "line-md:file-upload",
            permission: "",
            role: ["Admin", "Sales", "MOS"],
        },
        {
            name: "Transaksi",
            to: "/transaction",
            icon: "iconoir:fill-color-solid",
            permission: "",
            role: ["Admin", "Sales", "MOS"],
        },
        {
            name: "Outlet",
            to: "/outlet",
            icon: "mynaui:store",
            permission: "",
            role: ["Admin"],
        },
        {
            name: "base.setting",
            icon: "solar:settings-linear",
            subActivePaths: "/settings/",
            sub: [
                {
                    name: "base.system",
                    to: "/settings/system",
                    permission: "",
                    role: ["Admin"],
                },
                {
                    name: "base.user",
                    to: "/settings/user",
                    permission: "",
                    role: ["Admin"],
                },
                // {
                //     name: 'base.role_permission',
                //     to: '/settings/permission',
                //     permission : ''
                // },
                {
                    name: "Branch",
                    to: "/settings/branch",
                    permission: "",
                    role: ["Admin"],
                },
                {
                    name: "base.colorant",
                    to: "/settings/colorant",
                    permission: "",
                    role: ["Admin"],
                },
                {
                    name: "base.product",
                    to: "/settings/product",
                    permission: "",
                    role: ["Admin"],
                },
                {
                    name: "base.base_paint",
                    to: "/settings/base-paint",
                    permission: "",
                    role: ["Admin"],
                },
            ],
        },
    ],
};
