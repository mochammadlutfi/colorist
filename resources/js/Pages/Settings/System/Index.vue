<template>
    <div class="content">
        
        <div class="content-header">
            <h2 class="content-title">{{ $t(`base.${title}`) }}</h2>

            <div class="md:flex hidden items-center gap-2.5 text-sm font-semibold">
                <el-breadcrumb separator="/">
                    <el-breadcrumb-item :to="{ path: '/' }">{{ $t('base.dashboard') }}</el-breadcrumb-item>
                    <el-breadcrumb-item :to="{ path: '/settings' }">{{ $t('base.setting',2) }}</el-breadcrumb-item>
                    <el-breadcrumb-item>{{ $t('base.system')}}</el-breadcrumb-item>
                </el-breadcrumb>
            </div>
        </div>

        <el-row :gutter="20">
            <el-col :lg="6">
                <el-card class="shadow-sm sm:rounded-lg" body-class="p-2">
                    <div class="space-y-2">
                        <router-link to="/settings/system" exactActiveClass="text-gray-500 bg-slate-100"
                            class="flex items-center py-2 px-4 text-sm rounded hover:text-slate-700 dark:text-gray-400 dark:bg-gray-700 dark:hover:text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="me-3.5 w-5" width="24" height="24"
                                viewBox="0 0 24 24">
                                <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="1.5" color="currentColor">
                                    <path d="M15.5 12a3.5 3.5 0 1 1-7 0a3.5 3.5 0 0 1 7 0" />
                                    <path
                                        d="M20.79 9.152C21.598 10.542 22 11.237 22 12s-.403 1.458-1.21 2.848l-1.923 3.316c-.803 1.384-1.205 2.076-1.865 2.456s-1.462.38-3.065.38h-3.874c-1.603 0-2.405 0-3.065-.38s-1.062-1.072-1.865-2.456L3.21 14.848C2.403 13.458 2 12.763 2 12s.403-1.458 1.21-2.848l1.923-3.316C5.936 4.452 6.338 3.76 6.998 3.38S8.46 3 10.063 3h3.874c1.603 0 2.405 0 3.065.38s1.062 1.072 1.865 2.456z" />
                                </g>
                            </svg>
                            <span>{{ $t('base.general') }}</span>
                        </router-link>
                        <router-link to="/settings/system/email"
                            class="flex items-center py-2 px-4 text-sm rounded hover:text-slate-700 dark:text-gray-400 dark:bg-gray-700 dark:hover:text-gray-300"
                            exactActiveClass="text-gray-500 bg-slate-100">
                            <icon icon="hugeicons:mail-setting-01" class="me-3.5 w-5" width="24" height="24" />
                            <span>{{ $t('common.email') }}</span>
                        </router-link>
                    </div>
                </el-card>
            </el-col>
            <el-col :lg="18">
                <router-view @childinit="childInit"></router-view>
            </el-col>
        </el-row>
    </div>
</template>

<script setup>
import { Icon } from '@iconify/vue';
import { ref } from 'vue';

const title = ref('');

const childInit = (val) =>{
  title.value = val
}
</script>
