//微信素材推送
const Material = () => import('@/view/app/push/material/Index.vue');
const PicTxtManager = () => import('@/view/app/push/pictxt/Index.vue');
const PicTxtQueue = () => import('@/view/app/push/PicTxtQueue.vue');
const PicTxtSend = () => import('@/view/app/push/PicTxtSend.vue');
const ScanLogin = () => import('@/view/app/push/ScanLogin.vue');

//微信自动回复
const Default = () => import('@/view/app/reply/Default.vue');
const DiyMenu = () => import('@/view/app/reply/menu/Index.vue');
const Keyword = () => import('@/view/app/reply/keyword/Index.vue');
const OpenWechat = () => import('@/view/app/reply/OpenWechat.vue');
const Subscribe = () => import('@/view/app/reply/Subscribe.vue');
const Member = () => import('@/view/app/member/Index.vue');

export default [
    {
        path: '/material',
        name: 'material',
        component: Material,
        meta: {
            requireAuth: true
        }
    },
    {
        path: '/pictxt-manager',
        name: 'pictxt-manager',
        component: PicTxtManager,
        meta: {
            requireAuth: true
        }
    },
    {
        path: '/pictxt-queue',
        name: 'pictxt-queue',
        component: PicTxtQueue,
        meta: {
            requireAuth: true
        }
    },
    {
        path: '/pictxt-send',
        name: 'pictxt-send',
        component: PicTxtSend,
        meta: {
            requireAuth: true
        }
    },
    {
        path: '/scan-login',
        name: 'scan-login',
        component: ScanLogin,
        meta: {
            requireAuth: true
        }
    },
    {
        path: '/default-reply',
        name: 'default-reply',
        component: Default,
        meta: {
            requireAuth: true
        }
    },
    {
        path: '/diy-menu',
        name: 'diy-menu',
        component: DiyMenu,
        meta: {
            requireAuth: true
        }
    },
    {
        path: '/keyword-reply',
        name: 'keyword-reply',
        component: Keyword,
        meta: {
            requireAuth: true
        }
    },
    {
        path: '/open-wechat',
        name: 'openw-echat',
        component: OpenWechat,
        meta: {
            requireAuth: true
        }
    },
    {
        path: '/subscribe-reply',
        name: 'subscribe-reply',
        component: Subscribe,
        meta: {
            requireAuth: true
        }
    },
    {
        path: '/members',
        name: 'members',
        component: Member,
        meta: {
            requireAuth: true
        }
    }
];