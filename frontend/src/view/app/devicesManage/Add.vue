<template>
    <Modal
        v-model="shows.add"
        title="新增设备"
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
                    <FormItem prop="name" label="设备名称">
                        <Input v-model="formData.name" placeholder="请输入设备名称"></Input>
                    </FormItem>
                </Col>
                <Col span="2">
                    <div :style="{padding: '5px 5px 5px 5px'}"></div>
                </Col>
                <Col span="11" >
                    <FormItem prop="model" label="型号">
                        <Input v-model="formData.model" placeholder="请输入型号"></Input>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col span="11">
                    <FormItem prop="manufacturer" label="厂家">
                        <Input v-model="formData.manufacturer" placeholder="请输入厂家"></Input>
                    </FormItem>
                </Col>
                <Col span="2">
                    <div :style="{padding: '5px 5px 5px 5px'}"></div>
                </Col>
                <Col span="11">
                    <FormItem prop="serial_number" label="序列号">
                        <Input v-model="formData.serial_number" placeholder="请输入序列号"></Input>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col span="11">
                    <FormItem prop="production_date" label="投产日期">
                        <DatePicker v-model="formData.production_date" style="width: 100%;" type="date" placeholder="投产日期" clearable></DatePicker>
                    </FormItem>
                </Col>
                <Col span="2">
                    <div :style="{padding: '5px 5px 5px 5px'}"></div>
                </Col>
                <Col span="11">
                    <FormItem prop="status" label="设备状态">
                        <Select placeholder="设备状态" v-model="formData.status" filterable>
                            <Option value="active">启用</Option>
                            <Option value="storage">封存</Option>
                            <Option value="waste">报废</Option>
                        </Select>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col span="11">
                    <FormItem prop="charge_person_name" label="责任人姓名">
                        <Input v-model="formData.charge_person_name" placeholder="责任人姓名"></Input>
                    </FormItem>
                </Col>
                <Col span="2">
                    <div :style="{padding: '5px 5px 5px 5px'}"></div>
                </Col>
                <Col span="11">
                    <FormItem prop="charge_person_phone" label="责任人电话">
                        <Input v-model="formData.charge_person_phone" placeholder="责任人电话"></Input>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col span="11">
                    <FormItem prop="work_location" label="服役位置">
                        <Input v-model="formData.work_location" placeholder="服役位置"></Input>
                    </FormItem>
                </Col>
                <Col span="2">
                    <div :style="{padding: '5px 5px 5px 5px'}"></div>
                </Col>
                <Col span="11">

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
        DatePicker,
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
    Vue.component('DatePicker', DatePicker);
    Vue.component('Select', Select);
    Vue.component('Option', Option);
    import {unixtimefromat} from '@/utils/utils';

    export default {
        props: ['shows'],
        data () {
            return {
                model: true,
                width: '880px',
                loading: false,
                formData: {
                    name: '',
                    model: '',
                    manufacturer: '',
                    serial_number: '',
                    production_date: new Date(),
                    charge_person_name: '',
                    charge_person_phone: '',
                    status: 'active',
                    work_location: ''
                },
                ruleValidate: {
                    name: [
                        { required: true, message: '设备名称不能为空', trigger: 'blur' }
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
                    let params = JSON.parse(JSON.stringify(vm.formData));
                    params.production_date = unixtimefromat((new Date(params.production_date)).getTime()).date;
                    vm.loading = true;
                    vm.ajax({
                        method: 'POST',
                        url: vm.$request.equipments.equipmentAdd,
                        data: params,
                        success: function () {
                            vm.handleReset('formRef');
                            vm.$emit("listenChildClose");
                            vm.shows.add = false;
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
