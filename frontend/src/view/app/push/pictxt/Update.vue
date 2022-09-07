<template>
    <Modal
        v-model="shows.update"
        title="修改图文"
        :width="width"
        :mask-closable="true"
        :footer-hide="true"
        @on-visible-change="visibleChange">
        <Form ref="formRef"
              :model="formData"
              :rules="ruleValidate"
              :label-width="0">
            <Row>
                <Col span="24" >
                    <FormItem prop="name" label="">
                        <Input v-model="formData.name" placeholder="请输入图文名称" size="large"></Input>
                    </FormItem>
                </Col>
            </Row>

            <FormItem :style="{'text-align': 'right'}">
                <Button type="primary" @click="handleSubmit('formRef')">提交</Button>
            </FormItem>
        </Form>
    </Modal>
</template>

<script>
    import Vue from 'vue';
    import {
        Modal,
        Form,
        FormItem,
        Input,
        Button,
        Row,
        Col
    } from 'iview';

    Vue.component('Modal', Modal);
    Vue.component('Form', Form);
    Vue.component('Input', Input);
    Vue.component('FormItem', FormItem);
    Vue.component('Button', Button);
    Vue.component('Row', Row);
    Vue.component('Col', Col);

    export default {
        props: ['shows', 'picTxt'],
        data () {
            return {
                model: true,
                width: '580px',
                formData: {
                    name: '',
                    desc: ''
                },
                ruleValidate: {
                    name: [
                        { required: true, message: '图文名称不能为空', trigger: 'blur' }
                    ]
                }
            }
        },
        methods: {
            visibleChange: function(visible) {
                if (visible) {
                    this.formData.id = this.picTxt.id;
                    this.formData.name = this.picTxt.name;
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
                        url: vm.$request.picTxt.store,
                        params: vm.formData,
                        success: function () {
                            vm.handleReset('formRef');
                            vm.$emit("listenChildClose");
                            vm.shows.update = false;
                            vm.showMessage('操作成功', 'success');
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

<style type="lang">
</style>
