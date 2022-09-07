<template>
    <Drawer
        v-model="shows.update"
        title="修改组织"
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
                    <FormItem prop="name" label="组织名称">
                        <Input v-model="formData.name" placeholder="请输入组织名称"></Input>
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
                width: '500',
                loading: false,
                formData: {
                    id: undefined,
                    name: '',
                    sort: 0
                },
                ruleValidate: {
                    name: [
                        { required: true, message: '组织名称不能为空', trigger: 'blur' }
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
                            name: vm.currentNode.title,
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
                        name: vm.formData.name,
                        parent_id: vm.currentNode.parent_id,
                        sort: vm.formData.sort,
                        level: vm.currentNode.level,
                        description: vm.currentNode.description
                    };
                    vm.ajax({
                        method: 'POST',
                        url: vm.$request.orgnization.store,
                        data: data,
                        success: function () {
                            vm.handleReset('formRef');
                            vm.$emit("listenChildClose");
                            vm.shows.update = false;
                            vm.loading = false;
                            vm.showMessage('操作成功', 'success');
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
