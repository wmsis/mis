//系统设置
const AlarmLimit = () => import('@/view/app/setting/alarm/Index.vue');
const Module = () => import('@/view/app/setting/module/Index.vue');
const Group = () => import('@/view/app/setting/group/Index.vue');
const TagBind = () => import('@/view/app/setting/bindTag/Index.vue');

export default [
    {
        path: '/module-setting',
        name: 'module-setting',
        component: Module,
        meta: {
            requireAuth: true
        }
    },
    {
        path: '/group-manager',
        name: 'group-manager',
        component: Group,
        meta: {
            requireAuth: true
        }
    },
    {
        path: '/limit',
        name: 'limit',
        component: AlarmLimit,
        meta: {
            requireAuth: true
        }
    },
    {
        path: '/tagBind',
        name: 'tagBind',
        component: TagBind,
        meta: {
            requireAuth: true
        }
    }
]