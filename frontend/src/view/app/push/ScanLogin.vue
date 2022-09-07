<template>
    <div class="scan-page">
        <div class="img" v-loading="loading">
            <img :src="qrcode_img" v-if="qrcode_img"/>
        </div>
        <div class="bind-info">
            <div class="row" v-if="member">
                <img :src="member.headimgurl"/>
                <div class="desc">{{member.nickname}}，您好，您已绑定微信</div>
            </div>
            <div class="row" v-if="!member">
                <div class="desc">扫码绑定微信</div>
            </div>
        </div>
    </div>
</template>

<script>
    import Vue from 'vue';
    import {
        Loading
    } from 'element-ui';

    Vue.use(Loading);

    export default {
        name: "ScanLogin",
        data(){
            return{
                qrcode_img: '',
                channel: '',
                loading: false,
                member: null
            }
        },
        methods:{
            loadQrcode(){
                const vm = this;
                vm.loading = true;
                vm.ajax({
                    method: 'GET',
                    url: this.$request.picTxt.qrcode,
                    params: {

                    },
                    success: function (data) {
                        vm.qrcode_img = data.qrcode;
                        vm.channel = data.channel;
                        vm.member = data.wechat;
                        vm.loading = false;
                        vm.ws();
                    },
                    fail(){
                        vm.loading = false;
                    }
                });
            },
            ws(){
                let that = this;
                if(window.Echo) {
                    window.Echo.channel('scan-login.' + that.channel)
                        .listen('WechatScanLogin', (e) => {
                            console.log('扫码登录');
                            console.log(e);
                            that.member = e.member;
                            that.showMessage('登录成功', 'success');
                            that.bindMember();
                        });
                }
            },
            bindMember(){
                const vm = this;
                vm.loading = true;
                vm.ajax({
                    method: 'POST',
                    url: this.$request.users.bindMember,
                    data: {
                        member_id: vm.member.id
                    },
                    success: function (data) {
                        vm.showMessage('绑定成功', 'success');
                        vm.loading = false;
                    },
                    fail(){
                        vm.showMessage('绑定失败', 'warning');
                        vm.loading = false;
                    }
                });
            },
        },
        mounted() {
            this.loadQrcode();
        }
    }
</script>

<style scoped lang="scss">
    @import '../../../assets/scss/base/mixins';
    @import '../../../assets/scss/base/placeholder';

    .scan-page{
        position: relative;
        padding: 15px;
        @extend %flex-center;
        & .img{
            background: white;
            width: 480px;
            height: 480px;
            @extend %flex-center;
            & img{
                width: 400px;
                height: 400px;
                display: block;
            }
        }
        & .bind-info{
            position: absolute;
            left: 0px;
            top: 30px;
            width: 100%;
            height: 50px;
            & .row{
                display: flex;
                flex-direction: row;
                justify-content: center;
                align-items: center;
                height: 100%;
                & img{
                    width: 50px;
                    height: 50px;
                    display: block;
                    margin-right: 20px;
                    border-radius: 3px;
                }
                & .desc{
                    color: #999999;
                    font-size: 16px;
                }
            }
        }
    }
</style>
