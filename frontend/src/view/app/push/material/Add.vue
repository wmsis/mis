<template>
    <Modal
        v-model="shows.add"
        title="新增素材"
        :width="width"
        :mask-closable="true"
        :footer-hide="true"
        @on-visible-change="visibleChange">
        <Form ref="formRef"
              :model="formData"
              :rules="ruleValidate"
              :label-width="60"
              label-position="left">
            <div class="container-material-add">
                <div class="tips" v-if="formData.type=='text'">
                    <p>可用变量：</p>
                    <p>[$name]：微信昵称</p>
                    <p>[$time]：时间</p>
                    <p>[$province]：省份位置(打开公众号回复可用)</p>
                    <p>[$city]：城市位置(打开公众号回复可用)</p>
                    <p>[$district]：区县位置(打开公众号回复可用)</p>
                    <p>注意：地理位置变量仅打开公众号回复可用</p>
                </div>

                <Row>
                    <Col span="24" >
                        <FormItem prop="type" label="类型">
                            <Select v-model="formData.type" class="sel" size="large" placeholder="请选择类型">
                                <Option v-for="item in typeList" :value="item.value" :key="item.value">{{ item.label }}</Option>
                            </Select>
                        </FormItem>
                    </Col>
                </Row>
                <Row>
                    <Col span="24" >
                        <FormItem prop="title" label="标题">
                            <Input v-model="formData.title" size="large" placeholder="请输入角色姓名"></Input>
                        </FormItem>
                    </Col>
                </Row>
                <Row>
                    <Col span="24" >
                        <FormItem prop="description" label="描述">
                            <Input v-model="formData.description" size="large" placeholder="请输入描述消息"></Input>
                        </FormItem>
                    </Col>
                </Row>
                <Row>
                    <Col span="24" >
                        <FormItem prop="url" label="链接">
                            <Input v-model="formData.url" size="large" placeholder="请输入链接"></Input>
                        </FormItem>
                    </Col>
                </Row>

                <Row v-if="formData.type=='pictxt'">
                    <Col span="24" >
                        <FormItem prop="img" label="图文">
                            <image-upload ref="imgUploadRef"
                                          v-bind:uploadUrl="uploadUrl"
                                          v-bind:maxImageCount="1"
                                          v-on:listenChange="imgChange">
                            </image-upload>
                            ① 单图文封面：宽900像素，高383像素 <br/>② 多图文封面：头条宽900像素，高383像素，非头条宽200像素，高200像素
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
        Button,
        Select,
        Option,
        Row,
        Col,
        InputNumber
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
    Vue.component('InputNumber', InputNumber);

    import ImageUpload from '@/components/imageUpload'

    export default {
        computed: {
            ...mapState([
                'baseURL',
                'token'
            ])
        },
        props: ['shows', 'picTxtId'],
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
                width: '750',
                formData: {
                    // 图片
                    img: '',
                    // 标题
                    title: '',
                    // 描述
                    description: '',
                    // 链接
                    url: '',
                    // 排序
                    sort: 0,
                    // 素材类型
                    type: 'pictxt',
                    // 图文id
                    pic_txt_id: ''
                },
                ruleValidate: {
                    img: [
                        {required: true, message: '图文不能为空', trigger: 'blur'},
                        {validator: checkImg, trigger: 'blur'}
                    ],
                    title: [
                        {required: true, message: '标题不能为空', trigger: 'blur'}
                    ],
                    description: [
                        {required: true, message: '描述不能为空', trigger: 'blur'}
                    ],
                    url: [
                        {required: true, message: '链接不能为空', trigger: 'blur'}
                    ],
                    type: [
                        {required: true, message: '类型不能为空', trigger: 'change'}
                    ]
                },
                typeList: [
                    {
                        value: 'pictxt',
                        label: '图文'
                    }
                ],
                uploadUrl: ''
            }
        },
        methods: {
            visibleChange: function (visible) {
                if (!visible) {
                    this.shows.add = false;
                }
            },
            imgChange: function (value) {
                this.formData.img = value;
            },
            handleSubmit: function (name) {
                let vm = this;
                vm.formData.pic_txt_id = this.picTxtId;
                vm.$refs[name].validate((valid) => {
                    if (!valid) {
                        return;
                    }
                    vm.ajax({
                        method: 'POST',
                        url: vm.$request.material.store,
                        data: vm.formData,
                        success: function () {
                            vm.handleReset('formRef');
                            vm.$emit("listenChildClose");
                            vm.shows.add = false;
                            vm.showMessage('操作成功', 'success');
                        }
                    });
                });
            },
            handleReset(name) {
                this.$refs[name].resetFields();
                this.$refs.imgUploadRef.reset();
            }
        },
        mounted(){
            this.uploadUrl = this.baseURL + '/wechat/upload?access_token=' + this.token.access_token;
        }
    }
</script>

<style lang="scss">
    .container-material-add {
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
