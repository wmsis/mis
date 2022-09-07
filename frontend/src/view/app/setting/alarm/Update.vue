<template>
    <Modal
            v-model="shows.update"
            title="修改上下限"
            :width="width"
            :mask-closable="false"
            :footer-hide="true"
            @on-visible-change="visibleChange">
        <Form ref="formRef"
              :model="formData"
              :rules="ruleValidate"
              v-loading="loading"
              :label-width="80">
            <Row>
                <Col span="11">
                    <FormItem prop="upper_limit" label="上限值">
                        <Input v-model="formData.upper_limit" placeholder="请输入上限值" number></Input>
                    </FormItem>
                </Col>
                <Col span="2">
                    <div :style="{padding: '5px 5px 5px 5px'}"></div>
                </Col>
                <Col span="11">
                    <FormItem prop="lower_limit" label="下限值">
                        <Input v-model="formData.lower_limit" placeholder="请输入下限值" number></Input>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col span="11">
                    <FormItem prop="alias" label="别名">
                        <Input v-model="formData.alias" placeholder="请输入别名"></Input>
                    </FormItem>
                </Col>
                <Col span="2">
                    <div :style="{padding: '5px 5px 5px 5px'}"></div>
                </Col>
                <Col span="11">
                    <FormItem prop="measure" label="单位">
                        <Input v-model="formData.measure" placeholder="请输入单位"></Input>
                    </FormItem>
                </Col>
            </Row>
            <FormItem :style="{'text-align': 'right'}">
                <Button type="primary" @click="handleSubmit('formRef')" :loading="submiting">提交</Button>
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
    import {
        Loading
    } from 'element-ui';

    Vue.component('Form', Form);
    Vue.component('Input', Input);
    Vue.component('FormItem', FormItem);
    Vue.component('Button', Button);
    Vue.component('Modal', Modal);
    Vue.component('Row', Row);
    Vue.component('Col', Col);
    Vue.component('Select', Select);
    Vue.component('Option', Option);
    Vue.use(Loading);

    export default {
        props: ['shows', 'limit'],
        data() {
            const upValidate = (rule, value, callback) => {
                if (value == null) {
                    callback(new Error('上限值不能为空'));
                } else if (typeof (value) != 'number') {
                    callback(new Error('上限值类型必须为数值'));
                }
                callback()
            };
            const lowerValidate = (rule, value, callback) => {
                if (value == null) {
                    callback(new Error('下限值不能为空'));
                } else if (typeof (value) != 'number') {
                    callback(new Error('下限值类型必须为数值'));
                }
                callback()
            };
            return {
                model: true,
                loading: false,
                submiting: false,
                width: '880px',
                formData: {
                    upper_limit: 0,
                    lower_limit: 0,
                    alias: '',
                    measure: '',
                },
                ruleValidate: {
                    upper_limit: [
                        {validator: upValidate, trigger: 'blur'}
                    ],
                    lower_limit: [
                        {validator: lowerValidate, trigger: 'blur'}
                    ]
                }
            }
        },
        methods: {
            visibleChange: function (visible) {
                if (visible) {
                    this.formData.upper_limit = this.limit.upper_limit;
                    this.formData.lower_limit = this.limit.lower_limit;
                    this.formData.alias = this.limit.alias ? this.limit.alias : "";
                    this.formData.measure = this.limit.measure ? this.limit.measure : "";
                } else {
                    this.shows.update = false;
                }
            },
            handleSubmit: function (name) {
                let vm = this;
                vm.$refs[name].validate((valid) => {
                    if (!valid) {
                        return;
                    }

                    vm.submiting = true;
                    vm.ajax({
                        method: 'POST',
                        url: vm.$request.setting.limitupdate.replace('{id}', this.limit.id),
                        params: vm.formData,
                        success: function () {
                            vm.handleReset('formRef');
                            vm.$emit("listenChildClose");
                            vm.shows.update = false;
                            vm.submiting = false;
                        },
                        fail(){
                            vm.submiting = false;
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

<style>

</style>
