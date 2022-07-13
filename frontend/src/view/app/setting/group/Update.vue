<template>
    <Modal
        v-model="shows.update"
        title="修改分组"
        :width="width"
        :mask-closable="false"
        :footer-hide="true"
        @on-visible-change="visibleChange">
        <Form ref="formRef"
              :model="formData"
              :rules="ruleValidate"
              :label-width="80">
            <br/>
            <Row>
                <Col span="11" >
                    <FormItem prop="name" label="分组名称">
                        <Input v-model="formData.name" placeholder="请输入分组名称"></Input>
                    </FormItem>
                </Col>
                <Col span="2">
                    <div :style="{padding: '5px 5px 5px 5px'}"></div>
                </Col>
                <Col span="11" >
                    <FormItem prop="description" label="分组描述">
                        <Input v-model="formData.description" placeholder="请输入分组描述"></Input>
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
        RadioGroup,
        Radio
    } from 'iview';

    Vue.component('Form', Form);
    Vue.component('Input', Input);
    Vue.component('FormItem', FormItem);
    Vue.component('Button', Button);
    Vue.component('Modal', Modal);
    Vue.component('Row', Row);
    Vue.component('Col', Col);
    Vue.component('RadioGroup', RadioGroup);
    Vue.component('Radio', Radio);

    export default {
        props: ['shows', 'row'],
        data () {
            return {
                model: true,
                loading: false,
                width: '680px',
                formData: {
                    name: '',
                    description: ''
                },
                ruleValidate: {
                    name: [
                        { required: true, message: '模块名称不能为空', trigger: 'blur' }
                    ],
                    description: [
                        { required: true, message: '模块描述不能为空', trigger: 'blur' }
                    ]
                }
            }
        },
        methods: {
            visibleChange: function(visible) {
                if (visible) {
                    this.formData.id = this.row.id;
                    this.formData.name = this.row.name;
                    this.formData.description = this.row.description;
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
                    let url = vm.$request.group.update.replace("{id}", vm.row.id);
                    vm.ajax({
                        method: 'POST',
                        url: url,
                        params: vm.formData,
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
