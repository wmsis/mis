<template>
    <Modal
            v-model="shows.group"
            title="TAG分组"
            :width="width"
            :mask-closable="false"
            :footer-hide="true"
            @on-visible-change="visibleChange">
        <Form ref="formRef"
              :model="formData"
              :rules="ruleValidate"
              v-loading="loading"
              :label-width="80">

            <FormItem prop="alias" label="请选择分组">
                <Select placeholder="请选择分组" v-model="formData.groupId">
                    <Option v-for="item in dataList" :value="item.id" :key="item.id">{{ item.name }}</Option>
                </Select>
            </FormItem>
            <FormItem>
                <Button type="primary" @click="handleSubmit('formRef')" :loading="submiting">提交</Button>
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
        props: ['shows', 'rows'],
        data () {
            return {
                model: true,
                width: '680px',
                dataList: [],
                loading: false,
                submiting: false,
                formData: {
                    groupId: ''
                },
                ruleValidate: {}
            }
        },
        methods: {
            visibleChange: function(visible) {
                if (visible) {
                    this.groups();
                } else {
                    this.shows.group = false;
                }
            },
            handleSubmit: function(name) {
                let vm = this;
                let ids = vm.rows.map((row) => row.id).join(',');
                if(!vm.submiting) {
                    vm.submiting = true;
                    vm.ajax({
                        method: 'POST',
                        url: vm.$request.setting.tagbindgroup,
                        params: {
                            tagIds: ids,
                            groupId: vm.formData.groupId
                        },
                        success: function () {
                            vm.showMessage('绑定成功', 'success');
                            vm.handleReset('formRef');
                            vm.$emit("listenChildClose");
                            vm.shows.group = false;
                            vm.submiting = false;
                        },
                        fail() {
                            vm.submiting = false;
                        }
                    });
                }
            },
            groups(){
                let vm = this;
                vm.loading = true;
                vm.ajax({
                    method: 'get',
                    params: {
                        num: 1000,
                        page: 1
                    },
                    url: vm.$request.group.index,
                    success: (data) => {
                        vm.dataList = data.data;
                        vm.loading = false;
                        if(vm.rows && vm.rows[0].tag_group_id){
                            vm.formData.groupId = vm.rows[0].tag_group_id;
                        }
                        else{
                            vm.formData.groupId = vm.dataList[0].id;
                        }
                    },
                    fail(){
                        vm.loading = false;
                    }
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
