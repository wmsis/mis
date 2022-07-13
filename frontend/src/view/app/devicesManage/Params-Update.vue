<template>
    <Modal
        v-model="shows.update"
        title="修改设备参数"
        :width="width"
        :mask-closable="false"
        :footer-hide="true"
        @on-visible-change="visibleChange">
        <Form ref="formRef"
              :model="formData"
              :rules="ruleValidate"
              :label-width="80">
            <Row>
              <Col span="11">
                    <FormItem prop="name" label="参数名称">
                        <Input v-model="formData.name" placeholder=""></Input>
                    </FormItem>
                </Col>
                <Col span="2">
                    <div :style="{padding: '5px 5px 5px 5px'}"></div>
                </Col>
                <Col span="11">
                    <FormItem prop="value" label="参数值">
                        <Input v-model="formData.value" placeholder=""></Input>
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
        Col
    } from 'iview';

    Vue.component('Form', Form);
    Vue.component('Input', Input);
    Vue.component('FormItem', FormItem);
    Vue.component('Button', Button);
    Vue.component('Modal', Modal);
    Vue.component('Row', Row);
    Vue.component('Col', Col);

    export default {
        props: ['shows', 'dev'],
        data () {
            return {
                model: true,
                width: '880px',
                loading: false,
                formData: {
                    id:'',
                    name: '',
                    value: '',
                    equipment_id:''
                },
                ruleValidate: {
                    equipment_id: [
                        { required: true, message: '设备id不能为空', trigger: 'blur' }
                    ]
                }
            }
        },
        methods: {
            visibleChange: function(visible) {
                if (visible) {
                    this.formData.id=String(this.dev.id);
                    this.formData.name = this.dev.name;
                    this.formData.value = this.dev.value;
                    this.formData.equipment_id = String(this.dev.equipment_id);
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
                    vm.ajax({
                        method: 'POST',
                        url: vm.$request.equipments.equipmentParamUpdate.replace('{equipment_id}',vm.formData.equipment_id).replace('{id}',vm.formData.id),
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
            },
            dateTimeChange(datetime,type){
              this.formData.date=datetime;
            }
        }
    }
</script>

<style>

</style>
