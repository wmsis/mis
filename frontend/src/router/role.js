//用户角色权限
const User = () => import('@/view/app/role/user/Index.vue');
const Role = () => import('@/view/app/role/role/Index.vue');
const Permission = () => import('@/view/app/role/permission/Index.vue');
const Orgnization = () => import('@/view/app/role/orgnization/Index.vue');
const API = () => import('@/view/app/role/api/Index.vue');

export default [
    {
        path: '/user',
        name: 'user',
        component: User,
        meta: {
            requireAuth: true
        }
    },
    {
        path: '/role',
        name: 'role',
        component: Role,
        meta: {
            requireAuth: true
        }
    },
    {
        path: '/permission',
        name: 'permission',
        component: Permission,
        meta: {
            requireAuth: true
        }
    },
    {
        path: '/orgnization',
        name: 'orgnization',
        component: Orgnization,
        meta: {
            requireAuth: true
        }
    },
    {
        path: '/api',
        name: 'api',
        component: API,
        meta: {
            requireAuth: true
        }
    }
]
