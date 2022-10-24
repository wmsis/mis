<template>
    <div class="view-content">
        <div :class="menuClass">
            <div class="header">{{sitename}}</div>
            <div class="main">
                <Menu ref="menuRef"
                      v-show="menuExpanded"
                      theme="dark"
                      width="auto"
                      :open-names="openNames"
                      :active-name="activeName"
                      :accordion="true"
                      @on-select="selectMenu">
                    <Submenu v-for="(menu, idx) in menus" :key="idx" :name="menu.id">
                        <template slot="title">
                            <Icon :type="menu.icon" :color="menu.color" size="18"></Icon>
                            {{menu.name}}
                        </template>
                        <menu-item v-for="(child, idx) in menu.children"
                                  :key="idx"
                                  :to="child.target"
                                  :name="child.target">
                            &nbsp;&nbsp;{{child.name}}
                        </menu-item>
                    </Submenu>
                </Menu>
                <div class="menu-toggle-icon" v-show="!menuExpanded">
                    <div class="icon-div" v-for="menu in menus" :key="menu.id">
                        <Tooltip :content="menu.name" placement="right">
                            <a @click="slideMenu(menu.id)">
                                <Icon :type="menu.icon" size="16"/>
                            </a>
                        </Tooltip>
                    </div>
                </div>
            </div>
        </div>
        <div :class="centerClass">
            <Header class="header" :slideIcon="slideIcon" :breadcrumbItems="breadcrumbItems" v-on:slideMenu="slideMenu"></Header>
            <div class="main">
                <div v-loading="loading" class="loading" v-if="false"></div>
                <div class="butterbar active" v-if="loading">
                    <span class="bar"></span>
                </div>
                <transition :name="transitionName">
                    <router-view class="child-view"></router-view>
                </transition>
            </div>
        </div>
        <audio :src="music" :autoplay="false" ref="music" class="music"></audio>
    </div>
</template>

