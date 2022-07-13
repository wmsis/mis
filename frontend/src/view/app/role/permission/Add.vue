<template>
    <Modal
        v-model="shows.add"
        title="新增权限"
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
                    <FormItem prop="type" label="权限类型">
                        <Select v-model="formData.type" placeholder="请选择" clearable>
                            <Option value="root" v-if="!currentNode">一级菜单</Option>
                            <Option value="menu" v-if="currentNode && currentNode.level == 1">二级菜单</Option>
                            <Option value="button" v-if="currentNode && currentNode.level == 2">页面按钮</Option>
                            <Option value="look" v-if="currentNode && currentNode.level == 2">页面查看</Option>
                        </Select>
                    </FormItem>
                </Col>
                <Col span="2">
                    <div :style="{padding: '5px 5px 5px 5px'}"></div>
                </Col>
                <Col span="11" >
                    <FormItem prop="name" label="权限名称">
                        <Input v-model="formData.name" placeholder="请输入权限名称"></Input>
                    </FormItem>
                </Col>
            </Row>
            <Row v-if="currentNode && currentNode.level == 2">
                <Col span="11">
                    <FormItem prop="api_name" label="接口名称">
                        <Input v-model="formData.api_name" placeholder="请输入接口名称"></Input>
                    </FormItem>
                </Col>
                <Col span="2">
                    <div :style="{padding: '5px 5px 5px 5px'}"></div>
                </Col>
                <Col span="11">
                    <FormItem prop="api_url" label="接口地址">
                        <Input type="textarea" :rows="4" v-model="formData.api_url" placeholder="请输入接口地址（多个请换行）"></Input>
                    </FormItem>
                </Col>
            </Row>

            <Row>
                <Col span="11" >
                    <FormItem prop="sort" label="排序">
                        <InputNumber :max="100" :min="0" v-model="formData.sort" :style="{width: '100%'}"></InputNumber>
                    </FormItem>
                </Col>
                <Col span="2">
                    <div :style="{padding: '5px 5px 5px 5px'}"></div>
                </Col>
                <Col span="11" v-if="currentNode && currentNode.level == 1">
                    <FormItem prop="page_url" label="跳转路径">
                        <Input v-model="formData.page_url" placeholder="请输入跳转路径"></Input>
                    </FormItem>
                </Col>
                <Col span="11" v-if="!currentNode">
                    <FormItem prop="icon" label="菜单图标">
                        <Input v-model="formData.icon" placeholder="请输入菜单图标"></Input>
                    </FormItem>
                </Col>
            </Row>
            <Row v-if="!currentNode">
                <Col span="11">
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
                loading: false,
                width: '880px',
                formData: {
                    type: '',
                    name: '',
                    icon: '',
                    color: '',
                    page_url: '',
                    api_name: '',
                    api_url: '',
                    sort: 1
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
                    vm.loading = true;
                    const data = {
                        type: vm.formData.type,
                        name: vm.formData.name,
                        parent_path: vm.currentNode && vm.currentNode.path,
                        sort: vm.formData.sort,
                        icon: vm.formData.icon,
                        color: vm.formData.color,
                        page_url: vm.formData.page_url,
                        api_name: vm.formData.api_name,
                        api_url: vm.formData.api_url,
                        is_root: !vm.currentNode || !vm.currentNode.id
                    };
                    vm.ajax({
                        method: 'POST',
                        url: vm.$request.permission.insert,
                        data: data,
                        success: function () {
                            vm.handleReset('formRef');
                            vm.$emit("listenChildClose");
                            vm.shows.add = false;
                            vm.loading = false;
                        },
                        fail() {
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
