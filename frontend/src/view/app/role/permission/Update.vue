<template>
    <Drawer
        v-model="shows.update"
        title="修改权限"
        :width="width"
        :closable="false"
        :mask-closable="true"
        @on-visible-change="visibleChange">
        <Form ref="formRef"
              :model="formData"
              :rules="ruleValidate"
              :label-width="80">

            <Row>
                <Col span="24" >
                    <FormItem prop="name" label="权限名称">
                        <Input v-model="formData.name" placeholder="请输入权限名称"></Input>
                    </FormItem>
                </Col>
            </Row>
            <Row v-if="currentNode && currentNode.type == 'button'">
                <Col span="24">
                    <FormItem prop="api_name" label="授权标识">
                        <Input v-model="formData.api_name" placeholder="请输入授权标识"></Input>
                    </FormItem>
                </Col>
            </Row>
            <Row v-if="currentNode">
                <Col span="24">
                    <FormItem prop="page_url" label="跳转路径">
                        <Input v-model="formData.page_url" placeholder="请输入跳转路径"></Input>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col span="24" >
                    <FormItem prop="sort" label="排序号">
                        <InputNumber :max="100" :min="0" v-model="formData.sort" :style="{width: '100%'}"></InputNumber>
                    </FormItem>
                </Col>
            </Row>
            <Row v-if="currentNode">
                <Col span="24">
                    <FormItem prop="icon" label="菜单图标">
                        <Input v-model="formData.icon" placeholder="请输入菜单图标"></Input>
                    </FormItem>
                </Col>
            </Row>
            <Row v-if="currentNode">
                <Col span="24">
                    <FormItem prop="color" label="图标颜色">
                        <ColorPicker v-model="formData.color" />
                    </FormItem>
                </Col>
            </Row>

            <FormItem :style="{'text-align': 'right'}">
                <Button type="primary" @click="handleSubmit('formRef')" :loading="loading">提交</Button>
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
        Row,
        Col,
        Select,
        Radio,
        RadioGroup,
        Option,
        InputNumber,
        ColorPicker,
        Drawer
    } from 'iview';

    Vue.component('FormItem', FormItem);
    Vue.component('Input', Input);
    Vue.component('Button', Button);
    Vue.component('Form', Form);
    Vue.component('Modal', Modal);
    Vue.component('Row', Row);
    Vue.component('Col', Col);
    Vue.component('Select', Select);
    Vue.component('Radio', Radio);
    Vue.component('RadioGroup', RadioGroup);
    Vue.component('Option', Option);
    Vue.component('InputNumber', InputNumber);
    Vue.component('ColorPicker', ColorPicker);
    Vue.component('Drawer', Drawer);

    export default {
        props: ['shows', 'currentNode'],
        data () {
            return {
                model: true,
                width: '500',
                loading: false,
                formData: {
                    id: undefined,
                    type: 'menu',
                    name: '',
                    icon: '',
                    color: '#2d8cf0',
                    page_url: '',
                    api_name: '',
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
                            color: vm.currentNode.color ? vm.currentNode.color : '#2d8cf0',
                            page_url: vm.currentNode.page_url,
                            api_name: vm.currentNode.api_name,
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
                        parent_id: vm.currentNode.id,
                        sort: vm.formData.sort,
                        page_url: vm.formData.page_url,
                        api_name: vm.formData.api_name
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
