<template>
    <div class="page">
        <!--侧边伸缩-->
        <div class="left">
            <div class="icon-button" @click="switchMenu">
                <a title="侧边伸缩">
                    <Icon :custom="slideIcon" size="16" />
                </a>
            </div>
        </div>

        <!--修改密码-->
        <modify-password :shows="shows"></modify-password>

        <!--面包屑-->
        <div class="middle">
            <Breadcrumb>
                <BreadcrumbItem to="/home" class="breadcrumbItem">
                    <Icon type="ios-home" size="18"></Icon> 首页
                </BreadcrumbItem>
                <BreadcrumbItem v-for="(item, idx) in breadcrumbItems" :key="idx" class="breadcrumbItem" :to="item.target">
                    <Icon v-if="item.icon" size="18" :type="item.icon"></Icon> {{item.name}}
                </BreadcrumbItem>
            </Breadcrumb>
        </div>

        <!--当前登录-->
        <div class="right">
            <Dropdown @on-click="dropdownClick">
                <a>
                    <Avatar :src="header_img" class="u-avatar"/>
                    <span style="font-size: 13px; color: #333;">{{userInfo && userInfo.name ? userInfo.name : ''}}</span>
                    <Icon type="md-arrow-dropdown" size="18" style="color: #333"/>
                </a>
                <DropdownMenu slot="list" style="text-align: left;">
                    <DropdownItem name="basicMessage">基本资料</DropdownItem>
                    <DropdownItem name="modifyPassword">修改密码</DropdownItem>
                    <DropdownItem name="logout" divided>退出</DropdownItem>
                </DropdownMenu>
            </Dropdown>
        </div>
    </div>
</template>
<script>
    import Vue from 'vue';
    import { mapGetters, mapState } from 'vuex';
    import {
        Icon,
        Breadcrumb,
        BreadcrumbItem,
        Dropdown,
        DropdownMenu,
        DropdownItem,
        Avatar
    } from 'iview';
    import ModifyPassword from '@/view/app/home/ModifyPassword';

    Vue.component('Icon', Icon);
    Vue.component('Breadcrumb', Breadcrumb);
    Vue.component('BreadcrumbItem', BreadcrumbItem);
    Vue.component('Dropdown', Dropdown);
    Vue.component('DropdownMenu', DropdownMenu);
    Vue.component('DropdownItem', DropdownItem);
    Vue.component('Avatar', Avatar);

    export default {
        computed: {
            ...mapState([
                'userInfo'
            ])
        },
        props:{
            slideIcon: {
                type: String
            },
            breadcrumbItems: {
                type: Array
            },
            slideMenu: {
                type: Function
            }
        },
        components: {
            'modify-password': ModifyPassword
        },
        data() {
            return {
                header_img: require('../../assets/img/logo.png'),
                shows: {
                    modifyPassword: false
                }
            }
        },
        methods: {
            switchMenu(){
                //调用父组件slideMenu方法
                this.$emit("slideMenu", '');
            },
            dropdownClick: function (method) {
                if (this[method]) {
                    this[method]();
                }
            },
            basicMessage(){
                this.showMessage('正在路上了……', 'success');
            },
            modifyPassword(){
                this.shows.modifyPassword = true;
            },
            logout(){
                let that = this;
                that.showMessageBox('confirm', '退出登录', '确定要退出登录吗？', ()=>{
                    that.ajax({
                        method: 'GET',
                        url: that.$request.auth.logout,
                        success: function () {
                            that.$store.dispatch('logout');
                            that.$router.push('signin');
                        }
                    });
                });
            }
        },
        mounted() {

        }
    }
</script>
<style scoped lang="scss">
    .page {
        width: 100%;
        height: 100%;
        background-color: white;
        border-bottom: 1px solid #eee;
        display: flex;
        padding: 0px 15px;
        align-items: center;
        justify-content: space-between;
    }
    .page .left {
    }
    .page .middle {
        & i{
            position: relative;
            top: -1px;
        }
    }
    .page .right {
        flex: 1;
        text-align: right;
        margin-right: 15px;
    }
    .page .icon-button {
        padding: 0 10px;
        margin-right: 30px;
    }
    .page .icon-button a {
        color: #333;
        padding: 17px 0px;
    }
    .page .breadcrumbItem{
        font-size: 16px;
    }
    .page .u-avatar{
        margin-bottom: 3px; width: 30px; height: 30px
    }
</style>
