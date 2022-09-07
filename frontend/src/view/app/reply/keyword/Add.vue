<template>
    <Modal
        v-model="shows.add"
        title="新增关键字"
        :width="width"
        :mask-closable="true"
        :footer-hide="true"
        @on-visible-change="visibleChange">
        <Form ref="formRef"
              :model="formData"
              :rules="ruleValidate"
              :label-width="100"
              label-position="left">
            <div class="container-keyword-reply-add">
                <div class="tips" v-if="formData.type == 'text'">
                    <p>可用变量：</p>
                    <p>[$name]：微信昵称</p>
                    <p>[$time]：时间</p>
                </div>

                <Row>
                    <Col span="24" >
                        <FormItem prop="keyword" label="名称">
                            <Input v-model="formData.keyword" placeholder="请输入关键字名称" size="large"></Input>
                        </FormItem>
                    </Col>
                </Row>
                <Row>
                    <Col span="24" >
                        <FormItem prop="type" label="类型">
                            <Select v-model="formData.type" class="sel" placeholder="请选择类型" size="large">
                                <Option v-for="item in typeList" :value="item.value" :key="item.value">{{ item.label }}
                                </Option>
                            </Select>
                        </FormItem>
                    </Col>
                </Row>
                <Row v-if="formData.type=='text'">
                    <Col span="24" >
                        <FormItem prop="text" label="文本">
                            <Input v-model="formData.text" :rows="5" show-word-limit type="textarea" placeholder="请输入文本..."/>
                        </FormItem>
                    </Col>
                </Row>
                <Row v-if="formData.type=='pic_txt'">
                    <Col span="24" >
                        <FormItem prop="pic_txt_id" label="图文">
                            <Select v-model="formData.pic_txt_id" class="sel" size="large">
                                <Option v-for="item in picTxtList" :value="item.id" :key="item.id">{{ item.name }}
                                </Option>
                            </Select>
                        </FormItem>
                    </Col>
                </Row>
                <Row v-if="formData.type=='img'">
                    <Col span="24" >
                        <FormItem prop="img" label="图片">
                            <image-upload ref="imgUploadRef"
                                          v-bind:uploadUrl="uploadUrl"
                                          v-bind:maxImageCount="1"
                                          v-on:listenChange="imgChange">
                            </image-upload>
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
    import { mapGetters, mapState } from 'vuex'
    import {
        Modal,
        Form,
        FormItem,
        Input,
        Button,
        Select,
        Option,
        Row,
        Col
    } from 'iview';

    Vue.component('Modal', Modal);
    Vue.component('Form', Form);
    Vue.component('Input', Input);
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
        props: ['shows'],
        components: {
            'image-upload': ImageUpload
        },
        data() {
            let checkImg = (rule, value, callback) => {
                if (this.formData.img.length == '') {
                    callback(new Error('图片不能为空'));
                    return;
                }
                callback();
            };
            return {
                model: true,
                width: '580px',
                formData: {
                    // 关键字回复
                    keyword: '',
                    // 消息类型
                    type: '',
                    // 回复类型
                    category: 'keyword',
                    // 文本内容
                    text: '',
                    // 图文图片
                    pic_txt_id: '',
                    // 图片路径
                    img: '',
                    // 间隔时间
                    interval_time: ''
                },
                ruleValidate: {
                    keyword: [
                        {required: true, message: '关键字名称不能为空', trigger: 'blur'}
                    ],
                    type: [
                        {required: true, message: '类型不能为空', trigger: 'change'}
                    ],
                    pic_txt_id: [
                        {required: true, message: '图文不能为空', trigger: 'change'}
                    ],
                    text: [
                        {required: true, message: '文本内容不能为空', trigger: 'blur'}
                    ],
                    img: [
                        {required: true, message: '图片不能为空', trigger: 'blur'},
                        {validator: checkImg, trigger: 'blur'}
                    ]
                },
                picTxtList: [],
                typeList: [
                    {
                        value: 'text',
                        label: '文本'
                    },
                    {
                        value: 'pic_txt',
                        label: '图文'
                    },
                    {
                        value: 'img',
                        label: '图片'
                    }
                ],
                uploadUrl: '',
            }
        },
        mounted: function() {
            this.uploadUrl = this.baseURL + '/wechat/upload?access_token=' + this.token.access_token;
            this.loadPicTxtList();
        },
        methods: {
            visibleChange: function (visible) {
                if (!visible) {
                    this.shows.add = false;
                }
                else{
                    this.formData.type = '';
                }
            },
            imgChange: function (value) {
                this.formData.img = value;
            },
            loadPicTxtList: function() {
                let vm = this;
                vm.ajax({
                    method: 'GET',
                    url: this.$request.picTxt.list,
                    success: function (data) {
                        vm.picTxtList = data;
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
                        url: vm.$request.keyword.store,
                        data: vm.formData,
                        success: function () {
                            vm.$emit("listenChildClose");
                            vm.shows.add = false;
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
    .container-keyword-reply-add {
        .tips {
            padding: 5px 20px;
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
