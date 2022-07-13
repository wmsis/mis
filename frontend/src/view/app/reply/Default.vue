<template>
    <div class="diy-page">
        <div class="diy-content">
            <div class="type">
                <div class="title">类型：</div>
                <Select v-model="formData.type" class="select" size="large">
                    <Option v-for="item in typeList" :value="item.value" :key="item.value">{{ item.label }}</Option>
                </Select>
            </div>
            <div class="variable" v-if="formData.type=='text'">
                <p>可用变量：</p>
                <p>[$name]：微信昵称</p>
                <p>[$time]：时间</p>
                <p v-if="false">[$province]：省份位置(打开公众号回复可用)</p>
                <p v-if="false">[$city]：城市位置(打开公众号回复可用)</p>
                <p v-if="false">[$district]：区县位置(打开公众号回复可用)</p>
            </div>
            <div class="variable" v-if="formData.type=='text'">
                <p>可填入文本信息</p>
                <p>wap链接：&lt;a href="替换wap链接地址"&gt;商城首页点击这里哦~&lt;/a&gt;</p>
                <p>小程序链接：&lt;a href="替换wap链接地址（必填）" data-miniprogram-appid="替换小程序appid" data-miniprogram-path="替换小程序链接地址"&gt;个人中心在这里查看啦~&lt;/a&gt;</p>
            </div>
            <div class="demo">
                <div class="text" v-if="formData.type=='text'">
                    <div class="title">文本</div>
                    <div class="textarea">
                        <Input v-model="formData.text"
                               :rows="4"
                               show-word-limit
                               type="textarea"
                               placeholder="请输入内容"  />
                    </div>
                </div>

                <div class="type" v-if="formData.type=='pic_txt'">
                    <div class="title">图文</div>
                    <Select v-model="formData.pic_txt_id" size="large" class="select">
                        <Option v-for="item in picTxtList" :value="item.id" :key="item.id">{{ item.name }}
                        </Option>
                    </Select>
                </div>
            </div>
        </div>
        <div class="toolbar">
            <Button type="primary" icon="md-checkmark-circle-outline" @click="handleSubmit" v-if="isButtonExist('default-save')">提交</Button>
        </div>
        <Spin class="spin-loading" size="large" v-if="loading || submitLoading"></Spin>
    </div>
</template>
<script>
    import Vue from 'vue';
    import {
        Select,
        Option,
        Input,
        Button,
        Spin
    } from 'iview';
    import {checkToken} from '@/utils/checkToken';

    Vue.component('Select', Select);
    Vue.component('Option', Option);
    Vue.component('Input', Input);
    Vue.component('Button', Button);
    Vue.component('Spin', Spin);

    export default {
        computed: {
            loading() {
                if (this.detailLoading || this.pictxtLoading) {
                    return true;
                } else {
                    return false;
                }
            }
        },
        data() {
            return {
                detailLoading: false,
                pictxtLoading: false,
                submitLoading: false,
                typeList: [
                    {
                        value: 'text',
                        label: '文本'
                    },
                    {
                        value: 'pic_txt',
                        label: '图文'
                    }
                ],
                picTxtList: [],
                formData: {
                    category: "",
                    id: '',
                    img: null,
                    interval_time: null,
                    keyword: null,
                    pic_txt_id: '',
                    text: '',
                    type: ""
                }
            }
        },
        methods:{
            loadDetail: function () {
                const vm = this;
                vm.detailLoading = true;
                vm.ajax({
                    method: 'GET',
                    url: this.$request.autoreply.detail,
                    params: {
                        category: 'default'
                    },
                    success: function (data) {
                        vm.formData = data;
                        vm.detailLoading = false;
                    },
                    fail(){
                        vm.detailLoading = false;
                    }
                });
            },
            loadPicTxtList: function() {
                let vm = this;
                vm.pictxtLoading = true;
                vm.ajax({
                    method: 'GET',
                    url: this.$request.picTxt.list,
                    success: function (data) {
                        vm.picTxtList = data;
                        vm.pictxtLoading = false;
                    },
                    fail(){
                        vm.pictxtLoading = false;
                    }
                });
            },
            handleSubmit: function () {
                const vm = this;
                vm.submitLoading = true;
                vm.ajax({
                    method: 'POST',
                    url: this.$request.autoreply.store,
                    data: {
                        id: vm.formData.id,
                        category: vm.formData.category,
                        type: vm.formData.type,
                        text: vm.formData.text,
                        pic_txt_id: vm.formData.pic_txt_id
                    },
                    success: function () {
                        vm.submitLoading = false;
                        vm.showMessage('操作成功', 'success');
                    },
                    fail(){
                        vm.submitLoading = false;
                    }
                });
            }
        },
        mounted() {
            let that = this;
            checkToken(()=>{
                that.loadPicTxtList();
                that.loadDetail();
            });
        }
    }
</script>
<style scoped  lang="scss">
    @import '../../../assets/scss/base/mixins';
    @import '../../../assets/scss/base/placeholder';

    .diy-page {
        height: 100%;
        overflow: auto;
        position: relative;
        & .diy-content {
            height: calc(100vh - 120px);
            overflow-y: auto;
            & .type {
                @extend %flex-row;
                margin-bottom: 12px;
                & .title {
                    width: 60px;
                    height: 32px;
                    line-height: 32px;
                    font-weight: bold;
                }
                & .select {
                    width: 200px;
                    & .ivu-select-item {
                        padding: 10px 15px;
                    }
                }
            }
            & .variable, .link {
                background-color: #fff;
                font-size: 14px;
                margin-bottom: 12px;
                color: #606266;
                border-radius: 4px;
                border: 1px solid #ebeef5;
                transition: .3s;
                padding: 15px;
                & p {
                    line-height: 25px;
                    font-size: 14px;
                }
            }
            & .demo {
                margin-bottom: 20px;
                & .text {
                    @extend %flex-column;
                    & .title {
                        width: 60px;
                        font-weight: bold;
                        margin-bottom: 5px;
                    }
                }
                & .pic {
                    @extend %flex-row;
                    & .title {
                        width: 60px;
                        height: 32px;
                        line-height: 32px;
                        font-weight: bold;
                    }
                    & .select {
                        width: 200px;
                        & .ivu-select-item {
                            padding: 10px 15px;
                        }
                    }
                }
            }
        }
        & .toolbar {
            background-color: rgb(242,242,242);
            border: 1px solid rgb(232,232,232);
            height: 40px;
            text-align: center;
            padding-top: 3px;
            width: 100%;
        }
    }
</style>
