//系统设置
const AlarmLimit = () => import('@/view/app/setting/alarm/Index.vue');

export default [
    {
        path: '/limit',
        name: 'limit',
        component: AlarmLimit,
        meta: {
            requireAuth: true
        }
    }
]
