<template>
    <Modal
        v-model="shows.update"
        title="修改微信菜单"
        :width="width"
        :mask-closable="true"
        :footer-hide="true"
        @on-visible-change="visibleChange">
        <Form ref="formRef"
              :model="formData"
              :rules="ruleValidate"
              :label-width="100"
              label-position="left">
            <div class="container-wechat-menu-update">
                <Row>
                    <Col span="24" >
                        <FormItem prop="name" label="名称">
                            <Input v-model="formData.name" size="large" placeholder="请输入角色姓名"></Input>
                        </FormItem>
                    </Col>
                </Row>
                <Row>
                    <Col span="24" >
                        <FormItem prop="sort" label="排序">
                            <InputNumber :min="0"
                                         v-model="formData.sort"
                                         size="large"
                                         placeholder="请输入排序"
                                         :style="{'width': '100%'}" ></InputNumber>
                        </FormItem>
                    </Col>
                </Row>
                <Row>
                    <Col span="24" >
                        <FormItem prop="type" label="类型">
                            <Select v-model="formData.type" class="sel" size="large" placeholder="请选择类型">
                                <Option v-for="item in typeList" :value="item.value" :key="item.value">{{ item.name }}</Option>
                            </Select>
                        </FormItem>
                    </Col>
                </Row>
                <Row v-if="formData.type == 'click'">
                    <Col span="24" >
                        <FormItem prop="keyword" label="关键字">
                            <Select v-model="formData.keyword" class="sel" size="large" placeholder="请选择关键字">
                                <Option v-for="item in keywordList"
                                        :value="item.keyword"
                                        :key="item.id">{{ item.keyword }}</Option>
                                <Option  value="今日生产" key="today">今日生产</Option>
                                <Option  value="本月生产" key="month">本月生产</Option>
                            </Select>
                        </FormItem>
                    </Col>
                </Row>
                <Row v-if="formData.type == 'view' || formData.type == 'miniprogram'">
                    <Col span="24" >
                        <FormItem prop="url" label="链接">
                            <Input v-model="formData.url" size="large" placeholder="请输入链接"></Input>
                        </FormItem>
                    </Col>
                </Row>
                <Row v-if="formData.type == 'miniprogram'">
                    <Col span="24" >
                        <FormItem prop="pagepath" label="小程序链接">
                            <Input v-model="formData.pagepath" size="large" placeholder="请输入小程序链接"></Input>
                        </FormItem>
                    </Col>
                </Row>
                <Row v-if="formData.type == 'miniprogram'">
                    <Col span="24" >
                        <FormItem prop="appid" label="小程序appId">
                            <Input v-model="formData.appid" size="large" placeholder="请输入小程序appId"></Input>
                        </FormItem>
                    </Col>
                </Row>

            </div>

            <FormItem :style="{'text-align': 'right'}">
                <Button type="primary" @click="handleSubmit('formRef')">提交</Button>
            </FormItem>
        </Form>
    </Modal>
</template>

