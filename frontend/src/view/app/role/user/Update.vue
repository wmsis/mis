<template>
    <Drawer
        v-model="shows.update"
        title="修改用户"
        :width="width"
        :mask-closable="true"
        :closable="false"
        @on-visible-change="visibleChange">
        <Form ref="formRef"
              :model="formData"
              :rules="ruleValidate"
              :label-width="80">
            <Row>
                <Col span="24" >
                    <FormItem prop="type" label="权限类型">
                        <RadioGroup v-model="formData.type">
                            <Radio label="group"> 集团用户 </Radio>
                            <Radio label="webmaster"> 站长 </Radio>
                        </RadioGroup>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col span="24" >
                    <FormItem prop="name" label="用户姓名">
                        <Input v-model="formData.name" placeholder="请输入用户姓名"></Input>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col span="24" >
                    <FormItem prop="mobile" label="手机号码">
                        <Input v-model="formData.mobile" placeholder=""></Input>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col span="24">
                    <FormItem prop="email" label="邮箱地址">
                        <Input v-model="formData.email" placeholder=""></Input>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col span="24">
                    <FormItem prop="isopen" label="是否启用">
                        <RadioGroup v-model="formData.isopen">
                            <Radio label="1">是</Radio>
                            <Radio label="0">否</Radio>
                        </RadioGroup>
                    </FormItem>
                </Col>
            </Row>

            <FormItem :style="{'text-align': 'right'}">
                <Button type="primary" @click="handleSubmit('formRef')">提交</Button>
            </FormItem>
        </Form>
    </Drawer>
</template>

<script>
    import Vue from 'vue'
    import {
        Form,
        FormItem,
        Input,
        Button,
        Modal,
        Drawer,
        Row,
        Col,
        RadioGroup,
        Radio,
        Select,
        Option
    } from 'iview';

    Vue.component('Form', Form);
    Vue.component('Input', Input);
    Vue.component('FormItem', FormItem);
    Vue.component('Button', Button);
    Vue.component('Modal', Modal);
    Vue.component('Drawer', Drawer);
    Vue.component('Row', Row);
    Vue.component('Col', Col);
    Vue.component('RadioGroup', RadioGroup);
    Vue.component('Radio', Radio);
    Vue.component('Select', Select);
    Vue.component('Option', Option);
    Vue.component('RadioGroup', RadioGroup);
    Vue.component('Radio', Radio);

    export default {
        props: ['shows', 'user'],
        data () {
            return {
                model: true,
                width: '500',
                formData: {
                    name: '',
                    mobile: '',
                    email: '',
                    isopen: '1',
                    type: 'group'
                },
                ruleValidate: {
                    type: [
                        { required: true, message: '用户类型不能为空', trigger: 'blur' }
                    ],
                    name: [
                        { required: true, message: '用户姓名不能为空', trigger: 'blur' }
                    ],
                    mobile: [
                        { required: true, message: '手机号码不能为空', trigger: 'blur' }
                    ]
                }
            }
        },
        methods: {
            visibleChange: function(visible) {
                if (visible) {
                    this.formData.id = this.user.id;
                    this.formData.name = this.user.name;
                    this.formData.mobile = this.user.mobile;
                    this.formData.email = this.user.email;
                    this.formData.isopen = this.formData.isopen ? '1' : '0';
                    this.formData.type = this.user.type;
                } else {
                    this.shows.update = false;
                }
            },
            handleSubmit: function(name) {
                let vm = this;
                vm.$refs[name].validate((valid) => {
                    if (!valid) {
                        return;
                    }
                    vm.ajax({
                        method: 'POST',
                        url: vm.$request.users.store,
                        params: vm.formData,
                        success: function () {
                            vm.handleReset('formRef');
                            vm.$emit("listenChildClose");
                            vm.shows.update = false;
                        }
                    });
                });
            },
            handleReset (name) {
                this.$refs[name].resetFields();
            }
        }
    }
</script>

<style>

</style>
