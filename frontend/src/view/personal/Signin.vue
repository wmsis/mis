<template>
    <div id="login">
        <div class="siginin_login_outer"></div>
        <div class="siginin_login_bg">
            <div class="siginin_title_outer"></div>
            <div class="siginin_title_bg">
                <img src="../../assets/img/logo.png" style="border-radius: 50%;">
                <span class="siginin_title">{{sitename}}数据中台</span>
            </div>
            <form name="form">
                <input type="text" placeholder="请输入手机号码" class="siginin_name" v-model="mobile" required="">
                <i class="el-icon-mobile user"></i>
                <input type="password" placeholder="请输入密码" class="siginin_pwd" v-model="password" required="">
                <i class="el-icon-key key"></i>
                <Button type="primary" class="siginin_btn" :loading="loading" @click="signin">登  录</Button>
            </form>
        </div>
    </div>
</template>
<script>
    import Vue from 'vue'
    import { mapState } from 'vuex'
    import {
        Button
    } from 'iview'
    import { Configuration, OpenAIApi } from "openai";

    Vue.component('Button',Button);

    export default {
        computed: {
            ...mapState([
                'token',
                'sitename',
                'userInfo',
                'host'
            ])
        },
        data(){
            return {
                mobile: '17091952061',
                password: '123456',
                loading: false
            }
        },
        methods:{
            signin(){
                let that = this;
                if(!that.mobile){
                    that.showMessage('用户名不能为空');
                    return false;
                }

                if(!that.password){
                    that.showMessage('密码不能为空');
                    return false;
                }

                let data = {
                    mobile: that.mobile,
                    password: that.password
                };

                that.loading = true;
                that.ajax({
                    noauth: true,
                    method: 'POST',
                    url: that.$request.auth.signin,
                    data: data,
                    success(response) {
                        that.loading = false;
                        that.$store.dispatch('login', {
                            access_token: response.token,
                            refresh_token: '',
                            expiresIn: 3500
                        });

                        that.$store.dispatch('userInfo', {
                            userInfo: response.user
                        });
                        that.me();
                    },
                    fail(err){
                        that.loading = false;
                        that.showMessage(err.message,'error')
                    }
                });
            },
            me(){
                let that = this;
                that.loading = true;
                that.ajax({
                    method: 'GET',
                    url: that.$request.auth.me,
                    data: {},
                    success(response) {
                        that.loading = false;
                        let goto_url = '/home';
                        that.$store.dispatch('privileges', {
                            privileges: response.privileges
                        });

                        that.$router.push({
                            path: goto_url
                        });
                    },
                    fail(err){
                        that.loading = false;
                        that.showMessage(err.message,'error')
                    }
                });
            },
            test(){
                console.log('0000000000000');
                let that = this;
                let token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMC45OS4xMDAuOTY6NTA1MFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTY4NTQyNjQ4OCwiZXhwIjoxNjg1NDMwMDg4LCJuYmYiOjE2ODU0MjY0ODgsImp0aSI6IlpHUzlxd2czaWdFeXNBbHYiLCJzdWIiOjIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjciLCJyb2xlIjoidXNlciJ9.XerGK3rcN5Augq-jl1QJu2DK15FlZoioF1ne9TR37q8';
                that.$store.dispatch('login', {
                    access_token: token,
                    refresh_token: '',
                    expiresIn: 3500
                });
                that.ajax({
                    method: 'POST',
                    url: '/daily-data/import',
                    data: {
                        time : '2023-05',
                        orgnization_id: 49,
                        json: JSON.stringify({
                            'header':['cn_name', 'value'],
                            'results': [
                                {
                                    'cn_name': '属地生活垃圾量',
                                    'value': 62
                                }
                            ]
                        })
                    },
                    success(response) {
                        console.log('111111111111111111');
                        console.log(response);
                    },
                    fail(err){
                        that.showMessage(err.message,'error')
                    }
                });
            },

            async chat() {
                const configuration = new Configuration({
                    organization: "org-UNhBcTs57LdLHcnphv7TI0Cg",
                    apiKey: "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"
                });
                const openai = new OpenAIApi(configuration);
                const response = await openai.listEngines();
                console.log("000000000000000");
                console.log(response);
            }
        },
        mounted(){
            console.log("login")
            this.$store.dispatch('logout')
            this.test();
            //this.chat();
        }
    }
</script>
<style scoped>
    #login{
        height: 100%;
        width: 100%;
        background: url(../../assets/img/login_bg.jpg) no-repeat;
        background-size: 100% 100%;
        background-color: #fff;
    }
    .siginin_login_outer {
        background: #000;
        opacity: .2;
        filter: alpha(opacity=.2);
    }
    .siginin_login_bg, .siginin_login_outer {
        width: 430px;
        height: 320px;
        left: 50%;
        top: 50%;
        margin-top: -160px;
        margin-left: -215px;
        position: absolute;
        border: 1px solid #fff;
    }
    .siginin_title_outer {
        background: #000;
        opacity: .5;
        filter: alpha(opacity=.5);
    }
    .siginin_title_bg, .siginin_title_outer {
        height: 60px;
        line-height: 60px;
        width: 100%;
    }
    .siginin_title {
        color: white;
        font-size: 24px;
        margin-left: 12px;
        position: absolute;
        top: 0px;
        left:80px;
    }
    .siginin_login_bg form {
        position: relative;
    }
    input.siginin_name {
        margin-top: 45px;
        border: 1px solid #fff;
        border-radius: 3px;
        outline: none;
    }
    .siginin_btn, input.siginin_name, input.siginin_pwd,el-input.siginin_name {
        margin-left: 55px;
        width: 320px;
        height: 39px;
        color: #fff;
        outline: none;
    }
    input.siginin_name, input.siginin_pwd {
        display: block;
        margin-bottom: 20px;
        padding-left: 32px;
        background: 0 0;
        box-sizing: border-box;
        color: #fff;
        font-size: 14px;
        outline: none;
    }
    .siginin_btn {
        text-align: center;
        font-size: 18px;
        border-radius: 3px;
    }
    .siginin_btn /deep/ span{
        font-size: 18px;
    }
    input:focus:invalid, input[required]:invalid {
        box-shadow: none;
    }
    input.siginin_pwd {
        border: 1px solid #fff;
        border-radius: 3px;
    }
    .siginin_title_bg {
        position: absolute;
        left: 0;
        top: 0;
        padding-left: 25px;
        font-size: 20px;
        color: #fff;
        box-sizing: border-box;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .siginin_title_bg img {
        height: 45px;
        position: absolute;
        left:25px;
    }
    .siginin_login_bg input+i.user {
        position: absolute;
        left: 66px;
        top: 12px;
        color: #fff;
        font-size: 16px;
    }
    .siginin_login_bg input+i.key {
        position: absolute;
        left: 66px;
        top: 71px;
        color: #fff;
        font-size: 16px;
    }
    .siginin_login_bg :-moz-placeholder { /* Mozilla Firefox 4 to 18 */
        color: #fff;
    }
    .siginin_login_bg ::-moz-placeholder { /* Mozilla Firefox 19+ */
        color: #fff;
    }
    .siginin_login_bg input:-ms-input-placeholder{
        color: #fff;
    }
    .siginin_login_bg input::-webkit-input-placeholder {
        color: #fff;
    }
    .el-button{
        padding: 0px;
    }
</style>
