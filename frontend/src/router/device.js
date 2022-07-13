//设备管理
const DevsManage = () => import('@/view/app/devicesManage/Index.vue');
const DevRecords = () => import('@/view/app/devicesManage/Records.vue');
const DevParams = () => import('@/view/app/devicesManage/DevParams.vue');
const DefectStatistic = () => import('@/view/app/devicesManage/DefectStatistic.vue');

export default [
    {
        path: '/dev-manage',
        name: 'dev-manage',
        component: DevsManage,
        meta: {
            requireAuth: true
        }
    },
    {
        path: '/dev-records/:devid',
        name: 'dev-records',
        component: DevRecords,
        meta: {
            requireAuth: true
        }

    },
    {
        path: '/dev-params/:devid',
        name: 'dev-params',
        component: DevParams,
        meta: {
            requireAuth: true
        }
    },
    {
        path: '/defect-statistic',
        name: 'defect-statistic',
        component: DefectStatistic,
        meta: {
            requireAuth: true
        }
    },
]