<script>
    import Vue from 'vue';
    import { mapGetters, mapState } from 'vuex';
    import {
        Modal,
        Form,
        FormItem,
        Input,
        InputNumber,
        Button,
        Select,
        Option,
        Row,
        Col
    } from 'iview';

    Vue.component('Modal', Modal);
    Vue.component('Form', Form);
    Vue.component('Input', Input);
    Vue.component('InputNumber', InputNumber);
    Vue.component('FormItem', FormItem);
    Vue.component('Button', Button);
    Vue.component('Select', Select);
    Vue.component('Option', Option);
    Vue.component('Row', Row);
    Vue.component('Col', Col);

    import ImageUpload from '@/components/imageUpload'

    export default {
        computed: {
            ...mapState([
                'baseURL',
                'token'
            ])
        },
        props: ['shows', 'wechatMenu'],
        components: {
            'image-upload': ImageUpload
        },
        data() {
            let checkImg = (rule, value, callback) => {
                if (this.formData.img.length == '') {
                    callback(new Error('图文不能为空'));
                    return;
                }
                callback();
            };
            return {
                model: true,
                width: '580px',
                formData: {
                    // 主键
                    id: '',
                    // 菜单节点名称
                    name: '',
                    // 排序
                    sort: 0,
                    // 菜单类型
                    type: '',
                    // 关键字
                    keyword: '',
                    // 小程序appid
                    appid: '',
                    // 小程序路径
                    pagepath: '',
                    // 菜单链接
                    url: '',
                    // 是否开启
                    is_open: true,
                    // 是否根节点
                    is_root: false
                },
                ruleValidate: {
                    name: [
                        {required: true, message: '名称不能为空', trigger: 'blur'}
                    ],
                    sort: [
                        {required: true, type: 'number', message: '排序不能为空', trigger: 'blur'}
                    ],
                    type: [
                        {required: true, message: '类型不能为空', trigger: 'change'}
                    ],
                    parent_path: [
                        {required: true, message: '级别不能为空', trigger: 'change'}
                    ]
                },
                typeList: [
                    {
                        value: 'pictxt',
                        label: '图文'
                    }
                ],
                keywordList: [],
                topMenus: [{
                    path: '',
                    name: '最顶级'
                }],
                typeList: [{
                    name: '链接',
                    value: 'view'
                }, {
                    name: '关键字',
                    value: 'click'
                }, {
                    name: '小程序链接',
                    value: 'miniprogram',
                }],
                uploadUrl: '',
            }
        },
        mounted: function() {
            this.uploadUrl = this.baseURL + '/wechat/upload?access_token=' + this.token.access_token;
            this.loadTopMenus();
            this.loadKeywords();
        },
        methods: {
            visibleChange: function (visible) {
                if (visible) {
                    this.formData.id = this.wechatMenu.id;
                    this.formData.name = this.wechatMenu.name;

                    this.formData.sort = this.wechatMenu.sort;
                    this.formData.type = this.wechatMenu.type;
                    this.formData.keyword = this.wechatMenu.keyword;
                    this.formData.appid = this.wechatMenu.appid;
                    this.formData.pagepath = this.wechatMenu.pagepath;
                    this.formData.url = this.wechatMenu.url;
                    this.formData.is_open = this.wechatMenu.is_open;
                    this.formData.is_root = this.wechatMenu.is_root;
                } else {
                    this.handleReset('formRef');
                    this.shows.update = false;
                }
            },
            loadTopMenus: function() {
                const vm = this;
                vm.ajax({
                    method: 'GET',
                    url: this.$request.wechatMenu.list,
                    success: function (data) {
                        for (let item of data) {
                            if (item.is_root) {
                                vm.topMenus.push(item);
                            }
                        }
                    }
                });
            },
            loadKeywords: function() {
                const vm = this;
                vm.ajax({
                    method: 'GET',
                    url: this.$request.keyword.list,
                    success: function (data) {
                        vm.keywordList = data;
                    }
                });
            },
            handleSubmit: function (name) {
                let vm = this;
                vm.$refs[name].validate((valid) => {
                    if (!valid) {
                        return;
                    }
                    vm.ajax({
                        method: 'POST',
                        url: vm.$request.wechatMenu.update,
                        data: vm.formData,
                        success: function () {
                            vm.handleReset('formRef');
                            vm.$emit("listenChildClose");
                            vm.shows.update = false;
                            vm.showMessage('操作成功', 'success');
                        }
                    });
                });
            },
            handleReset(name) {
                this.$refs[name].resetFields();
            }
        }
    }
</script>

<style lang="scss">
    .container-wechat-menu-update {
        .tips {
            paddging: 5px 20px;
            background-color: #f5f5f5;
            border: 1px solid #e3e3e3;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 14px;
            line-height: 5px;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .05);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, .05);
            & p {
                height: 22px;
                line-height: 22px;
            }
        }
    }
</style>
