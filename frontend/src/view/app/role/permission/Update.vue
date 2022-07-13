<template>
    <Modal
        v-model="shows.update"
        title="新增权限"
        :width="width"
        :mask-closable="true"
        :footer-hide="true"
        @on-visible-change="visibleChange">
        <Form ref="formRef"
              :model="formData"
              :rules="ruleValidate"
              :label-width="80">

            <Row>
                <Col span="11" >
                    <FormItem prop="name" label="权限名称">
                        <Input v-model="formData.name" placeholder="请输入权限名称"></Input>
                    </FormItem>
                </Col>
                <Col span="2">
                    <div :style="{padding: '5px 5px 5px 5px'}"></div>
                </Col>
                <Col span="11" >
                    <FormItem prop="sort" label="排序">
                        <InputNumber :max="100" :min="0" v-model="formData.sort" :style="{width: '100%'}"></InputNumber>
                    </FormItem>
                </Col>
            </Row>

            <Row v-if="currentNode && currentNode.level == 3">
                <Col span="11">
                    <FormItem prop="api_url" label="接口地址">
                        <Input type="textarea" :rows="4" v-model="formData.api_url" placeholder="请输入接口地址（多个请换行）"></Input>
                    </FormItem>
                </Col>
                <Col span="2">
                    <div :style="{padding: '5px 5px 5px 5px'}"></div>
                </Col>
                <Col span="11">
                    <FormItem prop="api_name" label="接口名称">
                        <Input v-model="formData.api_name" placeholder="请输入接口名称"></Input>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col span="11" v-if="currentNode && currentNode.level == 2">
                    <FormItem prop="page_url" label="跳转路径">
                        <Input v-model="formData.page_url" placeholder="请输入跳转路径"></Input>
                    </FormItem>
                </Col>
                <Col span="11" v-if="currentNode && currentNode.level == 1">
                    <FormItem prop="icon" label="菜单图标">
                        <Input v-model="formData.icon" placeholder="请输入菜单图标"></Input>
                    </FormItem>
                </Col>
                <Col span="2" v-if="currentNode && currentNode.level == 1">
                    <div :style="{padding: '5px 5px 5px 5px'}"></div>
                </Col>
                <Col span="11" v-if="currentNode && currentNode.level == 1">
                    <FormItem prop="color" label="图标颜色">
                        <Input v-model="formData.color" placeholder="请输入图标颜色"></Input>
                    </FormItem>
                </Col>
            </Row>

            <FormItem :style="{'text-align': 'right'}">
                <Button type="primary" @click="handleSubmit('formRef')" :loading="loading">提交</Button>
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
        Option,
        InputNumber
    } from 'iview';

    Vue.component('FormItem', FormItem);
    Vue.component('Input', Input);
    Vue.component('Button', Button);
    Vue.component('Form', Form);
    Vue.component('Modal', Modal);
    Vue.component('Row', Row);
    Vue.component('Col', Col);
    Vue.component('Select', Select);
    Vue.component('Option', Option);
    Vue.component('InputNumber', InputNumber);

    export default {
        props: ['shows', 'currentNode'],
        data () {
            return {
                model: true,
                width: '880px',
                loading: false,
                formData: {
                    id: undefined,
                    type: 'menu',
                    name: '',
                    icon: '',
                    color: '',
                    page_url: '',
                    api_name: '',
                    api_url: '',
                    sort: 0
                },
                ruleValidate: {
                    type: [
                        { required: true, message: '权限类型不能为空', trigger: 'change' }
                    ],
                    name: [
                        { required: true, message: '权限名称不能为空', trigger: 'blur' }
                    ]
                }
            }
        },
        methods: {
            visibleChange: function(visible) {
                if (visible) {
                    let vm = this;
                    vm.$nextTick(() => {
                        this.formData = {
                            id: vm.currentNode.id,
                            type: vm.currentNode.type,
                            name: vm.currentNode.title,
                            icon: vm.currentNode.icon,
                            color: vm.currentNode.color,
                            page_url: vm.currentNode.page_url,
                            api_name: vm.currentNode.api_name,
                            api_url: vm.currentNode.api_url,
                            sort: vm.currentNode.sort
                        }
                    });
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
                    vm.loading = true;
                    const data = {
                        id: vm.formData.id,
                        type: vm.formData.type,
                        name: vm.formData.name,
                        icon: vm.formData.icon,
                        color: vm.formData.color,
                        parent_path: vm.currentNode && vm.currentNode.path,
                        sort: vm.formData.sort,
                        page_url: vm.formData.page_url,
                        api_name: vm.formData.api_name,
                        api_url: vm.formData.api_url,
                        is_root: !vm.currentNode || !vm.currentNode.id
                    };
                    vm.ajax({
                        method: 'POST',
                        url: vm.$request.permission.update,
                        data: data,
                        success: function () {
                            vm.handleReset('formRef');
                            vm.$emit("listenChildClose");
                            vm.shows.update = false;
                            vm.loading = false;
                        },
                        fail(){
                            vm.loading = false;
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
