<template>
    <Modal
        v-model="shows.add"
        title="新增图文"
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
        props: ['shows'],
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
                        url: vm.$request.picTxt.store,
                        params: vm.formData,
                        success: function () {
                            vm.handleReset('formRef');
                            vm.$emit("listenChildClose");
                            vm.shows.add = false;
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
