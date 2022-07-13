<template>
    <Modal
            v-model="shows.assign"
            title="批量绑定Tag"
            :width="width"
            :mask-closable="false"
            :footer-hide="true"
            @on-visible-change="visibleChange">
        <Form ref="formRef"
              :model="formData"
              :rules="ruleValidate"
              :label-width="80">

            <FormItem prop="alias" label="请选择模块">
                <Select placeholder="请选择模块" v-model="formData.moduleId">
                    <Option v-for="item in moduleList" :value="item.id" :key="item.id">{{ item.name }}</Option>
                </Select>
            </FormItem>
            <FormItem>
                <Button type="primary" @click="handleSubmit('formRef')" :loading="loading">提交</Button>
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
        props: ['shows', 'moduleList', 'rows'],
        data () {
            return {
                model: true,
                loading: false,
                width: '680px',
                formData: {
                    moduleId: ''
                },
                ruleValidate: {}
            }
        },
        methods: {
            visibleChange: function(visible) {
                if (visible) {
                    this.formData.moduleId = this.moduleList[0].id;
                } else {
                    this.shows.assign = false;
                }
            },
            handleSubmit: function(name) {
                let vm = this;
                let ids = vm.rows.map((row) => row.id).join(',');
                if(!vm.loading) {
                    vm.loading = true;
                    vm.ajax({
                        method: 'POST',
                        url: vm.$request.setting.tagbindmodule,
                        params: {
                            tagIds: ids,
                            moduleId: vm.formData.moduleId
                        },
                        success: function () {
                            vm.showMessage('绑定成功', 'success');
                            vm.handleReset('formRef');
                            vm.$emit("listenChildClose");
                            vm.shows.assign = false;
                            vm.loading = false;
                        },
                        fail(){
                            vm.loading = false;
                        }
                    });
                }
            },
            handleReset (name) {
                this.$refs[name].resetFields();
            }
        }
    }
</script>

<style>

</style>
