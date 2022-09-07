<template>
    <Drawer
        v-model="shows.add"
        title="新增节点"
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
                    <FormItem prop="type" label="节点类型">
                        <RadioGroup v-model="formData.type">
                            <Radio label="parent" v-if="!currentNode"> 父节点 </Radio>
                            <Radio label="child" v-if="currentNode"> 子节点 </Radio>
                        </RadioGroup>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col span="24" >
                    <FormItem prop="name" label="节点名称">
                        <Input v-model="formData.name" placeholder="请输入节点名称"></Input>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col span="24" >
                    <FormItem prop="url" label="路径URL">
                        <Input v-model="formData.url" placeholder="请输入路径URL"></Input>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col span="24" >
                    <FormItem prop="sort" label="排序">
                        <InputNumber :max="100" :min="0" v-model="formData.sort" :style="{width: '100%'}"></InputNumber>
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
                loading: false,
                width: '500',
                formData: {
                    type: '',
                    name: '',
                    url: '',
                    sort: 1
                },
                ruleValidate: {
                    type: [
                        { required: true, message: '节点类型不能为空', trigger: 'change' }
                    ],
                    name: [
                        { required: true, message: '节点名称不能为空', trigger: 'blur' }
                    ]
                }
            }
        },
        methods: {
            visibleChange: function(visible) {
                if (!visible) {
                    this.shows.add = false;
                }
                else{
                    if(!this.currentNode){
                        this.formData.type = 'parent';
                    }
                    else{
                        this.formData.type = 'child';
                    }
                }
            },
            handleSubmit: function(name) {
                let vm = this;
                vm.$refs[name].validate((valid) => {
                    if (!valid) {
                        return;
                    }

                    let level, parent_id;
                    if(!vm.currentNode || !vm.currentNode.id){
                        level = 1;
                        parent_id = '';
                    }
                    else{
                        level = vm.currentNode.level + 1;
                        parent_id = vm.currentNode.id;
                    }

                    vm.loading = true;
                    const data = {
                        name: vm.formData.name,
                        parent_id: parent_id,
                        sort: vm.formData.sort,
                        url: vm.formData.url,
                        level: level,
                        description: ''
                    };
                    vm.ajax({
                        method: 'POST',
                        url: vm.$request.api.store,
                        data: data,
                        success: function () {
                            vm.handleReset('formRef');
                            vm.$emit("listenChildClose");
                            vm.shows.add = false;
                            vm.loading = false;
                            vm.showMessage('操作成功', 'success');
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
