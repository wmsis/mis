<template>
    <Modal
        v-model="shows.add"
        title="新增用户"
        :width="width"
        :mask-closable="false"
        :footer-hide="true"
        @on-visible-change="visibleChange">
        <Form ref="formRef"
              :model="formData"
              :rules="ruleValidate"
              :label-width="80">
            <Row>
                <Col span="11" >
                    <FormItem prop="name" label="用户姓名">
                        <Input v-model="formData.name" placeholder="请输入用户姓名"></Input>
                    </FormItem>
                </Col>
                <Col span="2">
                    <div :style="{padding: '5px 5px 5px 5px'}"></div>
                </Col>
                <Col span="11" >
                    <FormItem prop="mobile" label="手机号码">
                        <Input v-model="formData.mobile" placeholder=""></Input>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col span="11">
                    <FormItem prop="email" label="邮箱地址">
                        <Input v-model="formData.email" placeholder=""></Input>
                    </FormItem>
                </Col>
                <Col span="2">
                    <div :style="{padding: '5px 5px 5px 5px'}"></div>
                </Col>
                <Col span="11">
                    <FormItem prop="isopen" label="是否启用">
                        <RadioGroup v-model="formData.isopen">
                            <Radio label="1">是</Radio>
                            <Radio label="0">否</Radio>
                        </RadioGroup>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col span="11">
                    <FormItem label="上班班次">
                        <Select placeholder="上班班次" v-model="formData.period">
                            <Option v-for="item in timeList" :value="item.value" :key="item.value">{{ item.name }}</Option>
                        </Select>
                    </FormItem>
                </Col>
            </Row>

            <FormItem :style="{'text-align': 'right'}">
                <Button type="primary" @click="handleSubmit('formRef')">提交</Button>
                <Button @click="handleReset('formRef')" style="margin-left: 8px">重置</Button>
            </FormItem>
        </Form>
    </Modal>
</template>

<script>
    import Vue from 'vue'
    import {
        Form,
        FormItem,
        Input,
        Button,
        Modal,
        Row,
        Col,
        Select,
        Option
    } from 'iview';

    Vue.component('Form', Form);
    Vue.component('Input', Input);
    Vue.component('FormItem', FormItem);
    Vue.component('Button', Button);
    Vue.component('Modal', Modal);
    Vue.component('Row', Row);
    Vue.component('Col', Col);
    Vue.component('Select', Select);
    Vue.component('Option', Option);

    export default {
        props: ['shows'],
        data () {
            return {
                model: true,
                width: '880px',
                timeList: [
                    {
                        value: 'first',
                        name: '甲班'
                    },
                    {
                        value: 'second',
                        name: '乙班'
                    },
                    {
                        value: 'third',
                        name: '丙班'
                    },
                    {
                        value: 'fouth',
                        name: '丁班'
                    }
                ],
                formData: {
                    name: '',
                    mobile: '',
                    email: '',
                    isopen: '1',
                    period: 'first'
                },
                ruleValidate: {
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
                if (!visible) {
                    this.shows.add = false;
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
                        url: vm.$request.admins.store,
                        data: vm.formData,
                        success: function () {
                            vm.handleReset('formRef');
                            vm.$emit("listenChildClose");
                            vm.shows.add = false;
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