<script>
    import Vue from 'vue'
    import { mapGetters, mapState } from 'vuex'
    import {
        Loading
    } from 'element-ui'

    import {
        Menu,
        Submenu,
        Icon,
        MenuItem,
        Tooltip
    } from 'iview';
    import Header from './Header.vue';
    import Echo from 'laravel-echo';
    import io from 'socket.io-client';
    import { md5 } from '@/utils/utils';

    Vue.use(Loading);
    Vue.component('Menu', Menu);
    Vue.component('Submenu', Submenu);
    Vue.component('MenuItem', MenuItem);
    Vue.component('Icon', Icon);
    Vue.component('Tooltip', Tooltip);

    window.io = io;

    export default {
        computed: {
            ...mapState([
                'userInfo',
                'sitename',
                'privileges',
                'token',
                'host'
            ]),
            ...mapGetters([
                'loading',
            ])
        },
        data () {
            return {
                transitionName: 'fade',
                menuExpanded: true,
                slideIcon: 'iconfont icon-shensuoyou',
                menuClass: 'menu menu-expand',
                centerClass: 'center center-expand',
                breadcrumbItems: [],
                menus: [],
                activeName: undefined,
                openNames: [],
                music: require('../../assets/audio/new_order.mp3')
            }
        },
        methods:{
            slideMenu: function (id) {
                if (typeof id == 'number') {
                    this.openNames = [id];
                    this.$nextTick(() => {
                        this.$refs.menuRef.updateOpened();
                    });
                }
                this.menuExpanded = !this.menuExpanded;
                if (this.menuExpanded) {
                    this.slideIcon = 'iconfont icon-shensuoyou';
                    this.menuClass = 'menu menu-expand';
                    this.centerClass = 'center center-expand';
                } else {
                    this.slideIcon = 'iconfont icon-shensuozuo';
                    this.menuClass = 'menu menu-toggle';
                    this.centerClass = 'center center-toggle';
                }
            },
            selectMenu: function (target) {

            },
            setCurrentMenu: function(toUpdate) {
                let target = this.$route.path;
                let targetRouteName = this.$route.name;
                //debugger
                let matches = false;
                for (let menu of this.menus) {
                    let children = menu.children;
                    if (!children) {
                        continue;
                    }

                    for (let child of children) {
                        if (child.target == target) {
                            this.openNames = [menu.id];
                            this.activeName = child.target;

                            this.breadcrumbItems = [];
                            this.breadcrumbItems.push({
                                name: menu.name,
                                icon: menu.icon
                            });
                            this.breadcrumbItems.push({
                                name: child.name
                            });
                            matches = true;
                            break;
                        }

                        if (matches) {
                            break;
                        }
                    }

                    if (matches) {
                        break;
                    }
                }

                if (!matches) {
                    this.openNames = [];
                    this.activeName = '/home';
                }

                if (toUpdate) {
                    this.$nextTick(() => {
                        this.$refs.menuRef.updateOpened();
                        this.$refs.menuRef.updateActiveName();
                    });
                }
            },
            loadMenus: function () {
                for(let item of this.privileges){
                    item.expand = true;
                }
                this.menus = this.privileges;
                this.setCurrentMenu(true);
            },
            ws(){
                let that = this;
                console.log('AAAAAAAAAAAAAAAAAAAAA');
                if(window.Echo) {
                    console.log('11111111111111111111');
                    window.Echo.channel('test-channel')
                        .listen('TaskFlowEvent', (e) => {
                            console.log('测试广播');
                            console.log(e);
                        });

                    let channel1 = 'user.' + that.userInfo.id;
                    window.Echo.private(channel1)
                        .listen('UserLoginEvent', (e) => {
                            console.log('用户登录');
                            console.log(e);
                            let key = e.key;
                            let local_key = md5(that.token.access_token);
                            if(local_key != key){
                                let clickCb = ()=>{
                                    if(that.notifyInstance.hasOwnProperty(key) && that.notifyInstance[key]){
                                        that.notifyInstance[key].close();
                                    }
                                };
                                let closeCb = ()=>{
                                    if(that.notifyInstance.hasOwnProperty(key) && that.notifyInstance[key]){
                                        delete that.notifyInstance[key];
                                    }
                                };
                                that.notifyInstance[key] = that.showNotification(
                                    '退出提示',
                                    '当前账号在其它地方登录，即将退出',
                                    'warning',
                                    1200,
                                    clickCb,
                                    closeCb
                                );

                                setTimeout(()=>{
                                    that.$store.dispatch('logout');
                                    that.$router.replace({
                                        name: 'signin',
                                        query: {redirect: that.$router.currentRoute.fullPath}
                                    });
                                }, 1200);
                            }
                        });

                    console.log('22222222222222222');
                    //监听广播通知
                    let channel2 = 'App.Models.User.' + that.userInfo.id;
                    console.log(channel2);
                    window.Echo.private(channel2)
                        .notification((notification) => {
                            console.log('广播通知');
                            console.log(notification);
                        });

                    let channel3 = 'alarm-notify.' + that.userInfo.id;
                    window.Echo.private(channel3)
                        .listen('AlarmNotifyEvent', (e) => {
                            that.$refs.music.play();
                            let timestamp = (new Date()).getTime();
                            let randomStr = Math.random();
                            let str = timestamp + '' + randomStr;
                            let key = md5(str);
                            console.log('报警了');
                            let title = e.tag.tag_name + '报警了';
                            let content = '报警上限值为：' + e.record.upper_limit + '，报警下限值为：' + e.record.lower_limit
                            let clickCb = ()=>{
                                if(that.notifyInstance.hasOwnProperty(key) && that.notifyInstance[key]){
                                    that.notifyInstance[key].close();
                                }

                                let path = '/alarm-statistic';
                                that.$router.push({
                                    path: path
                                });
                            };
                            let closeCb = ()=>{
                                if(that.notifyInstance.hasOwnProperty(key) && that.notifyInstance[key]){
                                    delete that.notifyInstance[key];
                                }
                            };
                            that.notifyInstance[key] = that.showNotification(
                                title,
                                content,
                                'warning',
                                10000,
                                clickCb,
                                closeCb
                            );
                        });
                }
            },
            echo(token){
                let that = this;
                window.Echo = new Echo({
                    auth: {
                        headers: {
                            Authorization: `Bearer ${token}`
                        }
                    },
                    broadcaster: 'socket.io',
                    host: that.host + ':6001'
                });

                that.ws();
            }
        },
        components:{
            Header
        },
        watch: {
            '$route' (to, from) {
                if(from.name == 'center'){
                    this.transitionName = 'fade';
                }
                else if(to.name == 'center'){
                    this.transitionName = 'fade';
                }
                else{
                    this.transitionName = 'fade';
                }
                this.setCurrentMenu(true);
            }
        },
        mounted: function() {
            let that = this;
            that.loadMenus();
            that.$nextTick(()=>{
                that.echo(this.token.access_token);
            });
        },
        destroyed() {
            if(window.Echo) {
                window.Echo.disconnect();
                window.Echo = null;
            }
        }
    }
