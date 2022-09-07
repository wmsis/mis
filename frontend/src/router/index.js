import Vue from 'vue'
import Router from 'vue-router'
import store from '../store/'

const PersonalFrame = () => import('@/view/personal/Frame.vue');
const Signin = () => import('@/view/personal/Signin.vue');
const AppFrame = () => import('@/view/app/Frame.vue');

//首页
const Home = () => import('@/view/app/home/Index.vue');

import Wechat from './wechat'; //微信相关路由
import Role from './role'; //用户角色权限
import SystemSetting from './systemSetting'; //系统设置


Vue.use(Router);
const routes = [
    {
        path: '/app',
        component: AppFrame,
        children: [
            {
                path: '/home',
                name: 'home',
                component: Home,
                meta: {
                    requireAuth: true
                }
            },
            ...Wechat,
            ...Role,
            ...SystemSetting
        ]
    },
    {
        path: '/personal',
        component: PersonalFrame,
        children: [
            {
                path: '/signin',
                name: 'signin',
                component: Signin,
                meta: {
                    requireAuth: false
                }
            }
        ]
    },
    {
        path: '*',
        redirect: '/home'
    }
];

const router = new Router({
    //mode: 'history',
    scrollBehavior: () => ({y: 0}), // 滚动条滚动的行为，不加这个默认就会记忆原来滚动条的位置
    routes
});

router.beforeEach((to, from, next) => {
    //dispatch 异步操作 this.$store.dispatch('actions的方法'，arg)
    //commit 同步操作 this.$store.commit('mutations的方法'，arg)
    store.dispatch('showLoading');
    if (to.matched.some(r => r.meta.requireAuth)) {
        if (store.state.token) {
            next();
        } else {
            next({
                path: '/signin',
                query: {redirect: to.fullPath}
            })
        }
    } else {
        next();
    }
});

router.afterEach(function (to) {
    store.dispatch('hideLoading')
});

export default router;