</script>

<style scoped="scoped">
    .view-content{
        width:100%;
        height:100%;
        overflow: hidden;
        display: flex;
        flex-direction: row;
    }
    .music{
        opacity: 0;
    }
    .router-link-active{
        color:#f60;
        font-size: 0.6rem;
    }
    .child-view {
        position: absolute;
        width:100%;
        height:100%;
        left: 0;
        top:0;
        transition: all 300ms cubic-bezier(.55,0,.1,1);
        backface-visibility: hidden;
        z-index: 0;
        background-color: #f0eff4;
        overflow: hidden;
        padding: 15px;
        box-sizing: border-box;
    }
    .fade-enter-active, .fade-leave-active {
        transition: opacity .5s ease;
    }
    .fade-enter, .fade-leave-active {
        opacity: 0
    }
    .fadeup-enter-active {
        animation: fadeInUp .4s;
    }
    .fadeup-leave-active {
        animation: fadeInUp .4s reverse;
    }
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    @keyframes fadeOutUp {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translate3d(0, -30px, 0);
        }
    }
    .clear{
        clear: both;
    }
    .loading{
        z-index: 99;
        position: absolute;;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
    }
    .view-content {
        display: flex;
        flex-direction: row;
        width: 100%;
        height: 100%;
    }
    .view-content .menu {
        display: flex;
        flex-direction: column;
        background-color: #20222A;
        overflow-x: hidden;
        overflow-y: hidden;
        transition: width 0.3s;
        -moz-transition: width 0.3s;	/* Firefox 4 */
        -webkit-transition: width 0.3s;	/* Safari 和 Chrome */
        -o-transition: width 0.3s;
    }
    .view-content .menu-expand {
        width: 220px;
    }
    .view-content .menu-toggle {
        width: 60px;
    }
    .view-content .menu .header {
        text-align: center;
        height: 50px;
        line-height: 50px;
        font-size: 14px;
        color: rgba(255,255,255,.8)
    }
    .view-content .menu .main {
        height: calc(100vh - 50px);
        overflow-y: auto;
    }
    .view-content .menu .main::-webkit-scrollbar {
        width: 0px;
        height: 0px;
        background-color: #fff;
    }
    .view-content .menu .main::-webkit-scrollbar-track {
        background-color: #fff;
    }
    .view-content .menu .main::-webkit-scrollbar-thumb {
        background: #fff;
    }
    .view-content .menu .main .menu-toggle-icon .icon-div {
        height: 56px;
        line-height: 56px;
        text-align: center;
    }
    .view-content .menu .main .menu-toggle-icon a {
        color: rgba(255,255,255,.7);
        padding: 17px 22px;

    }
    .view-content .menu .main .menu-toggle-icon a:hover {
        color: white;
    }
    .view-content .center {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .view-content .center-expand {
        width: calc(100% - 220px);
    }
    .view-content .center-toggle {
        width: calc(100% - 60px);
    }
    .view-content .center .header {
         width: 100%;
         height: 50px;
         background-color: white;
         border-bottom: 1px solid #eee;
     }
    .view-content .center .main {
        height: calc(100% - 50px);
        overflow-y: hidden;
        background-color: white;
        position: relative;
    }
    .menu .main .ivu-menu-dark {
        background-color: #20222A;
    }
    .menu .main .ivu-menu-dark.ivu-menu-vertical .ivu-menu-opened {
        background-color: rgb(17,19,22);
    }
    .menu .main .ivu-menu-dark.ivu-menu-vertical .ivu-menu-opened /deep/ .ivu-menu-submenu-title {
        background-color: #20222A;
    }

    /* Moving bar */
    @-webkit-keyframes movingbar {
        0% {
            right: 50%;
            left: 50%;
        }
        99.9% {
            right: 0;
            left: 0;
        }
        100% {
            right: 50%;
            left: 50%;
        }
    }
    @-moz-keyframes movingbar {
        0% {
            right: 50%;
            left: 50%;
        }
        99.9% {
            right: 0;
            left: 0;
        }
        100% {
            right: 50%;
            left: 50%;
        }
    }
    @keyframes movingbar {
        0% {
            right: 50%;
            left: 50%;
        }
        99.9% {
            right: 0;
            left: 0;
        }
        100% {
            right: 50%;
            left: 50%;
        }
    }

    /* change bar */
    @-webkit-keyframes changebar {
        0% {
            background-color: #23b7e5;
        }
        33.3% {
            background-color: #23b7e5;
        }
        33.33% {
            background-color: #fad733;
        }
        66.6% {
            background-color: #fad733;
        }
        66.66% {
            background-color: #7266ba;
        }
        99.9% {
            background-color: #7266ba;
        }
    }
    @-moz-keyframes changebar {
        0% {
            background-color: #23b7e5;
        }
        33.3% {
            background-color: #23b7e5;
        }
        33.33% {
            background-color: #fad733;
        }
        66.6% {
            background-color: #fad733;
        }
        66.66% {
            background-color: #7266ba;
        }
        99.9% {
            background-color: #7266ba;
        }
    }
    @keyframes changebar {
        0% {
            background-color: #23b7e5;
        }
        33.3% {
            background-color: #23b7e5;
        }
        33.33% {
            background-color: #fad733;
        }
        66.6% {
            background-color: #fad733;
        }
        66.66% {
            background-color: #7266ba;
        }
        99.9% {
            background-color: #7266ba;
        }
    }
    .butterbar {
        position: relative;
        height: 3px;
        margin-bottom: -3px;
        z-index: 9999;
    }
    .butterbar.active {
        -webkit-animation: changebar 2.25s infinite 0.75s;
        -moz-animation: changebar 2.25s infinite 0.75s;
        animation: changebar 2.25s infinite 0.75s;
    }
    .butterbar .bar {
        position: absolute;
        width: 100%;
        height: 0;
        text-indent: -9999px;
        background-color: #23b7e5;
    }
    .butterbar.active .bar {
        -webkit-animation: changebar 2.25s infinite;
        -moz-animation: changebar 2.25s infinite;
        animation: changebar 2.25s infinite;
    }
    .butterbar .bar:before {
        position: absolute;
        right: 50%;
        left: 50%;
        height: 3px;
        background-color: inherit;
        content: "";
        box-sizing: border-box;
    }
    .butterbar.active .bar:before {
        -webkit-animation: movingbar 0.75s infinite;
        -moz-animation: movingbar 0.75s infinite;
        animation: movingbar 0.75s infinite;
        box-sizing: border-box;
    }
</style>
